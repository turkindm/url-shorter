<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Services\Link\Dto\CreateLinkDto;
use App\Services\Link\Dto\UpdateLinkDto;
use App\Services\Link\LinkService;
use App\Services\Link\UnreachableResourceException;
use App\Services\Link\WebResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinkController extends Controller
{
    public function __construct(private LinkService $service)
    {
    }

    public function view(Request $request, string $hashId): RedirectResponse
    {
        /** @var Link $link */
        $link = Link::query()->byHashId($hashId)->first();
        if ($link === null) {
            throw new NotFoundHttpException('Link not found.');
        }

        $this->service->view($link, $request->header('user-agent'), $request->ip());

        return redirect()->away($link->long_url);
    }

    public function index(): AnonymousResourceCollection
    {
        $links = QueryBuilder::for(Link::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('tag', 'tags.name')
            ])
            ->paginate();

        return LinkResource::collection($links);
    }

    public function show(int $id): LinkResource
    {
        return new LinkResource($this->findLink($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(int $id, Request $request): LinkResource
    {
        $validated = $request->validate([
            'long_url' => 'sometimes|url|max:255',
            'title' => 'sometimes|string|max:255',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:255',
        ]);

        $link = $this->findLink($id);
        $dto = new UpdateLinkDto(
            longUrl: $validated['long_url'] ?? null,
            title: $validated['title'] ?? null,
            tags: $validated['tags'] ?? null
        );

        try {
            $this->service->update($link, $dto);
        } catch (UnreachableResourceException) {
            throw ValidationException::withMessages(['long_url' => 'Resource is unreachable.']);
        }

        return new LinkResource($link);
    }

    public function destroy(int $id): Response
    {
        $this->findLink($id)->delete();

        return response()->noContent();
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResource
    {
        if (array_is_list($request->all())) {
            return $this->storeMultiple($request);
        }

        return $this->storeSingle($request);
    }

    /**
     * @throws ValidationException
     */
    private function storeSingle(Request $request): LinkResource
    {
        $validated = $request->validate([
            'long_url' => 'required|url|max:255',
            'title' => 'sometimes|string|max:255',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:255',
        ]);

        $resource = new WebResource($validated['long_url']);
        if (!$resource->isAvailable()) {
            throw ValidationException::withMessages(['long_url' => 'Resource is unreachable.']);
        }

        $dto = new CreateLinkDto(
            longUrl: $validated['long_url'],
            title: $validated['title'] ?? $resource->getTitle(),
            tags: $validated['tags'] ?? [],
        );

        return new LinkResource($this->service->create($dto));
    }

    /**
     * @throws ValidationException
     */
    private function storeMultiple(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            '*.long_url' => 'required|url|max:255',
            '*.title' => 'sometimes|string|max:255',
            '*.tags' => 'sometimes|array',
            '*.tags.*' => 'string|max:255',
        ]);

        $resources = [];
        $fails = [];
        foreach ($validated as $i => $item) {
            $resource = new WebResource($item['long_url']);
            if (!$resource->isAvailable()) {
                $fails["{$i}.long_url"] = 'Resource is unreachable.';
            }
            $resources[] = $resource;
        }

        if (!empty($fails)) {
            throw ValidationException::withMessages($fails);
        }

        $links = [];
        foreach ($validated as $i => $item) {
            $dto = new CreateLinkDto(
                longUrl: $item['long_url'],
                title: $item['title'] ?? $resources[$i]->getTitle(),
                tags: $item['tags'] ?? [],
            );
            $links[] = $this->service->create($dto);
        }

        return LinkResource::collection($links);
    }

    private function findLink(int $id): Link
    {
        /** @var Link $link */
        $link = Link::query()->with('tags')->find($id);
        if ($link === null) {
            throw new NotFoundHttpException('Link not found.');
        }

        return $link;
    }
}

<?php

namespace App\Services\Link;

use App\Models\Link;
use App\Services\Link\Dto\CreateLinkDto;
use App\Services\Link\Dto\UpdateLinkDto;
use Hashids\Hashids;

class LinkService
{
    public function create(CreateLinkDto $dto): Link
    {
        $link = new Link();
        $link->title = $dto->title;
        $link->long_url = $dto->longUrl;
        $link->save();

        $link->syncTags($dto->tags);

        $link->hash_id = (new Hashids())->encode($link->id);
        $link->save();

        return $link;
    }

    /**
     * @throws UnreachableResourceException
     */
    public function update(Link $link, UpdateLinkDto $dto): Link
    {
        $title = $dto->title;

        if ($dto->longUrl !== null && $link->long_url !== $dto->longUrl) {
            $resource = new WebResource($dto->longUrl);
            $resource->assertAvailable();

            $link->long_url = $dto->longUrl;
            $title ??= $resource->getTitle();
        }

        if ($title !== null) {
            $link->title = $title;
        }

        if ($link->isDirty()) {
            $link->save();
        }

        if ($dto->tags !== null) {
            $link->syncTags($dto->tags);
        }

        return $link;
    }

    public function view(Link $link, string $userAgent, string $userIp): void
    {
        $link->views()->create([
            'user_agent' => $userAgent,
            'user_ip' => $userIp,
        ]);
    }
}

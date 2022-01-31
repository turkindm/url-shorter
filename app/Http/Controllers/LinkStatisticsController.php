<?php

namespace App\Http\Controllers;

use App\Services\Link\LinkStatisticService;
use Illuminate\Http\JsonResponse;

class LinkStatisticsController
{
    public function __construct(private LinkStatisticService $service)
    {
    }

    public function showOverall(): JsonResponse
    {
        return response()->json($this->service->getOverall());
    }

    public function showForLink(int $linkId): JsonResponse
    {
        return response()->json($this->service->getDailyForLink($linkId));
    }
}

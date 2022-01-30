<?php

namespace App\Services\Link;

use App\Models\Link;
use Illuminate\Support\Facades\DB;

class LinkStatisticService
{
    public function getDailyForLink(int $linkId): array
    {
        return DB::table('views')
            ->select(
                DB::raw('DATE(created_at) as date, count(*) as total_views, count(distinct (user_agent, user_ip)) as unique_views')
            )->where('link_id', $linkId)
            ->orderBy('date', 'desc')
            ->groupBy('date')
            ->get()
            ->all();
    }

    public function getOverall(): array
    {
        return DB::table('views')
            ->join('links', 'views.link_id', '=', 'links.id')
            ->select(
                DB::raw('id, count(*) as total_views, count(distinct (user_agent, user_ip)) as unique_views')
            )->where('links.deleted_at', null)
            ->orderBy('unique_views', 'desc')
            ->groupBy('id')
            ->get()
            ->all();
    }
}

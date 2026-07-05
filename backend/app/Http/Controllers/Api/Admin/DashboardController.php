<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardStatsService $statsService,
    ) {}

    public function stats(): JsonResponse
    {
        return response()->json($this->statsService->statistiques());
    }
}

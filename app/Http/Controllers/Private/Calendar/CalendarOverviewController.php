<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\CalendarOverviewRequest;
use App\Http\Resources\ApiResponse;
use App\Services\Calendar\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CalendarOverviewController extends Controller
{
    public function __construct(
        private CalendarService $service,
    ) {}

    public function __invoke(CalendarOverviewRequest $request): JsonResponse
    {
        $from = Carbon::parse($request->validated('from'));
        $to = Carbon::parse($request->validated('to'));
        $userId = auth()->id();

        return ApiResponse::success($this->service->getOverview($userId, $from, $to));
    }
}

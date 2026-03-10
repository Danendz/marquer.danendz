<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\CalendarWeekRequest;
use App\Http\Resources\ApiResponse;
use App\Services\Calendar\CalendarWeekService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CalendarWeekController extends Controller
{
    public function __construct(
        private CalendarWeekService $service,
    ) {}

    public function __invoke(CalendarWeekRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $from = Carbon::parse($request->input('from'))->startOfDay();
        $to = Carbon::parse($request->input('to'))->endOfDay();

        return ApiResponse::success($this->service->assembleWeekView($userId, $from, $to));
    }
}

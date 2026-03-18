<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\CalendarWeekRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Calendar\CountdownResource;
use App\Http\Resources\Tasks\TaskResource;
use App\Services\Calendar\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CalendarWeekController extends Controller
{
    public function __construct(
        private CalendarService $service,
    ) {}

    public function __invoke(CalendarWeekRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $from = Carbon::parse($request->validated('from'))->startOfDay();
        $to = Carbon::parse($request->validated('to'))->endOfDay();

        $data = $this->service->getWeekView($userId, $from, $to);

        $data['tasks'] = $data['tasks']
            ->map(fn ($group) => TaskResource::collection($group)->resolve())
            ->toArray();

        $data['countdowns'] = $data['countdowns']
            ->map(fn ($group) => CountdownResource::collection($group)->resolve())
            ->toArray();

        return ApiResponse::success($data);
    }
}

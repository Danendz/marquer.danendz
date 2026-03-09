<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\StoreCountdownRequest;
use App\Http\Requests\Calendar\UpdateCountdownRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Calendar\CountdownResource;
use App\Models\Countdown;
use App\Services\CountdownService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountdownController extends Controller
{
    public function __construct(
        protected CountdownService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success(CountdownResource::collection($this->service->list($request->user()->id)));
    }

    public function store(StoreCountdownRequest $request): JsonResponse
    {
        $countdown = $this->service->create($request->user()->id, $request->validated());
        return ApiResponse::success(new CountdownResource($countdown));
    }

    public function update(Countdown $countdown, UpdateCountdownRequest $request): JsonResponse
    {
        $countdown = $this->service->update($countdown, $request->validated());
        return ApiResponse::success(new CountdownResource($countdown));
    }

    public function destroy(Countdown $countdown): JsonResponse
    {
        $this->service->delete($countdown);
        return ApiResponse::success();
    }
}

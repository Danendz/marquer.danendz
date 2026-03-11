<?php

namespace App\Http\Controllers\Private\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\PlanForDateRequest;
use App\Http\Requests\Calendar\StorePlanRequest;
use App\Http\Requests\Calendar\TogglePlanTaskCompletionRequest;
use App\Http\Requests\Calendar\UpdatePlanRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Calendar\PlanForDateResource;
use App\Http\Resources\Calendar\PlanResource;
use App\Models\Calendar\Plan;
use App\Models\Calendar\PlanTask;
use App\Services\Calendar\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success(PlanResource::collection($this->service->list($request->user()->id)));
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = $this->service->create($request->user()->id, $request->validated());
        return ApiResponse::success(new PlanResource($plan));
    }

    public function show(Plan $plan): JsonResponse
    {
        return ApiResponse::success(new PlanResource($plan->load('tasks')));
    }

    public function update(Plan $plan, UpdatePlanRequest $request): JsonResponse
    {
        $plan = $this->service->update($plan, $request->validated());
        return ApiResponse::success(new PlanResource($plan));
    }

    public function destroy(Plan $plan): JsonResponse
    {
        $this->service->delete($plan);
        return ApiResponse::success();
    }

    public function forDate(PlanForDateRequest $request): JsonResponse
    {
        $plans = $this->service->getPlansForDate($request->user()->id, $request->validated('date'));
        return ApiResponse::success(PlanForDateResource::collection($plans));
    }

    public function toggleCompletion(PlanTask $planTask, TogglePlanTaskCompletionRequest $request): JsonResponse
    {
        $isCompleted = $this->service->toggleTaskCompletion($planTask, $request->validated('date'));
        return ApiResponse::success(['is_completed' => $isCompleted]);
    }
}

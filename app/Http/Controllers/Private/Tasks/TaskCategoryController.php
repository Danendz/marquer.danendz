<?php

namespace App\Http\Controllers\Private\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskCategoryRequest;
use App\Http\Requests\Tasks\UpdateTaskCategoryRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Tasks\TaskCategoryResource;
use App\Models\Tasks\TaskCategory;
use App\Services\Tasks\TaskCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    /**
     * Create a new controller instance with the required TaskCategoryService dependency.
     */
    public function __construct(
        protected TaskCategoryService $taskCategoryService
    )
    {
    }
    /**
     * Create a new task category for the authenticated user.
     *
     * Uses the validated input from the provided request to create the category and
     * returns the created resource in a standardized success response.
     *
     * @param StoreTaskCategoryRequest $request Request containing validated category data.
     * @return JsonResponse JsonResponse containing the created TaskCategoryResource wrapped in an API success envelope.
     */
    public function store(StoreTaskCategoryRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $category = $this->taskCategoryService->create($userId, $request->validated());

        return ApiResponse::success(new TaskCategoryResource($category));
    }

    /**
     * Updates the given task category using validated request data and returns the updated resource.
     *
     * @param TaskCategory $taskCategory The task category model to update.
     * @param UpdateTaskCategoryRequest $request Request containing validated update data.
     * @return JsonResponse Json response containing the updated TaskCategoryResource wrapped in a success ApiResponse.
     */
    public function update(TaskCategory $taskCategory, UpdateTaskCategoryRequest $request): JsonResponse
    {
        $category = $this->taskCategoryService->update($taskCategory, $request->validated());

        return ApiResponse::success(new TaskCategoryResource($category));
    }

    /**
     * Delete the given task category.
     *
     * @param TaskCategory $taskCategory The task category to delete.
     * @return JsonResponse A success JSON response with no content.
     */
    public function destroy(TaskCategory $taskCategory): JsonResponse
    {
        $this->taskCategoryService->delete($taskCategory);

        return ApiResponse::success();
    }
}
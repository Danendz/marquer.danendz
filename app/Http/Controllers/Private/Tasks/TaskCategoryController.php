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

class TaskCategoryController extends Controller
{
    /**
     * Initialize the controller with its TaskCategoryService dependency.
     */
    public function __construct(
        protected TaskCategoryService $taskCategoryService
    )
    {
    }

    /**
     * Create a new task category for the authenticated user.
     *
     * @param StoreTaskCategoryRequest $request Request containing validated category data.
     * @return JsonResponse A successful API response containing the created TaskCategoryResource.
     */
    public function store(StoreTaskCategoryRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $category = $this->taskCategoryService->create($userId, $request->validated());

        return ApiResponse::success(new TaskCategoryResource($category));
    }

    /**
     * Update an existing task category and return the updated resource.
     *
     * @param UpdateTaskCategoryRequest $request Request containing validated update data.
     * @param TaskCategory $taskCategory The task category to update.    *
     * @return JsonResponse The JSON response containing the updated TaskCategoryResource.
     */
    public function update(UpdateTaskCategoryRequest $request, TaskCategory $taskCategory): JsonResponse
    {
        $category = $this->taskCategoryService->update($taskCategory, $request->validated());

        return ApiResponse::success(new TaskCategoryResource($category));
    }

    /**
     * Deletes the given task category.
     *
     * Deletes the specified TaskCategory and returns a successful API response with no payload.
     *
     * @param TaskCategory $taskCategory The task category to delete.
     * @return JsonResponse Successful API response with no payload.
     */
    public function destroy(TaskCategory $taskCategory): JsonResponse
    {
        $this->taskCategoryService->delete($taskCategory);

        return ApiResponse::success();
    }
}

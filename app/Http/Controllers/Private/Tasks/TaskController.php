<?php

namespace App\Http\Controllers\Private\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\ListTasksRequest;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Tasks\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{

    /**
     * Create a new TaskController instance with the TaskService dependency.
     */
    public function __construct(
        protected TaskService $taskService
    )
    {
    }

    /**
     * List tasks for the authenticated user using the request's validated filters.
     *
     * @param ListTasksRequest $request Request containing validated query and pagination parameters.
     * @return JsonResponse A successful API response containing a collection of TaskResource representations of the user's tasks.
     */
    public function index(ListTasksRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $tasks = $this->taskService->list($userId, $request->validated());
        return ApiResponse::success(TaskResource::collection($tasks));
    }

    /**
     * Create a new task for the authenticated user.
     *
     * @param StoreTaskRequest $request Request containing authenticated user and validated task data.
     * @return JsonResponse ApiResponse containing a TaskResource representation of the created task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $task = $this->taskService->create($userId, $request->validated());
        return ApiResponse::success(new TaskResource($task));
    }

    /**
     * Update the given task with validated input and return its representation.
     *
     * @param Task $task The task model to update.
     * @param UpdateTaskRequest $request Request containing validated update data.
     * @return JsonResponse JSON response with a TaskResource representing the updated task.
     */
    public function update(Task $task, UpdateTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());
        return ApiResponse::success(new TaskResource($task));
    }

    /**
     * Delete the given task.
     *
     * @param Task $task The task to delete.
     * @return JsonResponse A successful API response with no content.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return ApiResponse::success();
    }
}

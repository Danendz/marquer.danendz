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
use Illuminate\Http\Request;

class TaskController extends Controller
{

    /**
     * Injects the TaskService dependency into the controller.
     *
     * @param TaskService $taskService Service responsible for task business logic.
     */
    public function __construct(
        protected TaskService $taskService
    )
    {
    }

    /**
     * Retrieve a collection of tasks for the authenticated user using the request's filters.
     *
     * @param ListTasksRequest $request Request containing validated listing filters and pagination parameters.
     * @return JsonResponse A JSON success response wrapping a collection of TaskResource objects for the user's tasks.
     */
    public function index(ListTasksRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $tasks = $this->taskService->list($userId, $request->validated());
        return ApiResponse::success(TaskResource::collection($tasks));
    }

    /**
     * Create a new task for the authenticated user using the request's validated data.
     *
     * @param StoreTaskRequest $request Request containing validated task creation data and the authenticated user.
     * @return JsonResponse A success ApiResponse containing the created task as a `TaskResource`.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $task = $this->taskService->create($userId, $request->validated());
        return ApiResponse::success(new TaskResource($task));
    }

    /**
     * Update the specified task using validated input and return the updated task resource.
     *
     * @param \App\Models\Task $task The task model to update.
     * @param \App\Http\Requests\UpdateTaskRequest $request The request containing validated update attributes.
     * @return \Illuminate\Http\JsonResponse The updated TaskResource wrapped in a standardized success response.
     */
    public function update(Task $task, UpdateTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());
        return ApiResponse::success(new TaskResource($task));
    }

    /**
     * Delete the given task.
     *
     * @param Task $task The task model to delete.
     * @return JsonResponse A JSON success response with an empty payload.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return ApiResponse::success();
    }
}
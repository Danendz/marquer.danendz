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

    public function __construct(
        protected TaskService $taskService
    )
    {
    }

    public function index(ListTasksRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $tasks = $this->taskService->list($userId, $request->validated());
        return ApiResponse::success(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $task = $this->taskService->create($userId, $request->validated());
        return ApiResponse::success(new TaskResource($task));
    }

    public function update(Task $task, UpdateTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());
        return ApiResponse::success(new TaskResource($task));
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return ApiResponse::success();
    }
}

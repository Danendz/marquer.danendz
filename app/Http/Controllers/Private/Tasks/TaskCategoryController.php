<?php

namespace App\Http\Controllers\Private\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskCategoryRequest;
use App\Http\Requests\Tasks\UpdateTaskCategoryRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Tasks\TaskCategoryResource;
use App\Services\Tasks\TaskCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function __construct(
        protected TaskCategoryService $taskCategoryService
    )
    {
    }
    public function store(StoreTaskCategoryRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $category = $this->taskCategoryService->create($userId, $request->validated());

        return ApiResponse::success(new TaskCategoryResource($category));
    }

    public function update(int $id, UpdateTaskCategoryRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $category = $this->taskCategoryService->update($id, $userId, $request->validated());

        return ApiResponse::success(new TaskCategoryResource($category));
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $this->taskCategoryService->delete($id, $userId);

        return ApiResponse::success();
    }
}

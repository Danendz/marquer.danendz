<?php

namespace App\Http\Controllers\Private\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskFolderRequest;
use App\Http\Requests\Tasks\UpdateTaskFolderRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Tasks\TaskFolderResource;
use App\Models\Tasks\TaskFolder;
use App\Services\Tasks\TaskFolderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskFolderController extends Controller
{

    public function __construct(
        protected TaskFolderService $taskFolderService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $folders = $this->taskFolderService->list($userId);
        return ApiResponse::success(TaskFolderResource::collection($folders));
    }

    public function store(StoreTaskFolderRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $folder = $this->taskFolderService->create($userId, $request->validated());
        return ApiResponse::success(new TaskFolderResource($folder));
    }

    public function update(TaskFolder $taskFolder, UpdateTaskFolderRequest $request): JsonResponse
    {
        $folder = $this->taskFolderService->update($taskFolder, $request->validated());
        return ApiResponse::success(new TaskFolderResource($folder));
    }

    public function destroy(TaskFolder $taskFolder): JsonResponse
    {
        $this->taskFolderService->delete($taskFolder);
        return ApiResponse::success();
    }
}

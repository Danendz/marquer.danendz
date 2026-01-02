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

    /**
     * Create a controller configured with the task folder service.
     *
     * @param TaskFolderService $taskFolderService The service responsible for task folder business logic.
     */
    public function __construct(
        protected TaskFolderService $taskFolderService
    )
    {
    }

    /**
     * Return a list of task folders for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request; must contain an authenticated user.
     * @return \Illuminate\Http\JsonResponse A JSON response containing a collection of TaskFolderResource representations of the user's task folders.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $folders = $this->taskFolderService->list($userId);
        return ApiResponse::success(TaskFolderResource::collection($folders));
    }

    /**
     * Create a new task folder for the authenticated user.
     *
     * @param StoreTaskFolderRequest $request Validated input used to create the folder.
     * @return JsonResponse API success response containing the created TaskFolderResource.
     */
    public function store(StoreTaskFolderRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $folder = $this->taskFolderService->create($userId, $request->validated());
        return ApiResponse::success(new TaskFolderResource($folder));
    }

    /**
     * Update the specified task folder with validated input and return the updated resource.
     *
     * @param \App\Models\TaskFolder $taskFolder The task folder to update.
     * @param \App\Http\Requests\Tasks\UpdateTaskFolderRequest $request The request containing validated update data.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated TaskFolderResource.
     */
    public function update(TaskFolder $taskFolder, UpdateTaskFolderRequest $request): JsonResponse
    {
        $folder = $this->taskFolderService->update($taskFolder, $request->validated());
        return ApiResponse::success(new TaskFolderResource($folder));
    }

    /**
     * Delete the given task folder.
     *
     * @param TaskFolder $taskFolder The task folder to delete.
     * @return JsonResponse A success response with no data.
     */
    public function destroy(TaskFolder $taskFolder): JsonResponse
    {
        $this->taskFolderService->delete($taskFolder);
        return ApiResponse::success();
    }
}
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
     * Initialize the controller with the TaskFolderService dependency.
     */
    public function __construct(
        protected TaskFolderService $taskFolderService
    )
    {
    }

    /**
     * Return the authenticated user's task folders as a JSON API response.
     *
     * @param Request $request The incoming HTTP request; must be from an authenticated user.
     * @return JsonResponse A success ApiResponse containing a collection of TaskFolderResource objects representing the user's folders.
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
     * @param StoreTaskFolderRequest $request Request containing validated attributes for the new folder and the authenticated user.
     * @return \Illuminate\Http\JsonResponse JSON response with the created TaskFolderResource wrapped in a successful API response.
     */
    public function store(StoreTaskFolderRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $folder = $this->taskFolderService->create($userId, $request->validated());
        return ApiResponse::success(new TaskFolderResource($folder));
    }

    /**
     * Update an existing task folder using validated input and return its updated representation.
     *
     * @param TaskFolder $taskFolder The folder model resolved via route model binding to be updated.
     * @param UpdateTaskFolderRequest $request Request containing validated update attributes.
     * @return JsonResponse JsonResponse wrapping an ApiResponse that contains the updated TaskFolderResource.
     */
    public function update(TaskFolder $taskFolder, UpdateTaskFolderRequest $request): JsonResponse
    {
        $folder = $this->taskFolderService->update($taskFolder, $request->validated());
        return ApiResponse::success(new TaskFolderResource($folder));
    }

    /**
     * Delete the given task folder.
     *
     * @param TaskFolder $taskFolder The folder to delete.
     * @return JsonResponse A successful API response with no additional data.
     */
    public function destroy(TaskFolder $taskFolder): JsonResponse
    {
        $this->taskFolderService->delete($taskFolder);
        return ApiResponse::success();
    }
}
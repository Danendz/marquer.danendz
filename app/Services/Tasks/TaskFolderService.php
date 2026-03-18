<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskFolder;
use App\Services\Concerns\PublishesAnalytics;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Collection;

readonly class TaskFolderService
{
    use PublishesAnalytics;

    public function __construct(
        private RabbitPublisherService $publisher
    )
    {
    }

    /**
     * Retrieve task folders belonging to the specified user.
     *
     * Each returned TaskFolder model has its `categories` relationship loaded,
     * and each category includes a `tasks_count` property with the number of related tasks.
     *
     * @param int $userId The ID of the user whose task folders should be retrieved.
     * @return Collection A collection of TaskFolder models with categories and task counts loaded.
     */
    public function list(int $userId): Collection
    {
        return TaskFolder::where(['user_id' => $userId])->with(['categories' => fn($q) => $q->withCount('tasks')])->get();
    }

    public function create(int $userId, array $data): TaskFolder
    {
        return $this->withAnalytics('task.folder_created', 'task_folder_created', 'task_folder_id', function () use ($userId, $data) {
            return TaskFolder::create([...$data, 'user_id' => $userId]);
        });
    }

    public function update(TaskFolder $taskFolder, array $data): TaskFolder
    {
        return $this->withAnalytics('task.folder_updated', 'task_folder_updated', 'task_folder_id', function () use ($taskFolder, $data) {
            $taskFolder->update($data);
            return $taskFolder;
        });
    }

    public function delete(TaskFolder $taskFolder): void
    {
        $this->deleteWithAnalytics($taskFolder, 'task.folder_deleted', 'task_folder_deleted', 'task_folder_id');
    }
}

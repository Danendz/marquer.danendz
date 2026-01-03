<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskFolder;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class TaskFolderService
{
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

    /**
     * Create a new TaskFolder for the specified user with the given attributes.
     *
     * @param int $userId ID of the user who will own the folder.
     * @param array $data Attributes for the new TaskFolder.
     * @return TaskFolder The newly created TaskFolder model.
     */
    public function create(int $userId, array $data): TaskFolder
    {
        return DB::transaction(function () use ($userId, $data) {
            $taskFolder = TaskFolder::create([
                ...$data,
                'user_id' => $userId
            ]);

            DB::afterCommit(function () use ($taskFolder) {
                $this->publisher->publishAnalytics('task.folder_created', [
                    'event_name' => 'task_folder_created',
                    'properties' => [
                        'task_folder_id' => $taskFolder->id
                    ]
                ]);
            });

            return $taskFolder;
        });
    }

    /**
     * Update the given TaskFolder with the provided attributes.
     *
     * @param TaskFolder $taskFolder The TaskFolder model to update.
     * @param array $data Associative array of attributes to apply to the model.
     * @return TaskFolder The updated TaskFolder instance.
     */
    public function update(TaskFolder $taskFolder, array $data): TaskFolder
    {
        return DB::transaction(static function () use ($taskFolder, $data) {
            $taskFolder->update($data);

            DB::afterCommit(function () use ($taskFolder) {
                $this->publisher->publishAnalytics('task.folder_updated', [
                    'event_name' => 'task_folder_updated',
                    'properties' => [
                        'task_folder_id' => $taskFolder->id
                    ]
                ]);
            });

            return $taskFolder;
        });
    }

    /**
     * Delete the given task folder from persistent storage.
     *
     * @param TaskFolder $taskFolder The TaskFolder model to delete.
     */
    public function delete(TaskFolder $taskFolder): void
    {
        DB::transaction(function () use ($taskFolder) {
            $taskFolder->delete();

            DB::afterCommit(function () use ($taskFolder) {
                $this->publisher->publishAnalytics('task.folder_deleted', [
                    'event_name' => 'task_folder_deleted',
                    'properties' => [
                        'task_folder_id' => $taskFolder->id
                    ]
                ]);
            });
        });
    }
}

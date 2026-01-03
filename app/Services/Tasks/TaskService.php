<?php

namespace App\Services\Tasks;

use App\Models\Tasks\Task;
use App\Services\RabbitPublisher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class TaskService
{
    public function __construct(
        private RabbitPublisher $publisher
    )
    {
    }

    /**
     * Retrieve tasks for a user filtered by task category.
     *
     * @param int $userId The ID of the user whose tasks will be retrieved.
     * @param array $data Array containing query parameters; must include `task_category_id` to filter by category.
     * @return Collection Collection of Task models matching the given user and category.
     */
    public function list(int $userId, array $data): Collection
    {
        return Task::where([
                ['user_id', $userId],
                ['task_category_id', $data['task_category_id']]
            ]
        )->get();
    }

    /**
     * Create a new Task for the given user.
     *
     * @param int $userId ID of the user who will own the new task.
     * @param array $data Associative array of task attributes; the `user_id` key will be set to `$userId`.
     * @return Task The newly created Task instance.
     */
    public function create(int $userId, array $data): Task
    {
        return DB::transaction(function () use ($userId, $data) {
            $task = Task::create([
                ...$data,
                'user_id' => $userId
            ]);

            DB::afterCommit(function () use ($task) {
                $this->publisher->publishAnalytics('task.created', [
                    'event_name' => 'task_created',
                    'properties' => [
                        'task_id' => $task->id
                    ]
                ]);
            });

            return $task;
        });
    }

    /**
     * Update the given Task with the provided attributes.
     *
     * @param Task $task The Task model to update.
     * @param array $data Associative array of attributes to apply to the task.
     * @return Task The updated Task instance.
     */
    public function update(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $task->update($data);

            DB::afterCommit(function () use ($task) {
                $this->publisher->publishAnalytics('task.updated', [
                    'event_name' => 'task_updated',
                    'properties' => [
                        'task_id' => $task->id
                    ]
                ]);
            });

            return $task;
        });
    }

    /**
     * Remove the given Task from persistent storage.
     *
     * @param Task $task The Task instance to delete.
     */
    public function delete(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $task->delete();

            DB::afterCommit(function () use ($task) {
                $this->publisher->publishAnalytics('task.deleted', [
                    'event_name' => 'task_deleted',
                    'properties' => [
                        'task_id' => $task->id
                    ]
                ]);
            });

            return $task;
        });
    }
}

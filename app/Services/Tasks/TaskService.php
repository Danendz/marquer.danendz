<?php

namespace App\Services\Tasks;

use App\Models\Tasks\Task;
use Illuminate\Support\Collection;

class TaskService
{
    /**
     * Retrieve tasks for a user filtered by task category.
     *
     * @param int $userId The ID of the user whose tasks to retrieve.
     * @param array $data Array containing filter values; requires key `task_category_id` to specify the category ID.
     * @return Collection A collection of Task models that belong to the given user and task category.
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
     * Create a new Task assigned to the given user.
     *
     * @param int $userId The ID of the user to assign as the task owner.
     * @param array $data Attributes for the new Task; `user_id` will be set to `$userId`.
     * @return \App\Models\Task The created Task model instance.
     */
    public function create(int $userId, array $data): Task
    {
        $data['user_id'] = $userId;

        return Task::create($data);
    }

    /**
     * Update the given Task with the provided attributes and return the Task instance.
     *
     * @param Task $task The Task model to update.
     * @param array $data Attributes to apply to the Task.
     * @return Task The updated Task instance.
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    /**
     * Permanently removes the given Task record from storage.
     *
     * @param Task $task The Task instance to delete.
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }
}
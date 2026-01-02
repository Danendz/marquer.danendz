<?php

namespace App\Services\Tasks;

use App\Models\Tasks\Task;
use Illuminate\Support\Collection;

class TaskService
{
    public function list(int $userId, array $data): Collection
    {
        return Task::where([
                ['user_id', $userId],
                ['task_category_id', $data['task_category_id']]
            ]
        )->get();
    }

    public function create(int $userId, array $data): Task
    {
        $data['user_id'] = $userId;

        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}

<?php

namespace App\Services\Tasks;

use App\Models\Tasks\Task;
use App\Services\Concerns\PublishesAnalytics;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Collection;

readonly class TaskService
{
    use PublishesAnalytics;

    public function __construct(
        private RabbitPublisherService $publisher
    )
    {
    }

    /**
     * Retrieve tasks for a user with optional filters.
     *
     * @param int $userId The ID of the user whose tasks will be retrieved.
     * @param array $data Optional filters: `task_category_id`, `task_folder_id`, `status`.
     * @return Collection Collection of Task models matching the given criteria.
     */
    public function list(int $userId, array $data): Collection
    {
        $query = Task::where('user_id', $userId);

        if (isset($data['task_category_id']) && $data['task_category_id'] !== null) {
            $query->where('task_category_id', $data['task_category_id']);
        }

        if (isset($data['task_folder_id']) && $data['task_folder_id'] !== null) {
            $query->whereHas('category', fn($q) => $q->where('task_folder_id', $data['task_folder_id']));
        }

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (!empty($data['date'])) {
            $query->whereDate('date', $data['date']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function create(int $userId, array $data): Task
    {
        return $this->withAnalytics('task.created', 'task_created', 'task_id', function () use ($userId, $data) {
            return Task::create([...$data, 'user_id' => $userId]);
        });
    }

    public function update(Task $task, array $data): Task
    {
        return $this->withAnalytics('task.updated', 'task_updated', 'task_id', function () use ($task, $data) {
            $task->update($data);
            return $task;
        });
    }

    public function delete(Task $task): void
    {
        $this->deleteWithAnalytics($task, 'task.deleted', 'task_deleted', 'task_id');
    }
}

<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskCategory;
use App\Services\Concerns\PublishesAnalytics;
use App\Services\RabbitPublisherService;

readonly class TaskCategoryService
{
    use PublishesAnalytics;

    public function __construct(private RabbitPublisherService $publisher)
    {
    }

    /**
     * Set the 'color' key in the provided data array to the default value '#fff'.
     *
     * @param array $data Input data array to update.
     * @return string The updated data array with `'color'` set to `'#fff'`.
     */
    private function getRandomColor(): string
    {
        //TODO: ADD random color generation
        return '#fff';
    }

    public function create(int $userId, array $data): TaskCategory
    {
        return $this->withAnalytics('task.category_created', 'task_category_created', 'task_category_id', function () use ($data, $userId) {
            $taskCategory = TaskCategory::create([
                ...$data,
                'user_id' => $userId,
                'color' => $data['color'] ?? $this->getRandomColor()
            ]);

            $taskCategory->loadCount('tasks');

            return $taskCategory;
        });
    }

    public function update(TaskCategory $taskCategory, array $data): TaskCategory
    {
        return $this->withAnalytics('task.category_updated', 'task_category_updated', 'task_category_id', function () use ($data, $taskCategory) {
            $taskCategory->update($data);

            $taskCategory->loadCount('tasks');

            return $taskCategory;
        });
    }

    public function delete(TaskCategory $taskCategory): void
    {
        $this->deleteWithAnalytics($taskCategory, 'task.category_deleted', 'task_category_deleted', 'task_category_id');
    }
}

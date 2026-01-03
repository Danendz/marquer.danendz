<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskCategory;
use App\Services\RabbitPublisher;
use Illuminate\Support\Facades\DB;

readonly class TaskCategoryService
{
    public function __construct(private RabbitPublisher $publisher)
    {
    }

    /**
     * Set the 'color' key in the provided data array to the default value '#fff'.
     *
     * @param array $data Input data array to update.
     * @return array The updated data array with `'color'` set to `'#fff'`.
     */
    private function setRandomColor(array $data): array {
        $data['color'] = '#fff';
        return $data;
    }

    /**
     * Create a new TaskCategory for the given user, ensuring a default color is set when absent.
     *
     * @param int $userId ID of the user who will own the created TaskCategory.
     * @param array $data Attributes for the new TaskCategory; if `color` is missing or empty a default will be applied.
     * @return TaskCategory The created TaskCategory instance.
     */
    public function create(int $userId, array $data): TaskCategory
    {
        return DB::transaction(function () use ($data, $userId) {
            $taskCategory = TaskCategory::create([
                ...$data,
                'user_id' => $userId,
                'color' => $data['color'] ?? $this->setRandomColor($data)
            ]);

            DB::afterCommit(function () use ($taskCategory) {
                $this->publisher->publishAnalytics('task.category_created', [
                    'event_name' => 'task_category_created',
                    'properties' => ['task_category_id' => $taskCategory->id]
                ]);
            });

            return $taskCategory;
        });
    }

    /**
     * Updates an existing TaskCategory with the provided attributes.
     *
     * @param TaskCategory $taskCategory The TaskCategory model to update.
     * @param array $data Associative array of attributes to apply to the model.
     * @return TaskCategory The updated TaskCategory instance.
     */
    public function update(TaskCategory $taskCategory, array $data): TaskCategory
    {
        return DB::transaction(function () use ($data, $taskCategory) {
            $taskCategory->update($data);

            DB::afterCommit(function () use ($taskCategory) {
                $this->publisher->publishAnalytics('task.category_updated', [
                    'event_name' => 'task_category_updated',
                    'properties' => ['task_category_id' => $taskCategory->id]
                ]);
            });

            return $taskCategory;
        });
    }

    /**
     * Delete the given TaskCategory model.
     *
     * @param TaskCategory $taskCategory The TaskCategory model to delete.
     */
    public function delete(TaskCategory $taskCategory): void
    {
        DB::transaction(function () use ($taskCategory) {
            $taskCategory->delete();

            DB::afterCommit(function () use ($taskCategory) {
                $this->publisher->publishAnalytics('task.category_deleted', [
                    'event_name' => 'task_category_deleted',
                    'properties' => ['task_category_id' => $taskCategory->id]
                ]);
            });

            return $taskCategory;
        });
    }
}

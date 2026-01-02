<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskCategory;

class TaskCategoryService
{
    /**
     * Ensure the data array contains a default color value.
     *
     * @param array $data Input attributes for a task category; the 'color' key will be set to the default value.
     * @return array The modified data array with the 'color' key set to "#fff".
     */
    private function setRandomColor(array $data): array {
        $data['color'] = '#fff';
        return $data;
    }

    /**
     * Create a new task category for a user, assigning a default color if none is provided.
     *
     * @param int $userId The ID of the user who will own the category.
     * @param array $data Attributes for the new category; if the `color` key is empty a default of `#fff` is applied.
     * @return TaskCategory The newly created TaskCategory model.
     */
    public function create(int $userId, array $data): TaskCategory
    {
        $data['user_id'] = $userId;

        if (empty($data['color'])) {
            $data = $this->setRandomColor($data);
        }

        return TaskCategory::create($data);
    }

    /**
     * Update the given task category with the provided attributes and return the updated model.
     *
     * @param TaskCategory $taskCategory The TaskCategory model instance to update.
     * @param array $data Attributes to apply to the task category.
     * @return TaskCategory The updated TaskCategory model.
     */
    public function update(TaskCategory $taskCategory, array $data): TaskCategory
    {
        $taskCategory->update($data);

        return $taskCategory;
    }

    /**
     * Deletes the given task category.
     *
     * @param TaskCategory $taskCategory The task category to delete.
     */
    public function delete(TaskCategory $taskCategory): void
    {
        $taskCategory->delete();
    }
}
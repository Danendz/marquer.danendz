<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskCategory;

class TaskCategoryService
{
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
     * @return \App\Models\TaskCategory The created TaskCategory instance.
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
     * Updates an existing TaskCategory with the provided attributes.
     *
     * @param \App\Models\TaskCategory $taskCategory The TaskCategory model to update.
     * @param array $data Associative array of attributes to apply to the model.
     * @return \App\Models\TaskCategory The updated TaskCategory instance.
     */
    public function update(TaskCategory $taskCategory, array $data): TaskCategory
    {
        $taskCategory->update($data);

        return $taskCategory;
    }

    /**
     * Delete the given TaskCategory model.
     *
     * @param TaskCategory $taskCategory The TaskCategory model to delete.
     */
    public function delete(TaskCategory $taskCategory): void
    {
        $taskCategory->delete();
    }
}
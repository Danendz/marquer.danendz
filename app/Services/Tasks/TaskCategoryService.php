<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskCategory;

class TaskCategoryService
{
    private function setRandomColor(array $data): array {
        $data['color'] = '#fff';
        return $data;
    }

    public function create(int $userId, array $data): TaskCategory
    {
        $data['user_id'] = $userId;

        if (empty($data['color'])) {
            $data = $this->setRandomColor($data);
        }

        return TaskCategory::create($data);
    }

    public function update(TaskCategory $taskCategory, array $data): TaskCategory
    {
        $taskCategory->update($data);

        return $taskCategory;
    }

    public function delete(TaskCategory $taskCategory): void
    {
        $taskCategory->delete();
    }
}

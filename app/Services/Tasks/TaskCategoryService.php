<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskCategory;

class TaskCategoryService
{
    public function getById(int $id, int $userId): TaskCategory
    {
        return TaskCategory::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

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

    public function update(int $id, int $userId, array $data): TaskCategory
    {
        $taskCategory = $this->getById($id, $userId);
        $taskCategory->update($data);

        return $taskCategory;
    }

    public function delete(int $id, int $userId): void
    {
        $this->getById($id, $userId)->delete();
    }
}

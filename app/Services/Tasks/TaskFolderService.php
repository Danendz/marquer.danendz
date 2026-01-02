<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskFolder;
use Illuminate\Support\Collection;

class TaskFolderService
{
    public function list(int $userId): Collection
    {
        return TaskFolder::where(['user_id' => $userId])->with('categories')->get();
    }

    public function getById(int $id, int $userId): TaskFolder
    {
        return TaskFolder::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function create(int $userId, array $data): TaskFolder
    {
        $data['user_id'] = $userId;
        return TaskFolder::create($data);
    }

    public function update(int $id, int $userId, array $data): TaskFolder
    {
        $taskFolder = $this->getById($id, $userId);
        $taskFolder->update($data);

        return $taskFolder;
    }

    public function delete(int $id, int $userId): void
    {
        $this->getById($id, $userId)->delete();
    }
}

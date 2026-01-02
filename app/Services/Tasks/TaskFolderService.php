<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskFolder;
use Illuminate\Support\Collection;

class TaskFolderService
{
    public function list(int $userId): Collection
    {
        return TaskFolder::where(['user_id' => $userId])->with(['categories' => fn($q) => $q->withCount('tasks')])->get();
    }

    public function create(int $userId, array $data): TaskFolder
    {
        $data['user_id'] = $userId;
        return TaskFolder::create($data);
    }

    public function update(TaskFolder $taskFolder, array $data): TaskFolder
    {
        $taskFolder->update($data);

        return $taskFolder;
    }

    public function delete(TaskFolder $taskFolder): void
    {
        $taskFolder->delete();
    }
}

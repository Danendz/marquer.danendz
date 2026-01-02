<?php

namespace App\Services\Tasks;

use App\Models\Tasks\TaskFolder;
use Illuminate\Support\Collection;

class TaskFolderService
{
    /**
     * Retrieve all task folders for the given user, including each folder's categories and each category's task count.
     *
     * @param int $userId The ID of the user whose task folders to retrieve.
     * @return Collection A collection of TaskFolder models with the `categories` relation loaded; each category includes a `tasks_count` attribute.
     */
    public function list(int $userId): Collection
    {
        return TaskFolder::where(['user_id' => $userId])->with(['categories' => fn($q) => $q->withCount('tasks')])->get();
    }

    /**
     * Creates a new TaskFolder for the specified user.
     *
     * @param int $userId ID of the user who will own the folder.
     * @param array $data Attributes to set on the new TaskFolder.
     * @return \App\Models\Tasks\TaskFolder The newly created TaskFolder instance.
     */
    public function create(int $userId, array $data): TaskFolder
    {
        $data['user_id'] = $userId;
        return TaskFolder::create($data);
    }

    /**
     * Update the given TaskFolder with the provided attributes.
     *
     * @param TaskFolder $taskFolder The TaskFolder to update.
     * @param array $data Associative array of attributes to apply to the TaskFolder.
     * @return TaskFolder The updated TaskFolder instance.
     */
    public function update(TaskFolder $taskFolder, array $data): TaskFolder
    {
        $taskFolder->update($data);

        return $taskFolder;
    }

    /**
     * Permanently removes the given TaskFolder from storage.
     *
     * @param \App\Models\Tasks\TaskFolder $taskFolder The TaskFolder instance to delete.
     */
    public function delete(TaskFolder $taskFolder): void
    {
        $taskFolder->delete();
    }
}
<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class NoteService
{
    public function list(int $user_id): Collection
    {
        return Note::where('user_id', $user_id)->get();
    }

    public function get_by_id(int $id, int $userId): Note
    {
        return Note::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function create(int $user_id, array $data): Note
    {
        $data['user_id'] = $user_id;

        return Note::create($data);
    }

    public function update(int $id, int $user_id, array $data): Note
    {
        $note = $this->get_by_id($id, $user_id);
        $note->update($data);

        return $note;
    }

    public function delete(int $id, int $user_id): void {
        $this->get_by_id($id, $user_id)->delete();
    }
}

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

    public function get_by_id(int $id, int $user_id): Note
    {
        return Note::where('id', $id)
            ->where('user_id', $user_id)
            ->firstOrFail();
    }

    public function create(int $user_id, array $data): Note
    {
        $data['user_id'] = $user_id;

        return Note::create($data);
    }

    public function update(Note $note, array $data): Note
    {
        $note->update($data);

        return $note;
    }

    public function delete(Note $note): void {
        $note->delete();
    }
}

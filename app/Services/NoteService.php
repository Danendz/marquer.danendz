<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class NoteService
{
    /**
     * Retrieve all notes belonging to the specified user.
     *
     * @param int $user_id The ID of the user whose notes should be returned.
     * @return Collection A collection of Note models for the given user.
     */
    public function list(int $user_id): Collection
    {
        return Note::where('user_id', $user_id)->get();
    }

    /**
     * Retrieve a Note by its ID that belongs to the given user.
     *
     * @param int $id The Note's ID.
     * @param int $user_id The user's ID who must own the Note.
     * @return Note The matching Note model.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching Note is found.
     */
    public function get_by_id(int $id, int $user_id): Note
    {
        return Note::where('id', $id)
            ->where('user_id', $user_id)
            ->firstOrFail();
    }

    /**
     * Create a new Note belonging to the given user.
     *
     * @param int   $user_id The ID of the user who will own the note.
     * @param array $data    Attributes for the new note.
     * @return \App\Models\Note The newly created Note model.
     */
    public function create(int $user_id, array $data): Note
    {
        $data['user_id'] = $user_id;

        return Note::create($data);
    }

    /**
     * Update a Note model with the given attributes.
     *
     * @param \App\Models\Note $note The Note model to update.
     * @param array $data Attributes to set on the Note.
     * @return \App\Models\Note The updated Note model.
     */
    public function update(Note $note, array $data): Note
    {
        $note->update($data);

        return $note;
    }

    /**
     * Deletes the given Note model from persistent storage.
     *
     * @param Note $note The Note model to delete.
     */
    public function delete(Note $note): void {
        $note->delete();
    }
}
<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class NoteService
{
    /**
     * Retrieve all notes belonging to a specific user.
     *
     * @param int $user_id The ID of the user whose notes will be retrieved.
     * @return \Illuminate\Database\Eloquent\Collection Collection of Note instances belonging to the given user.
     */
    public function list(int $user_id): Collection
    {
        return Note::where('user_id', $user_id)->get();
    }

    /**
     * Retrieve a Note by id belonging to the specified user.
     *
     * @param int $id The note's id.
     * @param int $user_id The owner's user id used to scope the lookup.
     * @return \App\Models\Note The matching Note instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no matching Note exists.
     */
    public function get_by_id(int $id, int $user_id): Note
    {
        return Note::where('id', $id)
            ->where('user_id', $user_id)
            ->firstOrFail();
    }

    /**
     * Create a new Note for the specified user.
     *
     * @param int $user_id ID of the user who will own the note.
     * @param array $data Attributes to set on the new note.
     * @return \App\Models\Note The created Note instance.
     */
    public function create(int $user_id, array $data): Note
    {
        $data['user_id'] = $user_id;

        return Note::create($data);
    }

    /**
     * Update the given Note with the provided attributes.
     *
     * @param \App\Models\Note $note The Note instance to update.
     * @param array $data Associative array of attributes to update on the Note.
     * @return \App\Models\Note The updated Note instance.
     */
    public function update(Note $note, array $data): Note
    {
        $note->update($data);

        return $note;
    }

    /**
     * Delete the given Note.
     *
     * @param Note $note The Note instance to delete.
     */
    public function delete(Note $note): void {
        $note->delete();
    }
}
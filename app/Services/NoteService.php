<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

readonly class NoteService
{
    public function __construct(private RabbitPublisher $publisher)
    {
    }

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
     * Create a new Note belonging to the given user.
     *
     * @param int $userId The ID of the user who will own the note.
     * @param array $data Attributes for the new note.
     * @return Note The newly created Note model.
     * @throws \Throwable
     */
    public function create(int $userId, array $data): Note
    {
        return DB::transaction(function () use ($data, $userId) {
            $note = Note::create([
                ...$data,
                'user_id' => $userId
            ]);

            DB::afterCommit(function () use ($note) {
                $this->publisher->publishAnalytics('note.created', [
                    'event_name' => 'note_created',
                    'properties' => ['note_id' => $note->id]
                ]);
            });

            return $note;
        });
    }

    /**
     * Update a Note model with the given attributes.
     *
     * @param Note $note The Note model to update.
     * @param array $data Attributes to set on the Note.
     * @return Note The updated Note model.
     */
    public function update(Note $note, array $data): Note
    {
        return DB::transaction(function () use ($data, $note) {
            $note->update($data);

            DB::afterCommit(function () use ($note) {
                $this->publisher->publishAnalytics('note.updated', [
                    'event_name' => 'note_updated',
                    'properties' => ['note_id' => $note->id]
                ]);
            });

            return $note;
        });
    }

    /**
     * Deletes the given Note model from persistent storage.
     *
     * @param Note $note The Note model to delete.
     */
    public function delete(Note $note): void
    {
        DB::transaction(function () use ($note) {
            $note->delete();
            DB::afterCommit(function () use ($note) {
                $this->publisher->publishAnalytics('note.updated', [
                    'event_name' => 'note_deleted',
                    'properties' => ['note_id' => $note->id]
                ]);
            });
        });
    }
}

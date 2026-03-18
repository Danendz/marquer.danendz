<?php

namespace App\Services;

use App\Models\Note;
use App\Services\Concerns\PublishesAnalytics;
use Illuminate\Database\Eloquent\Collection;

readonly class NoteService
{
    use PublishesAnalytics;

    public function __construct(private RabbitPublisherService $publisher)
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

    public function view(Note $note): void
    {
        $this->publisher->publishAnalyticsSafely('note.watched', [
            'event_name' => 'note_watched',
            'properties' => ['note_id' => $note->id]
        ]);
    }

    public function create(int $userId, array $data): Note
    {
        return $this->withAnalytics('note.created', 'note_created', 'note_id', function () use ($data, $userId) {
            return Note::create([...$data, 'user_id' => $userId]);
        });
    }

    public function update(Note $note, array $data): Note
    {
        return $this->withAnalytics('note.updated', 'note_updated', 'note_id', function () use ($data, $note) {
            $note->update($data);
            return $note;
        });
    }

    public function delete(Note $note): void
    {
        $this->deleteWithAnalytics($note, 'note.deleted', 'note_deleted', 'note_id');
    }
}

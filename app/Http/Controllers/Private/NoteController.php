<?php

namespace App\Http\Controllers\Private;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Notes\NoteListResource;
use App\Http\Resources\Notes\NoteResource;
use App\Models\Note;
use App\Services\NoteService;
use App\Services\RabbitPublisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Instantiate the controller with its NoteService dependency.
     */
    public function __construct(
        protected NoteService $noteService,
        private readonly RabbitPublisher $publisher
    )
    {
    }

    /**
     * Retrieves notes for the authenticated user.
     *
     * @param Request $request The HTTP request containing the authenticated user.
     * @return JsonResponse A successful API response containing a collection of NoteListResource items.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $notes = $this->noteService->list($userId);
        return ApiResponse::success(NoteListResource::collection($notes));
    }

    /**
     * Create a new note for the authenticated user and return it as a resource.
     *
     * @param StoreNoteRequest $request Request containing validated note attributes for creation; the authenticated user is used as the owner.
     * @return JsonResponse ApiResponse success containing a NoteResource for the created note.
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $note = $this->noteService->create($userId, $request->validated());
        return ApiResponse::success(new NoteResource($note));
    }

    /**
     * Return the given note as a NoteResource inside a successful API response.
     *
     * @param Note $note The note to return.
     * @return JsonResponse The successful API response containing the note serialized by NoteResource.
     */
    public function show(Note $note): JsonResponse
    {
        $this->publisher->publishAnalytics('note.watched', [
            'event_name' => 'note_watched',
            'properties' => ['note_id' => $note->id]
        ]);

        return ApiResponse::success(new NoteResource($note));
    }


    /**
     * Update the given note using validated request data and return the updated note resource.
     *
     * @param Note $note The note to update.
     * @param UpdateNoteRequest $request The request containing validated update attributes.
     * @return JsonResponse A JSON response containing the updated NoteResource.
     */
    public function update(Note $note, UpdateNoteRequest $request): JsonResponse
    {
        return ApiResponse::success(new NoteResource($this->noteService->update($note, $request->validated())));
    }

    /**
     * Deletes the specified note.
     *
     * @param Note $note The note to delete.
     * @return JsonResponse An empty successful API response.
     */
    public function destroy(Note $note): JsonResponse
    {
        $this->noteService->delete($note);
        return ApiResponse::success();
    }
}

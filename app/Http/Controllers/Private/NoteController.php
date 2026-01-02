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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Initialize the controller with the NoteService dependency.
     */
    public function __construct(
        protected NoteService $noteService
    )
    {
    }

    /**
     * Return a list of notes belonging to the authenticated user.
     *
     * @param Request $request The current HTTP request; must contain an authenticated user.
     * @return JsonResponse A successful ApiResponse containing a collection of NoteListResource representations of the user's notes.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $notes = $this->noteService->list($userId);
        return ApiResponse::success(NoteListResource::collection($notes));
    }

    /**
     * Create a new note for the authenticated user.
     *
     * @param StoreNoteRequest $request Request containing validated note attributes.
     * @return JsonResponse A successful API response containing the created note as a NoteResource.
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $note = $this->noteService->create($userId, $request->validated());
        return ApiResponse::success(new NoteResource($note));
    }

    /**
     * Respond with the given note wrapped in a successful API response.
     *
     * @param Note $note The note instance resolved via route-model binding.
     * @return JsonResponse A successful API response containing the note as a `NoteResource`.
     */
    public function show(Note $note): JsonResponse
    {
        return ApiResponse::success(new NoteResource($note));
    }


    / **
     * Update an existing note using validated request data.
     *
     * @param Note $note The Note model instance to update.
     * @param UpdateNoteRequest $request The request containing validated update attributes.
     * @return JsonResponse A successful API response containing the updated note resource.
     */
    public function update(Note $note, UpdateNoteRequest $request): JsonResponse
    {
        return ApiResponse::success($this->noteService->update($note, $request->validated()));
    }

    /**
     * Delete the provided note and return a success API response.
     *
     * @param Note $note The Note model instance to delete.
     * @return JsonResponse A JSON API success response with no payload.
     */
    public function destroy(Note $note): JsonResponse
    {
        $this->noteService->delete($note);
        return ApiResponse::success();
    }
}
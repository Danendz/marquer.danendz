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
    public function __construct(
        protected NoteService $noteService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $notes = $this->noteService->list($userId);
        return ApiResponse::success(NoteListResource::collection($notes));
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $note = $this->noteService->create($userId, $request->validated());
        return ApiResponse::success(new NoteResource($note));
    }

    public function show(Note $note): JsonResponse
    {
        return ApiResponse::success(new NoteResource($note));
    }


    public function update(Note $note, UpdateNoteRequest $request): JsonResponse
    {
        return ApiResponse::success($this->noteService->update($note, $request->validated()));
    }

    public function destroy(Note $note): JsonResponse
    {
        $this->noteService->delete($note);
        return ApiResponse::success();
    }
}

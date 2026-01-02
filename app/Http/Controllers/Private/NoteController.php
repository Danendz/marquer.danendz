<?php

namespace App\Http\Controllers\Private;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Notes\NoteListResource;
use App\Http\Resources\Notes\NoteResource;
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

    public function show($id, Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $note = $this->noteService->get_by_id((int)$id, $userId);

        return ApiResponse::success(new NoteResource($note));
    }


    public function update($id, UpdateNoteRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $note = $this->noteService->update((int)$id, $userId, $request->validated());

        return ApiResponse::success(new NoteResource($note));
    }

    public function destroy($id, Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $this->noteService->delete((int)$id, $userId);

        return ApiResponse::success();
    }
}

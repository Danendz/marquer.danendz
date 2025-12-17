<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\NoteListResource;
use App\Http\Resources\NoteResource;
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
        $user_id = $this->get_user_id($request);

        $notes = $this->noteService->list($user_id);

        return ApiResponse::success(NoteListResource::collection($notes));
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $user_id = $this->get_user_id($request);
        $note = $this->noteService->create($user_id, $request->validated());

        return ApiResponse::success(new NoteResource($note));
    }

    public function show($id, Request $request): JsonResponse
    {
        $user_id = $this->get_user_id($request);
        $note = $this->noteService->get_by_id((int)$id, $user_id);

        return ApiResponse::success(new NoteResource($note));
    }


    public function update($id, UpdateNoteRequest $request): JsonResponse
    {
        $user_id = $this->get_user_id($request);
        $note = $this->noteService->update((int)$id, $user_id, $request->validated());

        return ApiResponse::success(new NoteResource($note));
    }

    public function destroy($id, Request $request): JsonResponse
    {
        $user_id = $this->get_user_id($request);
        $this->noteService->delete((int)$id, $user_id);

        return ApiResponse::success();
    }
}

<?php

namespace App\Http\Controllers\Private\Study;

use App\Http\Controllers\Controller;
use App\Http\Requests\Study\StoreStudySubjectRequest;
use App\Http\Requests\Study\UpdateStudySubjectRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Study\StudySubjectResource;
use App\Models\Study\StudySubject;
use App\Services\Study\StudySubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudySubjectController extends Controller
{
    public function __construct(
        protected StudySubjectService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success(StudySubjectResource::collection($this->service->list($request->user()->id)));
    }

    public function store(StoreStudySubjectRequest $request): JsonResponse
    {
        $subject = $this->service->create($request->user()->id, $request->validated());
        return ApiResponse::success(new StudySubjectResource($subject));
    }

    public function update(StudySubject $studySubject, UpdateStudySubjectRequest $request): JsonResponse
    {
        if ($studySubject->user_id === null) {
            return ApiResponse::error(message: 'Cannot modify system subjects.', status: 403);
        }

        $subject = $this->service->update($studySubject, $request->validated());
        return ApiResponse::success(new StudySubjectResource($subject));
    }

    public function destroy(StudySubject $studySubject): JsonResponse
    {
        if ($studySubject->user_id === null) {
            return ApiResponse::error(message: 'Cannot delete system subjects.', status: 403);
        }

        $this->service->delete($studySubject);
        return ApiResponse::success();
    }
}

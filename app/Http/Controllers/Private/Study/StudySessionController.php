<?php

namespace App\Http\Controllers\Private\Study;

use App\Http\Controllers\Controller;
use App\Http\Requests\Study\CompleteStudySessionRequest;
use App\Http\Requests\Study\ListStudySessionsRequest;
use App\Http\Requests\Study\StoreStudySessionRequest;
use App\Http\Requests\Study\UpdateStudySessionRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Study\StudySessionResource;
use App\Models\Study\StudySession;
use App\Services\Study\StudySessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudySessionController extends Controller
{
    public function __construct(
        protected StudySessionService $service
    ) {
    }

    public function index(ListStudySessionsRequest $request): JsonResponse
    {
        $sessions = $this->service->list($request->user()->id, $request->validated());
        return ApiResponse::success(StudySessionResource::collection($sessions));
    }

    public function store(StoreStudySessionRequest $request): JsonResponse
    {
        $session = $this->service->create($request->user()->id, $request->validated());
        return ApiResponse::success(new StudySessionResource($session));
    }

    public function update(StudySession $studySession, UpdateStudySessionRequest $request): JsonResponse
    {
        $session = $this->service->update($studySession, $request->validated());
        return ApiResponse::success(new StudySessionResource($session));
    }

    public function complete(StudySession $studySession, CompleteStudySessionRequest $request): JsonResponse
    {
        $session = $this->service->complete($studySession, $request->validated());
        return ApiResponse::success(new StudySessionResource($session));
    }

    public function cancel(StudySession $studySession): JsonResponse
    {
        $session = $this->service->cancel($studySession);
        return ApiResponse::success(new StudySessionResource($session));
    }

    public function stats(Request $request): JsonResponse
    {
        $data = $this->service->stats($request->user()->id);
        return ApiResponse::success([
            'today_total_seconds' => $data['today_total_seconds'],
            'sessions' => StudySessionResource::collection($data['sessions']),
        ]);
    }
}

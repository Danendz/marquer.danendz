<?php

namespace App\Services\Study;

use App\Enums\StudySessionStatus;
use App\Models\Study\StudySession;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

readonly class StudySessionService
{
    public function __construct(
        private RabbitPublisherService $publisher
    ) {
    }

    public function list(int $userId, array $data): Collection
    {
        $query = StudySession::with('subject')->where('user_id', $userId);

        if (!empty($data['date_from'])) {
            $query->where('started_at', '>=', $data['date_from']);
        }

        if (!empty($data['date_to'])) {
            $query->where('started_at', '<=', $data['date_to']);
        }

        if (!empty($data['study_subject_id'])) {
            $query->where('study_subject_id', $data['study_subject_id']);
        }

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        return $query->orderBy('started_at', 'desc')->get();
    }

    public function create(int $userId, array $data): StudySession
    {
        return DB::transaction(function () use ($userId, $data) {
            $active = StudySession::where('user_id', $userId)
                ->where('status', StudySessionStatus::Active)
                ->exists();

            if ($active) {
                throw ValidationException::withMessages([
                    'session' => ['You already have an active study session.'],
                ]);
            }

            $session = StudySession::create([
                ...$data,
                'user_id' => $userId,
                'started_at' => now(),
                'status' => StudySessionStatus::Active,
            ]);

            DB::afterCommit(function () use ($session) {
                $this->publisher->publishAnalytics('study.session_started', [
                    'event_name' => 'study_session_started',
                    'properties' => [
                        'study_session_id' => $session->id,
                        'timer_mode' => $session->timer_mode->value,
                        'study_subject_id' => $session->study_subject_id,
                    ],
                ]);
            });

            return $session;
        });
    }

    public function update(StudySession $session, array $data): StudySession
    {
        return DB::transaction(function () use ($session, $data) {
            $oldStatus = $session->status;
            $session->update($data);
            $newStatus = $session->fresh()->status;

            DB::afterCommit(function () use ($session, $oldStatus, $newStatus) {
                if ($newStatus === StudySessionStatus::Paused && $oldStatus === StudySessionStatus::Active) {
                    $this->publisher->publishAnalytics('study.session_paused', [
                        'event_name' => 'study_session_paused',
                        'properties' => [
                            'study_session_id' => $session->id,
                            'actual_duration_seconds' => $session->actual_duration_seconds,
                        ],
                    ]);
                } elseif ($newStatus === StudySessionStatus::Active && $oldStatus === StudySessionStatus::Paused) {
                    $this->publisher->publishAnalytics('study.session_resumed', [
                        'event_name' => 'study_session_resumed',
                        'properties' => ['study_session_id' => $session->id],
                    ]);
                }
            });

            return $session;
        });
    }

    public function complete(StudySession $session, array $data): StudySession
    {
        return DB::transaction(function () use ($session, $data) {
            $session->update([
                ...$data,
                'status' => StudySessionStatus::Completed,
                'ended_at' => now(),
            ]);

            DB::afterCommit(function () use ($session) {
                $this->publisher->publishAnalytics('study.session_completed', [
                    'event_name' => 'study_session_completed',
                    'properties' => [
                        'study_session_id' => $session->id,
                        'actual_duration_seconds' => $session->actual_duration_seconds,
                        'timer_mode' => $session->timer_mode->value,
                        'study_subject_id' => $session->study_subject_id,
                    ],
                ]);
            });

            return $session;
        });
    }

    public function cancel(StudySession $session): StudySession
    {
        return DB::transaction(function () use ($session) {
            $session->update(['status' => StudySessionStatus::Cancelled, 'ended_at' => now()]);

            DB::afterCommit(function () use ($session) {
                $this->publisher->publishAnalytics('study.session_cancelled', [
                    'event_name' => 'study_session_cancelled',
                    'properties' => ['study_session_id' => $session->id],
                ]);
            });

            return $session;
        });
    }

    public function stats(int $userId): array
    {
        $today = StudySession::where('user_id', $userId)
            ->where('status', StudySessionStatus::Completed)
            ->whereDate('started_at', today())
            ->sum('actual_duration_seconds');

        $sessions = StudySession::with('subject')
            ->where('user_id', $userId)
            ->whereIn('status', [StudySessionStatus::Completed, StudySessionStatus::Cancelled])
            ->orderBy('started_at', 'desc')
            ->limit(50)
            ->get();

        return ['today_total_seconds' => $today, 'sessions' => $sessions];
    }
}

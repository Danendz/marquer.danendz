<?php

namespace App\Services\Study;

use App\Enums\StudySessionStatus;
use App\Models\Study\StudySession;
use App\Services\AnalyticsPublisherService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

readonly class StudySessionService
{
    public function __construct(
        private AnalyticsPublisherService $publisher
    ) {
    }

    public function list(int $userId, array $data): Collection
    {
        $query = StudySession::with('subject')->where('user_id', $userId);

        if (!empty($data['date_from'])) {
            $query->where('started_at', '>=', $data['date_from']);
        }

        if (!empty($data['date_to'])) {
            $query->where('started_at', '<=', Carbon::parse($data['date_to'])->endOfDay());
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

            try {
                $session = StudySession::create([
                    ...$data,
                    'user_id' => $userId,
                    'started_at' => now(),
                    'status' => StudySessionStatus::Active,
                ]);
            } catch (UniqueConstraintViolationException) {
                throw ValidationException::withMessages([
                    'session' => ['You already have an active study session.'],
                ]);
            }

            DB::afterCommit(function () use ($session) {
                $this->publisher->publish('study_session_started', [
                    'study_session_id' => $session->id,
                    'timer_mode' => $session->timer_mode->value,
                    'study_subject_id' => $session->study_subject_id,
                ]);
            });

            return $session;
        });
    }

    public function update(StudySession $session, array $data): StudySession
    {
        if (in_array($session->status, [StudySessionStatus::Completed, StudySessionStatus::Cancelled])) {
            throw ValidationException::withMessages([
                'session' => ['Cannot update a session that is already completed or cancelled.'],
            ]);
        }

        return DB::transaction(function () use ($session, $data) {
            $oldStatus = $session->status;
            $session->update($data);
            $newStatus = $session->fresh()->status;

            DB::afterCommit(function () use ($session, $oldStatus, $newStatus) {
                if ($newStatus === StudySessionStatus::Paused && $oldStatus === StudySessionStatus::Active) {
                    $this->publisher->publish('study_session_paused', [
                        'study_session_id' => $session->id,
                        'actual_duration_seconds' => $session->actual_duration_seconds,
                    ]);
                } elseif ($newStatus === StudySessionStatus::Active && $oldStatus === StudySessionStatus::Paused) {
                    $this->publisher->publish('study_session_resumed', [
                        'study_session_id' => $session->id,
                    ]);
                }
            });

            return $session;
        });
    }

    public function complete(StudySession $session, array $data): StudySession
    {
        if (in_array($session->status, [StudySessionStatus::Completed, StudySessionStatus::Cancelled])) {
            throw ValidationException::withMessages([
                'session' => ['Session is already in a terminal state.'],
            ]);
        }

        return DB::transaction(function () use ($session, $data) {
            $session->update([
                ...$data,
                'status' => StudySessionStatus::Completed,
                'ended_at' => now(),
            ]);

            DB::afterCommit(function () use ($session) {
                $this->publisher->publish('study_session_completed', [
                    'study_session_id' => $session->id,
                    'actual_duration_seconds' => $session->actual_duration_seconds,
                    'timer_mode' => $session->timer_mode->value,
                    'study_subject_id' => $session->study_subject_id,
                ]);
            });

            return $session;
        });
    }

    public function cancel(StudySession $session): StudySession
    {
        if (in_array($session->status, [StudySessionStatus::Completed, StudySessionStatus::Cancelled])) {
            throw ValidationException::withMessages([
                'session' => ['Session is already in a terminal state.'],
            ]);
        }

        return DB::transaction(function () use ($session) {
            $session->update(['status' => StudySessionStatus::Cancelled, 'ended_at' => now()]);

            DB::afterCommit(function () use ($session) {
                $this->publisher->publish('study_session_cancelled', [
                    'study_session_id' => $session->id,
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

<?php

namespace App\Services\Study;

use App\Models\Study\StudySubject;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class StudySubjectService
{
    public function __construct(
        private RabbitPublisherService $publisher
    ) {
    }

    public function list(int $userId): Collection
    {
        return StudySubject::where(function ($q) use ($userId) {
            $q->whereNull('user_id')->orWhere('user_id', $userId);
        })->orderBy('name')->get();
    }

    public function create(int $userId, array $data): StudySubject
    {
        return DB::transaction(function () use ($userId, $data) {
            $subject = StudySubject::create([...$data, 'user_id' => $userId]);

            DB::afterCommit(function () use ($subject) {
                $this->publisher->publishAnalytics('study.subject_created', [
                    'event_name' => 'study_subject_created',
                    'properties' => ['study_subject_id' => $subject->id],
                ]);
            });

            return $subject;
        });
    }

    public function update(StudySubject $subject, array $data): StudySubject
    {
        if ($subject->user_id === null) {
            throw new HttpException(403, 'Cannot modify system subjects.');
        }

        return DB::transaction(function () use ($subject, $data) {
            $subject->update($data);

            DB::afterCommit(function () use ($subject) {
                $this->publisher->publishAnalytics('study.subject_updated', [
                    'event_name' => 'study_subject_updated',
                    'properties' => ['study_subject_id' => $subject->id],
                ]);
            });

            return $subject;
        });
    }

    public function delete(StudySubject $subject): void
    {
        if ($subject->user_id === null) {
            throw new HttpException(403, 'Cannot delete system subjects.');
        }

        DB::transaction(function () use ($subject) {
            $subject->delete();

            DB::afterCommit(function () use ($subject) {
                $this->publisher->publishAnalytics('study.subject_deleted', [
                    'event_name' => 'study_subject_deleted',
                    'properties' => ['study_subject_id' => $subject->id],
                ]);
            });
        });
    }
}

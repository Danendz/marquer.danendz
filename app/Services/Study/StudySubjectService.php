<?php

namespace App\Services\Study;

use App\Models\Study\StudySubject;
use App\Services\Concerns\PublishesAnalytics;
use App\Services\RabbitPublisherService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class StudySubjectService
{
    use PublishesAnalytics;

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
        return $this->withAnalytics('study.subject_created', 'study_subject_created', 'study_subject_id', function () use ($userId, $data) {
            return StudySubject::create([...$data, 'user_id' => $userId]);
        });
    }

    public function update(StudySubject $subject, array $data): StudySubject
    {
        if ($subject->user_id === null) {
            throw new HttpException(403, 'Cannot modify system subjects.');
        }

        return $this->withAnalytics('study.subject_updated', 'study_subject_updated', 'study_subject_id', function () use ($subject, $data) {
            $subject->update($data);
            return $subject;
        });
    }

    public function delete(StudySubject $subject): void
    {
        if ($subject->user_id === null) {
            throw new HttpException(403, 'Cannot delete system subjects.');
        }

        $this->deleteWithAnalytics($subject, 'study.subject_deleted', 'study_subject_deleted', 'study_subject_id');
    }
}

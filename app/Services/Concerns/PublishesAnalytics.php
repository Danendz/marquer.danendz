<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait PublishesAnalytics
{
    protected function withAnalytics(string $eventName, string $idKey, callable $callback): mixed
    {
        return DB::transaction(function () use ($eventName, $idKey, $callback) {
            $result = $callback();

            DB::afterCommit(function () use ($eventName, $idKey, $result) {
                $this->publisher->publish($eventName, [$idKey => $result->id]);
            });

            return $result;
        });
    }

    protected function deleteWithAnalytics(Model $model, string $eventName, string $idKey): void
    {
        $modelId = $model->id;

        DB::transaction(function () use ($model, $eventName, $idKey, $modelId) {
            $deleted = $model->delete();

            if ($deleted) {
                DB::afterCommit(function () use ($eventName, $idKey, $modelId) {
                    $this->publisher->publish($eventName, [$idKey => $modelId]);
                });
            }
        });
    }
}

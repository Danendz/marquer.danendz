<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait PublishesAnalytics
{
    protected function withAnalytics(string $routingKey, string $eventName, string $idKey, callable $callback): mixed
    {
        return DB::transaction(function () use ($routingKey, $eventName, $idKey, $callback) {
            $result = $callback();

            DB::afterCommit(function () use ($routingKey, $eventName, $idKey, $result) {
                $this->publisher->publishAnalyticsSafely($routingKey, [
                    'event_name' => $eventName,
                    'properties' => [$idKey => $result->id],
                ]);
            });

            return $result;
        });
    }

    protected function deleteWithAnalytics(Model $model, string $routingKey, string $eventName, string $idKey): void
    {
        $modelId = $model->id;

        DB::transaction(function () use ($model, $routingKey, $eventName, $idKey, $modelId) {
            $model->delete();

            DB::afterCommit(function () use ($routingKey, $eventName, $idKey, $modelId) {
                $this->publisher->publishAnalyticsSafely($routingKey, [
                    'event_name' => $eventName,
                    'properties' => [$idKey => $modelId],
                ]);
            });
        });
    }
}

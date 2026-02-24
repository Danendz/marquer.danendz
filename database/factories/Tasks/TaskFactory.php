<?php

namespace Database\Factories\Tasks;

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'status' => 'draft',
            'task_category_id' => TaskCategory::factory(),
            'user_id' => 1,
        ];
    }

    public function withoutCategory(): static
    {
        return $this->state(['task_category_id' => null]);
    }
}

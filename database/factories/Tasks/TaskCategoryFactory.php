<?php

namespace Database\Factories\Tasks;

use App\Models\Tasks\TaskCategory;
use App\Models\Tasks\TaskFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TaskCategory> */
class TaskCategoryFactory extends Factory
{
    protected $model = TaskCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'color' => '#fff',
            'task_folder_id' => TaskFolder::factory(),
            'user_id' => 1,
        ];
    }
}

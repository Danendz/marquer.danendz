<?php

namespace Database\Factories\Tasks;

use App\Models\Tasks\TaskFolder;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TaskFolder> */
class TaskFolderFactory extends Factory
{
    protected $model = TaskFolder::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'user_id' => 1,
        ];
    }
}

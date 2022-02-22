<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'title' => $this->faker->words(5, true),
            'description' => $this->faker->sentence,
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
        ];
    }
}

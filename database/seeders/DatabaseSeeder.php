<?php

namespace Database\Seeders;

use App\Models\Enums\UserRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->createOne([
            'username' => 'laravel',
            'password' => 'password',
            'role' => UserRole::ADMIN,
        ]);

        \App\Models\User::factory(10)->create();
        \App\Models\Project::factory(25)->create();
        \App\Models\Task::factory(10)->create();
    }
}

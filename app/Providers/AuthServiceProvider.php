<?php

namespace App\Providers;

use App\Models\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-user-apis', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        Gate::define('access-project-apis', function (User $user) {
            return $user->role === UserRole::PRODUCT_OWNER;
        });

        Gate::define('update-task-status', function (User $user, Task $task) {
            return $user->id === $task->user_id;
        });
    }
}

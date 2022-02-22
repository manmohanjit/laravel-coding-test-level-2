<?php

namespace Tests\Feature\Traits;

use App\Models\Enums\UserRole;
use App\Models\User;

trait WithRoles
{
    public function actingAsRole(UserRole $role)
    {
        $this->actingAs(User::factory()->createOne(['role' => $role]));
    }
}

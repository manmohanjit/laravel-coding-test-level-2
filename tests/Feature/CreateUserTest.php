<?php

namespace Tests\Feature;

use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use WithFaker, WithRoles;

    /**
     * Create user as admin, should work.
     * Everything else should fail.
     */
    protected function createUser() : TestResponse
    {
        return $this->post('/api/v1/users', [
            'username' => Str::slug($this->faker->words(2, true)),
            'password' => 'password',
            'role' => UserRole::MEMBER->value,
        ]);
    }

    public function test_create_user_as_admin_works()
    {
        $this->actingAsRole(UserRole::ADMIN);

        $this->createUser()->assertStatus(201);
    }

    public function test_create_user_as_product_owner_fails()
    {
        $this->actingAsRole(UserRole::PRODUCT_OWNER);

        $this->createUser()->assertStatus(403);
    }

    public function test_create_user_as_product_member_fails()
    {
        $this->actingAsRole(UserRole::MEMBER);

        $this->createUser()->assertStatus(403);
    }
    public function test_create_user_as_product_guest_fails()
    {
        $this->createUser()->assertStatus(401);
    }

}

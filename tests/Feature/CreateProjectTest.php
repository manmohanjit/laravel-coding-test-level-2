<?php

namespace Tests\Feature;

use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Traits\WithRoles;
use Tests\TestCase;

/**
 * Create user as admin, should work.
 * Everything else should fail.
 */
class CreateProjectTest extends TestCase
{
    use WithFaker, WithRoles;

    /**
     * Creating a project as a Product Owner should
     * work, everything else should fail.
     */
    protected function createProject() : TestResponse
    {
        return $this->post('/api/v1/projects', [
            'name' => 'Sample Project',
        ]);
    }

    public function test_create_project_as_admin_passes()
    {
        $this->actingAsRole(UserRole::ADMIN);

        $this->createProject()->assertStatus(403);
    }

    public function test_create_project_as_product_owner_fails()
    {
        $this->actingAsRole(UserRole::PRODUCT_OWNER);

        $this->createProject()->assertStatus(201);
    }

    public function test_create_project_as_product_member_fails()
    {
        $this->actingAsRole(UserRole::MEMBER);

        $this->createProject()->assertStatus(403);
    }

    public function test_create_project_as_product_guest_fails()
    {
        $this->createProject()->assertStatus(401);
    }

    /**
     * Test assigning and removing users from project,
     * should only work for Product Owners
     */
    public function test_assign_members_to_project_as_product_owner_passes()
    {
        $this->actingAsRole(UserRole::PRODUCT_OWNER);

        $members = User::factory()->count(2)->create(['role' => UserRole::MEMBER]);
        $project = Project::factory()->createOne();

        // try to assign members
        $members->each(function($member) use($project) {
            $this->put("/api/v1/projects/{$project->id}/users/{$member->id}")->assertStatus(200);

            $this->assertDatabaseHas('project_user', [
                'user_id' => $member->id,
                'project_id' => $project->id,
            ]);
        });

        // try to remove members
        $members->each(function($member) use($project) {
            $this->delete("/api/v1/projects/{$project->id}/users/{$member->id}")->assertStatus(204);

            $this->assertDatabaseMissing('project_user', [
                'user_id' => $member->id,
                'project_id' => $project->id,
            ]);
        });
    }

    /**
     * Test assigning and removing users from project,
     * should fail for non-product owners
     */
    public function test_assign_members_to_project_as_member_fails()
    {
        $this->actingAsRole(UserRole::MEMBER);

        $members = User::factory()->count(2)->create(['role' => UserRole::MEMBER]);
        $project = Project::factory()->createOne();

        // try to assign members
        $members->each(function($member) use($project) {
            $this->put("/api/v1/projects/{$project->id}/users/{$member->id}")->assertStatus(403);

            $this->assertDatabaseMissing('project_user', [
                'user_id' => $member->id,
                'project_id' => $project->id,
            ]);
        });
    }
}

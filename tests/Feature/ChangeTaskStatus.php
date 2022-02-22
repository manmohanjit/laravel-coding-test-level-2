<?php

namespace Tests\Feature;

use App\Models\Enums\TaskStatus;
use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChangeTaskStatus extends TestCase
{

    public function test_change_my_tasks_status_passes()
    {
        $member = User::factory()->createOne();

        $this->actingAs($member); // myself

        $project = Project::factory()->createOne();
        $project->users()->sync([$member->id]);

        $task = Task::factory()->for($member)->for($project)->createOne();

        $this->patch("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
            'status' => TaskStatus::IN_PROGRESS->value,
        ])->assertStatus(200);
    }

    public function test_change_someone_else_tasks_status_fails()
    {
        $member = User::factory()->createOne();

        $this->actingAs(User::factory()->createOne(['role' => UserRole::MEMBER])); // someone else

        $project = Project::factory()->createOne();
        $project->users()->sync([$member->id]);

        $task = Task::factory()->for($member)->for($project)->createOne();

        $this->patch("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
            'status' => TaskStatus::IN_PROGRESS->value,
        ])->assertStatus(403);
    }
}

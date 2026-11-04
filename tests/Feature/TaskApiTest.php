<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_list_own_tasks(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        Task::factory()->count(2)->for($user)->create();
        Task::factory()->create(); // someone else's task

        $this->postJson('/api/tasks', ['title' => 'Write tests'])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Write tests')
            ->assertJsonPath('data.status', 'todo');

        $this->getJson('/api/tasks')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_title_is_required_and_status_must_be_valid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/tasks', ['status' => 'nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_tasks_can_be_filtered_by_status(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        Task::factory()->for($user)->create(['status' => 'done']);
        Task::factory()->for($user)->create(['status' => 'todo']);

        $this->getJson('/api/tasks?status=done')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_user_can_update_and_delete_own_task(): void
    {
        Sanctum::actingAs($user = User::factory()->create());
        $task = Task::factory()->for($user)->create(['status' => 'todo']);

        $this->patchJson("/api/tasks/{$task->id}", ['status' => 'done'])
            ->assertOk()->assertJsonPath('data.status', 'done');

        $this->deleteJson("/api/tasks/{$task->id}")->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_touch_another_users_task(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $other = Task::factory()->create();

        $this->getJson("/api/tasks/{$other->id}")->assertForbidden();
        $this->patchJson("/api/tasks/{$other->id}", ['title' => 'x'])->assertForbidden();
        $this->deleteJson("/api/tasks/{$other->id}")->assertForbidden();
    }
}

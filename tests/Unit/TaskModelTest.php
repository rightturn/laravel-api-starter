<?php

namespace Tests\Unit;

use App\Models\Task;
use PHPUnit\Framework\TestCase;

class TaskModelTest extends TestCase
{
    public function test_new_tasks_default_to_todo(): void
    {
        $this->assertSame('todo', (new Task)->status);
    }

    public function test_allowed_statuses(): void
    {
        $this->assertSame(['todo', 'in_progress', 'done'], Task::STATUSES);
    }
}

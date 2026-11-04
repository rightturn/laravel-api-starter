<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const STATUSES = ['todo', 'in_progress', 'done'];

    protected $attributes = ['status' => 'todo'];

    protected $fillable = ['title', 'description', 'status', 'due_date'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

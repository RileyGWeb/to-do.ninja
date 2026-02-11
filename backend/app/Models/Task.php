<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'task_list_id', 'project_id', 'user_id', 'completed', 'sort_order', 'notes'];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function taskList()
    {
        return $this->belongsTo(TaskList::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

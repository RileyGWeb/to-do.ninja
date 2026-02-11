<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'completed', 'sort_order'];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taskLists()
    {
        return $this->hasMany(TaskList::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

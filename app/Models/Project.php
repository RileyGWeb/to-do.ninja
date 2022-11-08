<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;

    public static function create(string $name)
    {
        $newProject = new Project; 
        $newProject->name = $name;
        $newProject->user_id = Auth::id();
        $newProject->save();
    }
}

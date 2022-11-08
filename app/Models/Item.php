<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    public static function create(string $name, int $currentProjectId, int $currentListId)
    {
        $newItem = new Item; 
        $newItem->name = $name;
        $newItem->user_id = Auth::id();
        $newItem->project_id = $currentProjectId;
        $newItem->list_id = $currentListId;
        $newItem->save();
    }
}

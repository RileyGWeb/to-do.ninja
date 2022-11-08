<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ItemList extends Model
{
    use HasFactory;

    public static function create(string $name, int $currentProjectId)
    {
        $newItemList = new ItemList; 
        $newItemList->name = $name;
        $newItemList->user_id = Auth::id();
        $newItemList->project_id = $currentProjectId;
        $newItemList->save();
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\ItemList;
use Illuminate\Support\Facades\Auth;

class TaskList extends Component
{
    public $selectedProject;
    public $selectedList;
    public $tasks;

    public function render()
    {   
        $this->tasks = Item::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->where('list_id', $this->selectedList)
        ->get();
        // dd($lists, Auth::id(), $this->selectedProject);
        return view('livewire.task-list');
    }
}

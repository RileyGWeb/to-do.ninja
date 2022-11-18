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
    public $listName;
    public $tasks;
    public $new_item_name;

    public function render()
    {   
        $currentList = ItemList::where('id', $this->selectedList)->get();
        if (!count($currentList) == 0) {
            $this->listName = $currentList[0]->name;
        } 
        $this->tasks = Item::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->where('list_id', $this->selectedList)
        ->get();
        
        return view('livewire.task-list');
    }

    public function store()
    {
        if($this->new_item_name != null) {
            Item::create($this->new_item_name, $this->selectedProject, $this->selectedList);
        }
    }
}

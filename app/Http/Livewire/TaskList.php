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
    public $listId;
    public $tasks;
    public $new_item_name = null;
    public $rename_list_input;

    public $listeners = [
        'refreshTasklist'
    ];

    public function render()
    {   
        $currentList = ItemList::where('id', $this->selectedList)->get();
        if (!count($currentList) == 0) {
            $this->listName = $currentList[0]->name;
        } 
        $this->rename_list_input = $this->listName;
        $this->listId = $this->selectedList;
        $this->tasks = Item::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->where('list_id', $this->selectedList)
        ->orderby('order')
        ->get();
        
        return view('livewire.task-list');
    }

    public function store()
    {
        if($this->new_item_name != null) {
            Item::create($this->new_item_name, $this->selectedProject, $this->selectedList);
            $this->new_item_name = null;
        }
        $this->emit('refreshListItem');
        $this->emit('refreshListSection');
    }

    public function refreshTasklist($listId)
    {
        $this->selectedList = $listId;
        $this->tasks = Item::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->where('list_id', $this->selectedList)
        ->get();
    }

    public function renameList($listId)
    {
        $currentList = ItemList::where('id', $listId)
            ->where('user_id', Auth::id())
            ->get();

        $currentList[0]->name = $this->rename_list_input;
        $currentList[0]->save();
           
        $this->name = $this->rename_list_input;
        $this->emit('refreshTasklist', $listId);
        $this->emit('refreshListItem');
        $this->emit('touch');
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\ItemList;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ListItem extends Component
{
    public $name = '';
    public $projectId;
    public $listId;
    public $totalItems;
    public $totalCompleted = 0;
    public $selectedList;
    public $completed = false;
    public $touch = false;

    protected $listeners = ['refreshListItem' => '$refresh'];

    public function render()
    {
        $items = Item::where('user_id', Auth::id())
            ->where('list_id', $this->listId)
            ->get();
        $currentList = ItemList::where('user_id', Auth::id())
            ->where('id', $this->listId)
            ->get();
            
        $this->totalItems = count($items);
        $this->totalCompleted = 0;
        foreach($items as $val) {
            if ($val->completed == 1) {
                $this->totalCompleted += 1;
            }
        }
        if($this->totalItems == $this->totalCompleted && $this->totalItems > 0) {
            $this->completed = true;
            $currentList[0]->completed = 1;
            $currentList[0]->save();
        } else {
            $this->completed = false;
            $currentList[0]->completed = 0;
            $currentList[0]->save();
        }

        if($this->touch) {
            // dd($this->selectedList);
        }
        
        return view('livewire.list-item');
    }

    public function switchList($listId)
    {
        // change active list, which means refreshing the task-list component and passing a new selectedProject and selectedList
        $this->touch = true;
        $this->selectedList = $listId;
        $this->emit('refreshTasklist', $listId);
        $this->emit('updateSelectedList', $listId);
        
        // change url
    }

    public function getItems() {
        dd("fdsafasdf");
    }
}

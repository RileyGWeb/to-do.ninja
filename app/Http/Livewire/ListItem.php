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
    public $order;

    protected $listeners = ['refreshListItem' => '$refresh', 'setOrder', 'setTaskOrder'];

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
        
        return view('livewire.list-item');
    }

    public function switchList($listId)
    {
        // change active list, which means refreshing the task-list component and passing a new selectedProject and selectedList
        $this->touch = true;
        $this->selectedList = $listId;
        $this->emit('refreshTasklist', $listId);
        $this->emit('updateSelectedList', $listId);
    }

    public function setListOrder($listId, $order) {
        $list = ItemList::where('user_id', Auth::id())
        ->where('id', $listId)
        ->get();

        $list[0]->order = $order;
        $list[0]->save();
    }

    public function setTaskOrder($taskId, $order) {
        $task = Item::where('user_id', Auth::id())
        ->where('id', $taskId)
        ->get();

        $task[0]->order = $order;
        $task[0]->save();
    }
}

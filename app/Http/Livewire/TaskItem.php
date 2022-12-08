<?php

namespace App\Http\Livewire;

use App\Models\Item;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TaskItem extends Component
{
    public $name;
    public $completed;
    public $taskId;
    public $listId;
    public $deleted = false;
    public $rename_task_input;

    public function render()
    {
        $this->rename_task_input = $this->name;
        return view('livewire.task-item');
    }

    public function completeItem($taskId)
    {
        $currentItem = Item::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->get();

        if($currentItem[0]->completed == 0) {
            $currentItem[0]->completed = 1;
            $currentItem[0]->save();
            $this->completed = 1;
        } else if ($currentItem[0]->completed == 1) {
            $currentItem[0]->completed = 0;
            $currentItem[0]->save();
            $this->completed = 0;
        }

        $this->emit('refreshTasklist', $this->listId);
        $this->emit('refreshListItem');
        $this->emit('refreshListSection');

    }

    public function deleteItem($taskId)
    {
        Item::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->delete();
        
        $this->deleted = true;
        $this->emit('refreshTasklist', $this->listId);
        $this->emit('refreshListItem');
        $this->emit('refreshListSection');
    }

    public function renameItem($taskId) {
        $currentItem = Item::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->get();

        $currentItem[0]->name = $this->rename_task_input;
        $currentItem[0]->save();
           
        $this->name = $this->rename_task_input;
        $this->emit('refreshTasklist', $this->listId);
    }
}

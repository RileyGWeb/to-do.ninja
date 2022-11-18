<?php

namespace App\Http\Livewire;

use App\Models\Item;
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

    public function render()
    {
        $items = Item::where('user_id', Auth::id())
            ->where('list_id', $this->listId)
            ->get();
        $this->totalItems = count($items);
        foreach($items as $val) {
            if ($val->completed == 1) {
                $this->totalCompleted += 1;
            }
        }
        
        return view('livewire.list-item');
    }
}

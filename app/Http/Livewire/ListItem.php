<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ListItem extends Component
{
    public $name = '';
    public $projectId;
    public $listId;

    public function render()
    {
        return view('livewire.list-item');
    }
}

<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TaskItem extends Component
{
    public $name,
        $completed;

    public function render()
    {
        return view('livewire.task-item');
    }
}

<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ListSection extends Component
{
    public $lists;
    public $selectedList;
    public $selectedProject;

    protected $listeners = ['refreshListSection' => '$refresh'];

    public function render()
    {
        return view('livewire.list-section');
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use App\Models\ItemList;
use Illuminate\Support\Facades\Auth;

class ProjectComponent extends Component
{
    public $project;
    public $selectedProject;
    public $selectedList;

    public function render()
    {
        $this->project = Project::where('user_id', Auth::id())
        ->where('id', $this->selectedProject)
        ->get();
        
        return view('livewire.project-component');
    }

    public function refresh() {
        $this->emit('refreshListSection');
    }
}

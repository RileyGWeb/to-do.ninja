<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectComponent extends Component
{
    public function render()
    {
        $project = Project::where('id', 1)->get();
        return view('livewire.project-component', $project);
    }
}

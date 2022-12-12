<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProjectComponent extends Component
{
    public $project;
    public $selectedProject;
    public $selectedList;
    public $rename_project_input;

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

    public function renameProject($projectId) {
        $currentProject = Project::where('id', $projectId)
            ->where('user_id', Auth::id())
            ->get();

        $currentProject[0]->name = $this->rename_project_input;
        $currentProject[0]->save();
           
        $this->name = $this->rename_project_input;
        // $this->emit('refreshProjectlist', $this->listId);
    }
}

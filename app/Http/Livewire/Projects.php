<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Projects extends Component
{
    public $readyToLoad = false;
    public $project_name;

    protected $listeners = ['projectAdded' => '$refresh'];

    public function loadProjects()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.projects', [
            'projects' => $this->readyToLoad
                ? Project::where('user_id', Auth::id())
                ->orderBy('updated_at')
                ->get()
                : [],
        ]);
    }

    public function store()
    {
        if($this->project_name != null) {
            Project::create($this->project_name);
        }
    }
}

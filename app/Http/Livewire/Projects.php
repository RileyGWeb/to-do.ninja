<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Projects extends Component
{
    public $readyToLoad = false;

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
}

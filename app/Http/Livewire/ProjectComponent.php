<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use App\Models\ItemList;
use Illuminate\Support\Facades\Auth;

class ProjectComponent extends Component
{
    public $new_list_name;
    public $project;
    public $lists;
    public $selectedProject;
    public $selectedList;

    protected $listeners = ['refreshListSection' => '$refresh'];

    public function render()
    {
        $this->project = Project::where('user_id', Auth::id())
        ->where('id', $this->selectedProject)
        ->get();
        $this->lists = ItemList::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->orderByDesc('created_at')
        ->get();
        
        return view('livewire.project-component');
    }

    public function store()
    {
        if($this->new_list_name != null) {
            ItemList::create($this->new_list_name, $this->selectedProject);
        }
    }
}

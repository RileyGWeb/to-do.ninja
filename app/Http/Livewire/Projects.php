<?php

namespace App\Http\Livewire;

use App\Models\Item;
use App\Models\Project;
use Livewire\Component;
use App\Models\ItemList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Projects extends Component
{
    public $readyToLoad = false;
    public $project_name;
    public $totalCompleted;

    protected $listeners = ['projectAdded' => '$refresh'];

    public function loadProjects()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.projects', [
            'projects' => $this->readyToLoad
                ? $this->getProjectsData()
                : [],
        ]);
    }

    public function getProjectsData()
    {
        $projects = Project::where('user_id', Auth::id())
        ->orderBy('updated_at')
        ->get();
        $this->totalCompleted = count($projects->where('completed', 1));

        foreach($projects as $project) {
            $currentProjectLists = ItemList::where('user_id', Auth::id())
                ->where('project_id', $project->id)
                ->get();


            $completedListCount = 0;
            foreach($currentProjectLists as $list) {
                if($list->completed) {
                    $completedListCount++;
                }
            }

            $currentProjectTasks = Item::where('user_id', Auth::id())
                ->where('project_id', $project->id)
                ->get();

            $completedTaskCount = 0;
            foreach($currentProjectTasks as $task) {
                if($task->completed) {
                    $completedTaskCount++;
                }
            }

            if(count($currentProjectLists) > 0 && count($currentProjectLists) == $completedListCount) {
                $project->completed = 1;
                $project->save();
            } else {
                $project->completed = 0;
                $project->save();
            }
            
            $project->total_lists = count($currentProjectLists);
            $project->completed_lists = $completedListCount;
            $project->total_tasks = count($currentProjectTasks);
            $project->completed_tasks = $completedTaskCount;
        }
        
        return $projects;
    }

    public function store()
    {
        if($this->project_name != null) {
            Project::create($this->project_name);
        }
    }
}

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ItemList;
use Illuminate\Support\Facades\Auth;

class ListSection extends Component
{
    public $lists;
    public $selectedList;
    public $selectedProject;
    public $new_list_name;

    protected $listeners = ['refreshListSection' => '$refresh'];

    public function render()
    {
        $this->lists = ItemList::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->orderByDesc('created_at')
        ->get();
        return view('livewire.list-section');
    }

    public function store()
    {
        if($this->new_list_name != null) {
            ItemList::create($this->new_list_name, $this->selectedProject);
        }
        $this->new_list_name = null;
    }

}

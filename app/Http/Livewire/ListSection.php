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
    public $touch;

    protected $listeners = ['refreshListSection' => '$refresh', 'updateSelectedList', 'touch'];

    public function render()
    {
        $this->lists = ItemList::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->orderBy('order')
        ->get();
        if($this->touch) {
            // dd($this->lists);
        }
        return view('livewire.list-section');
    }

    public function store()
    {
        if($this->new_list_name != null) {
            ItemList::create($this->new_list_name, $this->selectedProject);
        }
        $this->new_list_name = null;
    }

    public function updateSelectedList($listId) {
        $this->selectedList = $listId;
    }
    public function touch()
    {
        $this->touch = true;
        $this->lists = ItemList::where('user_id', Auth::id())
        ->where('project_id', $this->selectedProject)
        ->orderBy('order')
        ->get();
    }
}

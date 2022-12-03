<div>
    @foreach($lists as $list)
        <livewire:list-item name="{{ $list->name }}" listId="{{ $list->id }}" selectedList="{{ $selectedList }}" projectId="{{ $selectedProject }}" wire:key="list-item-{{ $list->id }}" />
        {{ $list->completed }}
    @endforeach
</div>

<div>
    <div id="incomplete_lists">
        @foreach($lists as $list)
            @if(!$list->completed)
                <livewire:list-item name="{{ $list->name }}" listId="{{ $list->id }}" selectedList="{{ $selectedList }}" projectId="{{ $selectedProject }}" wire:key="list-item-{{ $list->id }}" />
            @endif
        @endforeach
    </div>
    
    @foreach($lists as $list)
        @if($list->completed)
            <div id="completed_seperator" class="flex items-center mb-2 mt-6">
                <hr class="w-8 border-gray-500">
                <span class="mx-2 text-gray-500">Completed</span> 
                <hr class="w-full border-gray-500">
            </div>
            @break
        @endif
    @endforeach

    <div id="completed_lists">
        @foreach($lists as $list)
            @if($list->completed)
                <livewire:list-item name="{{ $list->name }}" listId="{{ $list->id }}" selectedList="{{ $selectedList }}" projectId="{{ $selectedProject }}" wire:key="list-item-{{ $list->id }}" />
            @endif
        @endforeach
    </div>
</div>

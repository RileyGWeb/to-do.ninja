<div>
    <div id="new_list_entry" class="list flex bg-gray-200 pl-4 h-8 items-center rounded-full mb-2 hidden" >
        <form wire:submit.prevent="store">
            @csrf

            <input id="new_list_input" type="text" name="new_list_name" class="h-full border-none bg-transparent p-0 focus:border-none focus:ring-0" placeholder="Name your list..." wire:model.defer="new_list_name">
            <input type="submit" class="hidden">
        </form>
        <div id="arrow" class="ml-auto flex mr-4">
            <img src="../images/arrow-right.svg" alt="">
        </div>
    </div>

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

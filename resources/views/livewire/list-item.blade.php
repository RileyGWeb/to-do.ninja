<a wire:click="switchList({{ $listId }})" onClick="switchList({{ $listId }})" id="list" listId="{{ $listId }}" class="list flex bg-gray-200 pl-4 h-8 items-center rounded-full hover:bg-gray-300 cursor-pointer @if($selectedList == $listId) border-2 border-gray-400 shadow @endif @if($completed) text-slate-500 line-through @endif">
    <div id="list_name">{{ $name }} <span id="remaining_items" class="opacity-50"> - {{ $totalCompleted }}/{{ $totalItems }}</span></div>
    @if($completed == 0)
        <div id="arrow" class="ml-auto flex mr-4">
            <img src="../images/arrow-right.svg" alt="">
        </div>
    @elseif($completed == 1)
        <div id="checkbox" class="ml-auto mr-2.5 h-5 w-5 border-gray-200 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
            <img src="../images/Line.svg" alt="checkmark" class="h-3 w-3">
        </div>
    @endif
</a>
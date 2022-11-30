<div id="item" class="bg-white rounded-full border border-gray-300 mb-2 flex cursor-pointer @if($deleted) hidden @endif">
    <div id="completion_tap_target" class="flex grow p-2 group" wire:click="completeItem({{ $taskId }})">
        @if ($completed)
            <div id="checkbox" class="h-6 w-6 border-gray-200 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
                <img src="../images/Line.svg" alt="checkmark" class="h-4 w-4">
            </div>
        @else
            <div id="checkbox" class="h-6 w-6 border border-gray-300 flex items-center justify-center rounded-full group-hover:bg-gradient-to-r group-hover:from-[#FFE000] group-hover:to-[#FFC200] group-hover:opacity-50">
                <img src="../images/Line.svg" alt="checkmark" class="h-4 w-4 hidden group-hover:block">
            </div>
        @endif
        <div id="item_name" class="mx-2 @if($completed) text-slate-500 line-through @endif">{{ $name }}</div>
    </div>
    <div id="actions" class="ml-auto mr-2 flex gap-4 items-center">
        <div id="more">
            <img src="../images/dropdown-arrow.svg" alt="">
        </div>
        <div id="delete" class="group" wire:click="deleteItem({{ $taskId }})">
            <img src="../images/trash.svg" alt="" class="group-hover:invert-[50]">
        </div>
    </div>
</div>
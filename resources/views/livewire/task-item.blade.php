<div id="item-{{ $taskId }}" taskId="{{ $taskId }}" order="{{ $order }}" class="task @if($completed == 0) incomplete @endif bg-white rounded-full border border-gray-300 mb-2 flex cursor-pointer @if($deleted) hidden @endif">
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
    <form id="rename_task_form" wire:submit.prevent="renameItem({{ $taskId }})" class="hidden mr-2">
        @csrf

        <input id="rename_task_input" type="text" name="rename_task_input" class="h-full border-none bg-transparent p-0 focus:border-none focus:ring-0" wire:model.defer="rename_task_input">
        <input type="submit" class="hidden">
    </form>
    <div id="actions" class="ml-auto mr-2 flex gap-4 items-center">
        <div id="more" class="group" onClick="renameItem({{ $taskId }})">
            <img src="../images/edit.svg" class="group-hover:invert-[50]">
        </div>
        <div id="delete" class="group" wire:click="deleteItem({{ $taskId }})">
            <img src="../images/trash.svg" alt="" class="group-hover:invert-[50]">
        </div>
    </div>
</div>
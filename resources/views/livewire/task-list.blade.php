<div>
    <h2 class="w-full text-center text-2xl mb-6 font-julius">{{ $listName }}</h2>
    <form wire:submit.prevent="store" id="new_item" class="flex hidden" wire:ignore>
        <input type="text" id="new_item_input" name="new_item_name" class="w-full rounded-full mr-4 border-gray-300 pl-4" placeholder="It all starts with a task..." wire:model.defer.debounce.5ms="new_item_name" >
        <button type="submit" class="bg-gradient-to-r from-[#FFE000] to-[#FFC200] text-white font-bold rounded-full px-6 whitespace-nowrap" onClick="clearText()">Add task</button>
        <input type="submit" class="hidden">
    </form>
    <hr class="mx-12 my-4 border-gray-300">
    <div id="list_items">
        <div id="incomplete_tasks">
            @foreach($tasks as $task)
                @if(!$task->completed)
                    <livewire:task-item name="{{ $task->name }}" completed="{{ $task->completed }}" taskId="{{ $task->id }}" order="{{ $task->order }}" listId="{{ $selectedList }}" wire:key="task-item-{{ $task->id }}" />
                @endif
            @endforeach
        </div>
        
        @foreach($tasks as $task)
            <div id="completed_seperator" class="flex items-center mb-2 mt-6">
                <hr class="w-8 border-gray-500">
                <span class="mx-2 text-gray-500">Completed</span> 
                <hr class="w-full border-gray-500">
            </div>
            @break
        @endforeach
        
        @foreach($tasks as $task)
            @if($task->completed)
                <livewire:task-item name="{{ $task->name }}" completed="{{ $task->completed }}" taskId="{{ $task->id }}" listId="{{ $selectedList }}" wire:key="task-item-{{ $task->id }}" />
            @endif
        @endforeach
    </div>
    
    <script>
        function clearText() {
            document.getElementById("new_item_input").value = '';
        }
        clearText();
    </script>
</div>

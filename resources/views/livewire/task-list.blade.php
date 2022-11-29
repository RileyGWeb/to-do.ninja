<div>
    <h2 class="w-full text-center text-2xl mb-6 font-julius">{{ $listName }}</h2>
    <form wire:submit.prevent="store" id="new_item" class="flex hidden" wire:ignore>
        <input type="text" id="new_item_input" name="new_item_name" class="w-full rounded-full mr-4 border-gray-300 pl-4" placeholder="It all starts with a task..." wire:model.defer="new_item_name" >
        <button type="submit" class="bg-gradient-to-r from-[#FFE000] to-[#FFC200] text-white font-bold rounded-full px-6 whitespace-nowrap" onClick="clearText()">Add task</button>
        <input type="submit" class="hidden">
    </form>
    <hr class="mx-12 my-4">
    <div id="list_items">
        @foreach($tasks as $task)
            <livewire:task-item name="{{ $task->name }}" completed="{{ $task->completed }}" taskId="{{ $task->id }}" wire:key="task-item-{{ $task->id }}" />
        @endforeach
    </div>
    
    <script>
        function clearText() {
            document.getElementById("new_item_input").value = '';
        }
        clearText();
    </script>
</div>

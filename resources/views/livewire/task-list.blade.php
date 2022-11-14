<div>
    @foreach($tasks as $task)
        <h2 class="w-full text-center text-2xl mb-6 font-julius">List 1</h2>
        <div id="new_item" class="flex">
            <input type="text" class="w-full rounded-full mr-4 border-gray-300 pl-4" placeholder="It all starts with a task...">
            <button class="bg-gradient-to-r from-[#FFE000] to-[#FFC200] text-white font-bold rounded-full px-6 whitespace-nowrap">Add task</button>
        </div>
        <hr class="mx-12 my-4">
        <div id="list_items">
            <livewire:task-item />
            <div id="item" class="bg-white rounded-full border border-gray-300 p-2 flex mb-2">
                <div id="checkbox" class="h-6 w-6 border-gray-200 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
                    <img src="../images/tick.svg" alt="checkmark" class="h-4 w-4">
                </div>
                <div id="item_name" class="mx-2">task item</div>
                <div id="actions" class="ml-auto mr-2 flex gap-4 items-center">
                    <div id="more">
                        <img src="../images/dropdown-arrow.svg" alt="">
                    </div>
                    <div id="delete">
                        <img src="../images/trash.svg" alt="">
                    </div>
                </div>
            </div>
            <div id="item" class="bg-white rounded-full border border-gray-300 p-2 flex mb-2">
                <div id="checkbox" class="h-6 w-6 border border-gray-300 flex items-center justify-center rounded-full">
                    <img src="../images/Line.svg" alt="checkmark" class="h-4 w-4 hidden">
                </div>
                <div id="item_name" class="mx-2">task item</div>
                <div id="actions" class="ml-auto mr-2 flex gap-4 items-center">
                    <div id="more">
                        <img src="../images/dropdown-arrow.svg" alt="">
                    </div>
                    <div id="delete">
                        <img src="../images/trash.svg" alt="">
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

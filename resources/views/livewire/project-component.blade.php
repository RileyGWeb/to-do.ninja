<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <h1 class="text-4xl text-center mb-4">Project Name</h1>
    <div id="project_view" class="flex border border-gray-400 rounded-[2rem] p-4 sm:p-6 sm:pt-12">
        <div id="project_lists" class="flex flex-col border-r border-gray-400 pr-4 sm:pr-6">
            <div id="title_area" class="flex w-64 relative h-9 items-center mb-6">
                <h2 class="w-full text-center text-2xl">Lists</h2>
                <button id="new_list" class="h-9 w-9 bg-gray-400 text-gray-100 font-bold text-1xl rounded-full ml-auto absolute right-0">+</button>
            </div>
            <div id="project_lists" class="flex flex-col gap-4">
                <div id="list" class="flex bg-gray-200 px-6 py-2 border-2 border-gray-400 rounded-full hover:bg-gray-300 cursor-pointer">
                    <div id="list_name">List 1 <span id="remaining_items" class="opacity-50"> - 3/7</span></div>
                    <div id="arrow" class="ml-auto">></div>
                </div>
                <div id="list" class="flex bg-gray-200 px-6 py-2 rounded-full hover:bg-gray-300 cursor-pointer">
                    <div id="list_name">List 2 <span id="remaining_items" class="opacity-50"> - 3/7</span></div>
                    <div id="arrow" class="ml-auto">></div>
                </div>
            </div>
        </div>
        <div id="list_view" class="pl-4 sm:pl-6">
            <h2 class="w-full text-center text-2xl mb-6">List 1</h2>
            <div id="new_item">
                <input type="text">
                <button>Add task</button>
            </div>
            <div id="list_items">
                <div id="item">
                    <div id="checkbox"></div>
                    <div id="item_name">task item</div>
                    <div id="actions">
                        <div id="delete"></div>
                        <div id="more"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

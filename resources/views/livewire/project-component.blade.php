<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 h-full">
    <h1 class="text-4xl text-center mb-4">{{ $project[0]->name }}</h1>
    <div id="project_view" class="flex border border-gray-400 rounded-[2rem] p-4 sm:p-6 sm:pt-12 min-h-[500px]">
        <div id="project_lists" class="flex flex-col border-r border-gray-400 pr-4 sm:pr-6">
            <div id="title_area" class="flex w-64 relative h-9 items-center mb-6">
                <h2 class="w-full text-center text-2xl font-julius">Lists</h2>
                <button id="new_list" class="h-8 w-8 bg-gray-400 text-gray-100 font-bold text-xl rounded-full ml-auto absolute right-0">+</button>
            </div>
            <div id="project_lists" class="flex flex-col gap-2" >
                <div id="new_list_entry" class="flex bg-gray-200 rounded-full px-4 h-8 cursor-pointer hidden" >
                    <form wire:submit.prevent="store">
                        @csrf

                        <input id="new_list_input" type="text" name="new_list_name" class="h-full border-none bg-transparent p-0 focus:border-none focus:ring-0" placeholder="Name your list..." wire:model.defer="new_list_name">
                        <input type="submit" class="hidden">
                    </form>
                    <div id="arrow" class="ml-auto flex">
                        <img src="../images/arrow-right.svg" alt="">
                    </div>
                </div>
                @foreach($lists as $val)
                    <livewire:list-item name="{{ $val->name }}" wire:key="list-item-{{ $val->id }}" />
                @endforeach
            </div>
        </div>
        <div id="list_view" class="pl-4 sm:pl-6 w-full">
            <livewire:task-list selectedProject="{{ $selectedProject }}" selectedList="{{ $selectedList }}" />
        </div>
    </div>

    <script>
        var projectLists = document.getElementById("project_lists");
        var addListButton = document.getElementById("new_list");   
        var newListEntry = document.getElementById("new_list_entry");
        var newListInput = document.getElementById("new_list_input");

        addListButton.addEventListener("click", function() {
            newListEntry.classList.remove("hidden");
            newListInput.focus();
        });

    </script>
</div>

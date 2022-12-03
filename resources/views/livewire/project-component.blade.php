<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 h-full">
    <h1 class="text-4xl text-center mb-4">{{ $project[0]->name }}</h1>
    <div id="project_view" class="flex border border-gray-400 rounded-[2rem] p-4 sm:p-6 min-h-[500px]">
        <div id="project_lists" class="flex flex-col border-r border-gray-400 pr-4 sm:pr-6">
            <div id="title_area" class="flex w-64 relative h-9 items-center mb-6">
                <h2 class="w-full text-center text-2xl font-julius">Lists</h2>
                <button id="new_list" class="h-8 w-8 bg-gray-400 text-gray-100 font-bold text-xl rounded-full ml-auto absolute right-0">+</button>
            </div>
            <div id="project_lists" class="flex flex-col gap-2" >
                <div id="new_list_entry" class="flex bg-gray-200 rounded-full px-4 h-8 cursor-pointer hidden" >
                    <form wire:submit.prevent="store" onsubmit="formAdded()">
                        @csrf

                        <input id="new_list_input" type="text" name="new_list_name" class="h-full border-none bg-transparent p-0 focus:border-none focus:ring-0" placeholder="Name your list..." wire:model.defer="new_list_name">
                        <input type="submit" class="hidden">
                    </form>
                    <div id="arrow" class="ml-auto flex">
                        <img src="../images/arrow-right.svg" alt="">
                    </div>
                </div>
                <livewire:list-section :lists=$lists selectedList="{{ $selectedList }}" projectId="{{ $selectedProject }}" />
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
            newListInput.value = '';
            newListEntry.classList.remove("hidden");
            newListInput.focus();
        });



        function switchList(listId) {

            var allLists = document.querySelectorAll('.list');
            allLists.forEach( (list) => {
                list.classList.remove('border-2', 'border-gray-400', 'shadow');
            })
            document.querySelector('[listId="' + listId + '"]').classList.add('border-2', 'border-gray-400', 'shadow');
            console.log(listId);

            var selectedProjectId = @js($selectedProject);
            var url = '/project-' + selectedProjectId + '/list-' + listId;
            history.pushState({}, '', url);

            showTaskEntry();
        }

        function showTaskEntry() {
            var taskEntry = document.getElementById('new_item').classList.remove('hidden');  
            console.log(taskEntry)
        }
        document.querySelector(".list").addEventListener("click", function() {
            showTaskEntry();
        });
        
        // If the URL contains 'list', that means a list is selected and we need to show the "add task" area
        if (window.location.href.indexOf('list') != -1) {
            showTaskEntry();
        }

        function formAdded() {
            
        }

    </script>
</div>

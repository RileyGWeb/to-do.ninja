<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 h-full">
    <h1 class="text-4xl text-center mb-4">{{ $project[0]->name }}</h1>
    <div id="project_view" class="flex border border-gray-400 rounded-[2rem] p-4 sm:p-6 min-h-[500px]">
        <div id="project_lists" class="flex flex-col border-r border-gray-400 pr-4 sm:pr-6">
            <div id="title_area" class="flex w-64 relative h-9 items-center mb-6">
                <h2 class="w-full text-center text-2xl font-julius">Lists</h2>
                <button id="new_list" class="h-8 w-8 bg-gray-400 text-gray-100 font-bold text-xl rounded-full ml-auto absolute right-0">+</button>
            </div>
            <div id="project_lists" class="flex flex-col gap-2" >
                <livewire:list-section selectedList="{{ $selectedList }}" selectedProject="{{ $selectedProject }}" />
            </div>
        </div>
        <div id="list_view" class="pl-4 sm:pl-6 w-full relative">
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

            var selectedProjectId = @js($selectedProject);
            var url = '/project-' + selectedProjectId + '/list-' + listId;
            history.pushState({}, '', url);

            document.getElementById("incomplete_tasks").classList.add("hidden");

            showTaskEntry();
        }

        function showTaskEntry() {
            var urlSlugs = location.pathname.split('/').slice(1);
            if (urlSlugs[1].includes('list')) { 
                var taskEntry = document.getElementById('new_item').classList.remove('hidden');  
            }
        }
        document.querySelector(".list").addEventListener("click", function() {
            showTaskEntry();
        });
        
        // If the URL contains 'list', that means a list is selected and we need to show the "add task" area
        if (window.location.href.indexOf('list') != -1) {
            showTaskEntry();
        }

        function renameItem(taskId) {
            var form = document.querySelector("#item-" + taskId + " #rename_task_form");
            var formInput = document.querySelector("#item-" + taskId + " #rename_task_input");
            var taskName = document.querySelector("#item-" + taskId + " #item_name");
            var TasktapTarget = document.querySelector("#item-" + taskId + " #completion_tap_target");

            form.classList.remove("hidden");
            taskName.classList.add("hidden");
            TasktapTarget.classList.remove("grow");
            formInput.select();
        }

        // // Dragula, drag & drop and order
        // Initialize drag & drop on page load
        var drakeList = dragula([document.querySelector('#incomplete_lists')]).on('dragend', function(el) {
            setListOrders();
        });
        var drakeTasks = dragula([document.querySelector('#incomplete_tasks')]).on('dragend', function(el) {
            setTaskOrders();
        });

        // Remove and re-add drag & drop to list section
        function listAdded() {
            drakeLists.destroy();
            drakeLists = dragula([document.querySelector('#incomplete_lists')]).on('dragend', function(el) { 
                // call setListOrders() when the user drops their dragged item
                setListOrders();
            });
        }
        // Remove and re-add drag & drop to task list
        function taskAdded() {
            drakeTasks.destroy();
            drakeTasks = dragula([document.querySelector('#incomplete_tasks')]).on('dragend', function(el) { 
                // call setListOrders() when the user drops their dragged item
                setTaskOrders();
            });
        }

        // Refresh all 'order' properties on the list DOM elements, and call 'setOrder' on the component to store this order in the database
        function setListOrders() {
            var index = 0;
            var allLists = document.querySelectorAll('.list.incomplete');

            allLists.forEach( (list) => {
                list.setAttribute('order', index);
                index++;
            });

            allLists.forEach( (list) => {
                var listId = list.getAttribute('listId');
                var order = list.getAttribute('order')
                Livewire.emit('setListOrder', listId, order);
            });
        }
        // Refresh all 'order' properties on the list DOM elements, and call 'setOrder' on the component to store this order in the database
        function setTaskOrders() {
            var index = 0;
            var alltasks = document.querySelectorAll('.task.incomplete');

            alltasks.forEach( (task) => {
                task.setAttribute('order', index);
                index++;
            });

            alltasks.forEach( (task) => {
                var taskId = task.getAttribute('taskId');
                var order = task.getAttribute('order')
                Livewire.emit('setTaskOrder', taskId, order);
            });
        }
    </script>
</div>

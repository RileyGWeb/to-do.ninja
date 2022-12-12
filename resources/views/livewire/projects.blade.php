<div>
    <div class="max-w-5xl mx-auto my-12" wire:init="loadProjects()"> 
        <h1 class="text-4xl text-center mb-4">Projects</h1>
        <div id="projects" class="flex flex-col gap-4">
            <div id="incomplete_projects" class="grid grid-cols-5 gap-4">
                @foreach($projects as $project)
                    @if(!$project->completed)
                        <div class="relative" x-data="{ open: false }">
                            <a href="/project-{{ $project['id'] }}" id="project" class="peer h-full w-full border-2 border-gray-300 aspect-square rounded-[1.25rem] text-2xl flex items-center justify-center text-slate-500 cursor-pointer select-none hover:bg-gray-200 transition-all flex flex-col p-2 text-center">
                                    
                                <div class="line-clamp-3">{{ $project['name'] }}</div>
                                <div id="project_info" class="text-sm absolute bottom-0 w-full">
                                    <span class="absolute bottom-2 left-4">Lists: {{ $project['completed_lists'] }}/{{ $project['total_lists'] }}</span>
                                    <span class="absolute bottom-2 right-4">Tasks: {{ $project['completed_tasks'] }}/{{ $project['total_tasks'] }}</span>
                                </div>
                            </a>
                            <div id="menu" x-on:click="open = ! open" class="absolute top-4 right-2 hover:bg-gray-300 w-8 h-8 flex items-center justify-center rounded-full opacity-0 peer-hover:opacity-50 hover:opacity-100 z-50 cursor-pointer">
                                <svg width="6" height="20" viewBox="0 0 6 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 14.39C1.45 14.39 0.190002 15.65 0.190002 17.19C0.190002 18.74 1.45 20 3 20C4.55 20 5.81 18.74 5.81 17.19C5.81 15.65 4.55 14.39 3 14.39Z" fill="#64748b"/>
                                    <path d="M3 12.81C4.55192 12.81 5.81 11.5519 5.81 10C5.81 8.44808 4.55192 7.19 3 7.19C1.44808 7.19 0.190002 8.44808 0.190002 10C0.190002 11.5519 1.44808 12.81 3 12.81Z" fill="#64748b"/>
                                    <path d="M3 0C1.45 0 0.190002 1.26 0.190002 2.81C0.190002 4.35 1.45 5.60999 3 5.60999C4.55 5.60999 5.81 4.35 5.81 2.81C5.81 1.26 4.55 0 3 0Z" fill="#64748b"/>
                                </svg>
                            </div>
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute z-50 rounded-lg shadow-lg origin-top-right right-2 top-14">
                                <div data-modal-toggle="deleteProjectConfirmationModal" class="rounded-md bg-white p-4 border border-gray-200 select-none cursor-pointer hover:bg-gray-100">Delete</div>
                            </div>
                        </div>
                        
                    @endif
                @endforeach

                <div id="project_add" class="@if(!$projects) hidden @endif h-full w-full border-dashed border-2 border-gray-300 aspect-square rounded-[1.25rem] text-8xl flex items-center justify-center text-slate-300 cursor-pointer select-none hover:bg-gray-200 hover:text-slate-500 hover:border-gray-300 hover:border-solid transition-all" type="button" data-modal-toggle="addProjectModal"  onClick="clearText()">
                    +
                </div>
            </div>            

            <div id="completed_projects" x-data="{ open: true }">
                @foreach($projects as $project)
                    @if($project->completed)
                        <div id="completed_seperator" class="flex items-center mb-4 py-1 px-4 mt-6 rounded-lg transition cursor-pointer hover:bg-gray-200" x-on:click="open = ! open">
                            <hr class="w-8 border-gray-500">
                            <span class="mx-2 text-gray-500 whitespace-nowrap">Completed ({{ $totalCompleted }})</span> 
                            <hr class="w-full border-gray-500">
                            <div id="arrow" class="ml-auto flex w-10 justify-center rotate-90 transition" :class="open ? '' : '!rotate-180'">
                                <img src="../images/arrow-right.svg" alt="" class="w-2.5">
                            </div>
                        </div>
                        @break
                    @endif
                @endforeach

                <div class="grid grid-cols-5 gap-4" x-show="open" x-transition>
                    @foreach($projects as $project)
                        @if($project->completed)

                        <div class="relative">
                            <a href="/project-{{ $project['id'] }}" id="project" class="peer h-full w-full border-2 border-gray-300 aspect-square rounded-[1.25rem] text-2xl flex items-center justify-center text-slate-500 cursor-pointer select-none hover:bg-gray-200 transition-all flex flex-col p-2 text-center relative">
                                <div id="checkbox" class="ml-auto mr-2.5 h-8 w-8 absolute left-2 top-2 border-gray-200 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
                                    <img src="../images/Line.svg" alt="checkmark" class="h-5 w-5">
                                </div>
                                <div class="line-clamp-3 @if($project->completed) text-slate-400 line-through @endif">{{ $project['name'] }}</div>
                                <div id="project_info" class="text-sm absolute bottom-0 w-full">
                                    <span class="absolute bottom-2 left-4 @if($project->completed) text-slate-400 line-through @endif">Lists: {{ $project['completed_lists'] }}/{{ $project['total_lists'] }}</span>
                                    <span class="absolute bottom-2 right-4 @if($project->completed) text-slate-400 line-through @endif">Tasks: {{ $project['completed_tasks'] }}/{{ $project['total_tasks'] }}</span>
                                </div>
                            </a>
                            <div id="menu" class="absolute top-4 right-2 hover:bg-gray-300 w-8 h-8 flex items-center justify-center rounded-full opacity-0 peer-hover:opacity-50 hover:opacity-100 z-50 cursor-pointer">
                                <svg width="6" height="20" viewBox="0 0 6 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 14.39C1.45 14.39 0.190002 15.65 0.190002 17.19C0.190002 18.74 1.45 20 3 20C4.55 20 5.81 18.74 5.81 17.19C5.81 15.65 4.55 14.39 3 14.39Z" fill="#64748b"/>
                                    <path d="M3 12.81C4.55192 12.81 5.81 11.5519 5.81 10C5.81 8.44808 4.55192 7.19 3 7.19C1.44808 7.19 0.190002 8.44808 0.190002 10C0.190002 11.5519 1.44808 12.81 3 12.81Z" fill="#64748b"/>
                                    <path d="M3 0C1.45 0 0.190002 1.26 0.190002 2.81C0.190002 4.35 1.45 5.60999 3 5.60999C4.55 5.60999 5.81 4.35 5.81 2.81C5.81 1.26 4.55 0 3 0Z" fill="#64748b"/>
                                </svg>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Main modal -->
    <div id="addProjectModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full items-center justify-center" wire:ignore>
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Create new project
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="addProjectModal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <form class="space-y-6" wire:submit.prevent="store">
                        @csrf

                        <input wire:model="project_name" name="project_name" id="project_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Very important project" required>
                        <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-modal-toggle="addProjectModal">Oh yeah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Are you sure you want to delete? modal -->
    <div id="deleteProjectConfirmationModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full items-center justify-center" wire:ignore>
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Create new project
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="deleteProjectConfirmationModal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <form class="space-y-6" wire:submit.prevent="store">
                        @csrf

                        <input wire:model="project_name" name="project_name" id="project_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Very important project" required>
                        <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-modal-toggle="deleteProjectConfirmationModal">Oh yeah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clearText() {
            document.getElementById("project_name").value = '';
        }

        // // Dragula, drag & drop and order
        var drakeProjects = dragula([document.querySelector('#incomplete_projects')]).on('dragend', function(el) {
            setTaskOrders();
        });

        // Remove and re-add drag & drop to task list
        function projectAdded() {
            drakeProjects.destroy();
            drakeProjects = dragula([document.querySelector('#incomplete_projects')]).on('dragend', function(el) { 
                // call setListOrders() when the user drops their dragged item
                setTaskOrders();
            });
        }

        function showConfirmationModal() {

        }

        
        // Refresh all 'order' properties on the list DOM elements, and call 'setOrder' on the component to store this order in the database
        function setTaskOrders() {
            var index = 0;
            var allProjects = document.querySelectorAll('.task.incomplete');

            allProjects.forEach( (task) => {
                task.setAttribute('order', index);
                index++;
            });

            allProjects.forEach( (task) => {
                var taskId = task.getAttribute('taskId');
                var order = task.getAttribute('order')
                Livewire.emit('setTaskOrder', taskId, order);
            });
        }
    </script>
</div>
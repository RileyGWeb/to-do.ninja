<div>
    <div class="max-w-5xl mx-auto my-12" wire:init="loadProjects()"> 
        <h1 class="text-4xl text-center mb-4">Projects</h1>
        <div id="projects" class="grid grid-cols-5">
            @foreach($projects as $val)
                <div id="project" class="h-full w-full border-dashed border-2 border-gray-300 aspect-square rounded-[1.25rem] text-8xl flex items-center justify-center text-slate-300 cursor-pointer select-none hover:bg-white transition-all">
                    {{ $val['name'] }}
                </div>
            @endforeach
            <div id="project_add" class="h-full w-full border-dashed border-2 border-gray-300 aspect-square rounded-[1.25rem] text-8xl flex items-center justify-center text-slate-300 cursor-pointer select-none hover:bg-white transition-all gap-8" type="button" data-modal-toggle="addProjectModal"  onClick="clearText()">
                +
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

                        <input type="project_name" wire:model="project_name" name="project_name" id="project_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Very important project" required>
                        <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-modal-toggle="addProjectModal">Oh yeah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clearText() {
            document.getElementById("project_name").value = '';
        }
    </script>
</div>
<a href="/project-{{ $projectId }}/list-{{ $listId }}" id="list" class="flex bg-gray-200 px-4 h-8 items-center rounded-full hover:bg-gray-300 cursor-pointer @if($selectedList == $listId) border-2 border-gray-400 shadow @endif">
    <div id="list_name">{{ $name }} <span id="remaining_items" class="opacity-50"> - {{ $totalCompleted }}/{{ $totalItems }}</span></div>
    <div id="arrow" class="ml-auto flex">
        <img src="../images/arrow-right.svg" alt="">
    </div>
</a>
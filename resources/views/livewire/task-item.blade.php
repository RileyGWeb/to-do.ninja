<div id="item" class="bg-white rounded-full border border-gray-300 p-2 flex mb-2">
    @if ($completed)
        <div id="checkbox" class="h-6 w-6 border-gray-200 bg-gradient-to-r from-[#FFE000] to-[#FFC200] flex items-center justify-center rounded-full">
            <img src="../images/Line.svg" alt="checkmark" class="h-4 w-4">
        </div>
    @else
        <div id="checkbox" class="h-6 w-6 border border-gray-300 flex items-center justify-center rounded-full">
            <img src="../images/Line.svg" alt="checkmark" class="h-4 w-4 hidden">
        </div>
    @endif
    <div id="item_name" class="mx-2">{{ $name }}</div>
    <div id="actions" class="ml-auto mr-2 flex gap-4 items-center">
        <div id="more">
            <img src="../images/dropdown-arrow.svg" alt="">
        </div>
        <div id="delete">
            <img src="../images/trash.svg" alt="">
        </div>
    </div>
</div>
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    public function store(Request $request, Project $project)
    {
        if ($project->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxOrder = $project->taskLists()->max('sort_order') ?? 0;

        $taskList = $project->taskLists()->create([
            'name' => $request->name,
            'user_id' => $request->user()->id,
            'sort_order' => $maxOrder + 1,
        ]);

        $taskList->total_tasks = 0;
        $taskList->completed_tasks = 0;

        return response()->json($taskList, 201);
    }

    public function update(Request $request, TaskList $taskList)
    {
        if ($taskList->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $taskList->update($request->only(['name']));

        return response()->json($taskList);
    }

    public function destroy(Request $request, TaskList $taskList)
    {
        if ($taskList->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $taskList->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(Request $request, Project $project)
    {
        if ($project->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:task_lists,id',
        ]);

        foreach ($request->order as $index => $listId) {
            TaskList::where('id', $listId)
                ->where('user_id', $request->user()->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Reordered']);
    }
}

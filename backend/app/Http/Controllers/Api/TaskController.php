<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, TaskList $taskList)
    {
        if ($taskList->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxOrder = $taskList->tasks()->max('sort_order') ?? 0;

        $task = $taskList->tasks()->create([
            'name' => $request->name,
            'project_id' => $taskList->project_id,
            'user_id' => $request->user()->id,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'completed' => 'sometimes|boolean',
            'notes' => 'sometimes|nullable|string',
        ]);

        $task->update($request->only(['name', 'completed', 'notes']));

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(Request $request, TaskList $taskList)
    {
        if ($taskList->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:tasks,id',
        ]);

        foreach ($request->order as $index => $taskId) {
            Task::where('id', $taskId)
                ->where('user_id', $request->user()->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Reordered']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = $request->user()->projects()
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get()
            ->map(function ($project) {
                $totalLists = $project->taskLists()->count();
                $completedLists = $project->taskLists()->where('completed', true)->count();
                $totalTasks = $project->tasks()->count();
                $completedTasks = $project->tasks()->where('completed', true)->count();

                $isCompleted = $totalLists > 0 && $totalLists === $completedLists;
                if ($project->completed !== $isCompleted) {
                    $project->completed = $isCompleted;
                    $project->save();
                }

                $project->total_lists = $totalLists;
                $project->completed_lists = $completedLists;
                $project->total_tasks = $totalTasks;
                $project->completed_tasks = $completedTasks;

                return $project;
            });

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxOrder = $request->user()->projects()->max('sort_order') ?? 0;

        $project = $request->user()->projects()->create([
            'name' => $request->name,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json($project, 201);
    }

    public function show(Request $request, Project $project)
    {
        if ($project->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $project->load(['taskLists' => function ($query) {
            $query->orderBy('sort_order')->orderBy('created_at');
        }, 'taskLists.tasks' => function ($query) {
            $query->orderBy('sort_order')->orderBy('created_at');
        }]);

        $project->taskLists->each(function ($list) {
            $totalTasks = $list->tasks->count();
            $completedTasks = $list->tasks->where('completed', true)->count();
            $list->total_tasks = $totalTasks;
            $list->completed_tasks = $completedTasks;

            $isCompleted = $totalTasks > 0 && $totalTasks === $completedTasks;
            if ($list->completed !== $isCompleted) {
                $list->completed = $isCompleted;
                $list->save();
            }
        });

        $totalLists = $project->taskLists->count();
        $completedLists = $project->taskLists->where('completed', true)->count();
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('completed', true)->count();

        $project->total_lists = $totalLists;
        $project->completed_lists = $completedLists;
        $project->total_tasks = $totalTasks;
        $project->completed_tasks = $completedTasks;

        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $project->update($request->only(['name']));

        return response()->json($project);
    }

    public function destroy(Request $request, Project $project)
    {
        if ($project->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $project->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:projects,id',
        ]);

        foreach ($request->order as $index => $projectId) {
            $request->user()->projects()
                ->where('id', $projectId)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Reordered']);
    }
}

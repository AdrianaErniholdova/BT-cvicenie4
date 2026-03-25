<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index(Note $note)
    {
        $tasks = $note->tasks()->orderBy('due_at')->get();

        return response()->json([
            'note_id' => $note->id,
            'tasks' => $tasks
        ], Response::HTTP_OK);
    }

    public function store(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['required', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task = $note->tasks()->create($validated);

        return response()->json([
            'message' => 'Úloha bola úspešne vytvorená.',
            'task' => $task
        ], Response::HTTP_CREATED);
    }

    public function show(Note $note, $taskId)
    {
        $task = $note->tasks()->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Úloha nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['task' => $task], Response::HTTP_OK);
    }

    public function update(Request $request, Note $note, $taskId)
    {
        $task = $note->tasks()->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Úloha nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Úloha bola úspešne aktualizovaná.',
            'task' => $task,
        ], Response::HTTP_OK);
    }

    public function destroy(Note $note, $taskId)
    {
        $task = $note->tasks()->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Úloha nenájdená.'
            ], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json([
            'message' => 'Úloha bola úspešne odstránená.'
        ], Response::HTTP_OK);
    }
}


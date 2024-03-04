<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
class TaskController extends Controller
{

    public function index(Request $request)
    {
        $tasks = Task::with('notes')
            ->withCount('notes')
            ->whereHas('notes')
            ->orderByDesc('priority')
            ->orderByDesc('notes_count');
    if ($request->has('filter')) {
        $filters = $request->input('filter');

        // Apply filter for task status, due date, and priority
        $tasks->when(isset($filters['status']), function ($query) use ($filters) {
            $query->whereIn('status', (array) $filters['status']);
        })
        ->when(isset($filters['due_date']), function ($query) use ($filters) {
            $query->where('due_date', $filters['due_date']);
        })
        ->when(isset($filters['priority']), function ($query) use ($filters) {
            $query->whereIn('priority', (array) $filters['priority']);
        });

        // Apply filter for tasks with at least one note attached
        if (isset($filters['notes']) && $filters['notes'] === 'true') {
            $tasks->has('notes');
        }
    }
    
        $tasks = $tasks->get();
    
        return response()->json(['data' => $tasks], 200);
    }
    
    
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required|in:New,Incomplete,Complete',
            'priority' => 'required|in:High,Medium,Low',
            'notes.*.subject' => 'required|string|max:255',
            'notes.*.note' => 'required|string',
            'attachments.*' => 'required|file',
        ]);
    
        // Create the task
        $task = Task::create($request->only('subject', 'description', 'start_date', 'due_date', 'status', 'priority'));
    
        // Create notes
        foreach ($request->notes as $noteData) {

            // Store the attachments for the note
            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('attachments');
                $attachments[] = ['path' => $path];
            }
    
            // Merge attachments with note data
            $noteData['attachments'] = json_encode($attachments);

            $task->notes()->create($noteData);

        }
    
        return response()->json(['message' => 'Task created successfully'], 201);
    }
    
    

}

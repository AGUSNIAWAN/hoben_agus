<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user')->get()->groupBy('status');
        $users = User::all();
        
        return view('tasks.index', compact('tasks', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $validated['status'] = 'todo';

        Task::create($validated);

        return redirect()->back()->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:todo,in_progress,review,done'
        ]);

        $task->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
    
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus!');
    }
}

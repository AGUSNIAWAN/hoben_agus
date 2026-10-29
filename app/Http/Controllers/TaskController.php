<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return redirect()->to(route('dashboard') . '#monitoring-kegiatan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'priority' => 'nullable|in:low,medium,high',
            'deadline' => 'nullable|date',
        ]);

        $validated['status'] = 'todo';
        $validated['priority'] = $validated['priority'] ?? 'medium';

        Task::create($validated);

        return redirect()->back()->with('success', 'Kegiatan operasional berhasil ditambahkan!');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'nullable|in:todo,in_progress,review,done',
            'deadline' => 'nullable|date',
        ]);

        $task->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil diperbarui!',
                'task' => $task->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Kegiatan operasional berhasil diperbarui!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:todo,in_progress,review,done'
        ]);

        $task->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
    
    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil dihapus!'
            ]);
        }

        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus!');
    }
}

<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ProjectTask;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, ProjectTask $task)
    {
        // Verify user is assigned to this task or is team member
        if ($task->assigned_to !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke task ini');
        }

        $request->validate([
            'file' => 'required|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('task_attachments', $fileName, 'public');

        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
        ]);

        // TODO: Send notification to project team

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'attachment' => $attachment->load('user'),
            ]);
        }

        return back()->with('success', 'File berhasil diupload');
    }

    public function download(TaskAttachment $attachment)
    {
        // Verify user has access to this task
        $task = $attachment->task;
        $isMember = $task->project->teams()
            ->whereHas('members', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->exists();

        if (!$isMember && $task->assigned_to !== auth()->id()) {
            abort(403);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function destroy(TaskAttachment $attachment)
    {
        // Only uploader can delete
        if ($attachment->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus',
            ]);
        }

        return back()->with('success', 'File berhasil dihapus');
    }
}

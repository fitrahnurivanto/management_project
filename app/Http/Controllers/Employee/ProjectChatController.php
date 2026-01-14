<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectChat;
use Illuminate\Http\Request;

class ProjectChatController extends Controller
{
    public function index(Project $project)
    {
        // Allow: Team members, Project owner (client), and Admin
        $user = auth()->user();
        
        $isTeamMember = $project->teams()
            ->whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();
            
        $isProjectOwner = $project->order->client_id === $user->id;
        $isAdmin = $user->isAdmin();

        if (!$isTeamMember && !$isProjectOwner && !$isAdmin) {
            abort(403, 'Anda tidak memiliki akses ke project ini');
        }

        $chats = $project->chats()
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        if (request()->ajax()) {
            return response()->json($chats);
        }

        return view('employee.projects.chat', compact('project', 'chats'));
    }

    public function store(Request $request, Project $project)
    {
        // Allow: Team members, Project owner (client), and Admin
        $user = auth()->user();
        
        $isTeamMember = $project->teams()
            ->whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();
            
        $isProjectOwner = $project->order->client_id === $user->id;
        $isAdmin = $user->isAdmin();

        if (!$isTeamMember && !$isProjectOwner && !$isAdmin) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $chat = ProjectChat::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $chat->load('user');

        // TODO: Send notification for mentions

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'chat' => $chat,
            ]);
        }

        return back()->with('success', 'Pesan terkirim');
    }
}

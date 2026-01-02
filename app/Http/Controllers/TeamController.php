<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Show the form for creating a new team.
     */
    public function create(Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $employees = User::where('role', 'employee')->get();

        return view('admin.teams.create', compact('project', 'employees'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'required|array|min:1',
            'members.*.user_id' => 'required|exists:users,id',
            'members.*.role' => 'required|in:pic,project_manager,content_creator,developer,designer,marketing,seo_specialist,other',
            'members.*.hourly_rate' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create team
            $team = Team::create([
                'project_id' => $project->id,
                'team_name' => $validated['team_name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Add team members
            foreach ($validated['members'] as $memberData) {
                $team->members()->create([
                    'user_id' => $memberData['user_id'],
                    'role' => $memberData['role'],
                    'hourly_rate' => $memberData['hourly_rate'],
                    'assigned_at' => now(),
                ]);
            }

            // Update project status to in_progress
            if ($project->status === 'pending') {
                $project->update(['status' => 'in_progress']);
            }

            // Log activity
            \App\Models\ActivityLog::createLog(
                'create_team',
                'Team',
                $team->id,
                auth()->user()->name . ' membuat tim "' . $team->team_name . '" untuk project ' . $project->project_name
            );

            DB::commit();

            return redirect()->route('admin.projects.show', $project)
                ->with('success', 'Tim project berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove a team member.
     */
    public function removeMember(Team $team, $memberId)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $member = $team->members()->findOrFail($memberId);
        $member->delete();

        // Log activity
        \App\Models\ActivityLog::createLog(
            'remove_team_member',
            'Team',
            $team->id,
            auth()->user()->name . ' menghapus member dari tim ' . $team->team_name
        );

        return redirect()->back()->with('success', 'Member berhasil dihapus dari tim.');
    }
}

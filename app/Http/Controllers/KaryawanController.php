<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class KaryawanController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = User::where('role', 'employee');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $karyawan = $query->latest()->paginate(12)->appends($request->query());

        // Count team memberships and active projects
        foreach ($karyawan as $employee) {
            $employee->projects_count = \App\Models\TeamMember::where('user_id', $employee->id)->count();
            
            // Check if has active projects
            $employee->has_active_projects = \App\Models\Project::whereHas('teams.members', function($q) use ($employee) {
                $q->where('user_id', $employee->id);
            })->whereIn('status', ['pending', 'in_progress'])->exists();
        }

        return view('admin.karyawan.index', compact('karyawan'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.karyawan.create');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'division' => 'required|in:agency,academy',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            
            // Create image manager with GD driver
            $manager = new ImageManager(new Driver());
            $image = $manager->read($avatar);
            
            // Cover (crop and resize to fit) to 400x400
            $image->cover(400, 400);
            
            // Save with quality 85%
            $path = storage_path('app/public/avatars/' . $filename);
            $image->save($path, 85);
            
            $avatarPath = 'avatars/' . $filename;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'division' => $validated['division'],
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified employee.
     */
    public function show(User $karyawan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Get employee's projects through team memberships
        $projects = \App\Models\Project::whereHas('teams.members', function($q) use ($karyawan) {
            $q->where('user_id', $karyawan->id);
        })->with(['client', 'teams.members'])->get();

        return view('admin.karyawan.show', compact('karyawan', 'projects'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(User $karyawan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.karyawan.edit', compact('karyawan'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, User $karyawan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $karyawan->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'division' => 'required|in:agency,academy',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $karyawan->name = $validated['name'];
        $karyawan->email = $validated['email'];
        $karyawan->phone = $validated['phone'] ?? null;
        $karyawan->address = $validated['address'] ?? null;
        $karyawan->division = $validated['division'];
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($karyawan->avatar) {
                \Storage::disk('public')->delete($karyawan->avatar);
            }
            
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            
            // Create image manager with GD driver
            $manager = new ImageManager(new Driver());
            $image = $manager->read($avatar);
            
            // Cover (crop and resize to fit) to 400x400
            $image->cover(400, 400);
            
            // Save with quality 85%
            $path = storage_path('app/public/avatars/' . $filename);
            $image->save($path, 85);
            
            $karyawan->avatar = 'avatars/' . $filename;
        }
        
        if ($request->filled('password')) {
            $karyawan->password = Hash::make($validated['password']);
        }

        $karyawan->save();

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Data karyawan berhasil diupdate.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(User $karyawan)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Check if employee has active projects
        $hasActiveProjects = \App\Models\TeamMember::where('user_id', $karyawan->id)->exists();
        
        if ($hasActiveProjects) {
            return back()->with('error', 'Tidak dapat menghapus karyawan yang masih tergabung dalam project.');
        }

        // Delete avatar if exists
        if ($karyawan->avatar) {
            \Storage::disk('public')->delete($karyawan->avatar);
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}

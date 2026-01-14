<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    /**
     * Display a listing of trainers.
     */
    public function index(Request $request)
    {
        $query = Trainer::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('expertise', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trainers = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.trainer.index', compact('trainers'));
    }

    /**
     * Show the form for creating a new trainer.
     */
    public function create()
    {
        return view('admin.trainer.create');
    }

    /**
     * Store a newly created trainer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainers,email',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'expertise' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Trainer::create($validated);

        return redirect()->route('admin.trainer.index')
            ->with('success', 'Trainer berhasil ditambahkan!');
    }

    /**
     * Display the specified trainer.
     */
    public function show(Trainer $trainer)
    {
        return view('admin.trainer.show', compact('trainer'));
    }

    /**
     * Show the form for editing the specified trainer.
     */
    public function edit(Trainer $trainer)
    {
        return view('admin.trainer.edit', compact('trainer'));
    }

    /**
     * Update the specified trainer in storage.
     */
    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainers,email,' . $trainer->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'expertise' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $trainer->update($validated);

        return redirect()->route('admin.trainer.index')
            ->with('success', 'Trainer berhasil diperbarui!');
    }

    /**
     * Remove the specified trainer from storage.
     */
    public function destroy(Trainer $trainer)
    {
        $trainer->delete();

        return redirect()->route('admin.trainer.index')
            ->with('success', 'Trainer berhasil dihapus!');
    }
}

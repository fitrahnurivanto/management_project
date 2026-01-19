<?php

namespace App\Http\Controllers;

use App\Models\Clas;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ClasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        $query = Clas::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('trainer', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        
        $classes = $query->latest()->paginate(10)->withQueryString();
        
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Get all trainers from trainers table
      
        
        // Get trainer IDs yang sudah digunakan di kelas aktif
        $usedTrainerIds = Clas::whereIn('status', ['pending', 'approved'])
            ->whereNotNull('trainer_id')
            ->pluck('trainer_id')
            ->toArray();
        
        // Get all kategoris
        $kategoris = \App\Models\Kategori::orderBy('nama_kategori')->get();
        
        return view('admin.classes.create', compact( 'usedTrainerIds', 'kategoris'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'name' => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'trainer_id' => [
                'nullable',
                'exists:trainers,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $exists = Clas::where('trainer_id', $value)
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();
                        
                        if ($exists) {
                            $fail('Trainer ini sudah digunakan di kelas lain yang masih aktif.');
                        }
                    }
                },
            ],
            'price' => 'required|numeric|min:0',
            'amount' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'meet' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
            'method' => 'required|in:online,offline',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'trainer' => 'required|array|min:1',
            'trainer.*' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Set default amount = 1 untuk kategori Private
        if (!isset($validated['amount']) || empty($validated['amount'])) {
            $validated['amount'] = 1;
        }

        $validated['slug'] = Str::slug($validated['name']);
        // Total pendapatan = jumlah siswa x harga per siswa - biaya operasional
        $validated['income'] = ($validated['amount'] * $validated['price']) - $validated['cost'];
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending'; // Default status pending

        Clas::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Clas $clas)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('admin.classes.show', compact('clas'));
    }

    public function edit(Clas $clas)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Get all trainers from trainers table
       
        // Get trainer IDs yang sudah digunakan di kelas aktif (kecuali kelas ini)
    
        // Get all kategoris
        $kategoris = \App\Models\Kategori::orderBy('nama_kategori')->get();
        
        return view('admin.classes.edit', compact('clas',  'kategoris'));
    }

    public function update(Request $request, Clas $clas)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'name' => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'trainer_id' => [
                'nullable',
                'exists:trainers,id',
                function ($attribute, $value, $fail) use ($clas) {
                    if ($value) {
                        $exists = Clas::where('trainer_id', $value)
                            ->where('id', '!=', $clas->id) // Kecuali kelas yang sedang diedit
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();
                        
                        if ($exists) {
                            $fail('Trainer ini sudah digunakan di kelas lain yang masih aktif.');
                        }
                    }
                },
            ],
            'price' => 'required|numeric|min:0',
            'amount' => 'nullable|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'meet' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
            'method' => 'required|in:online,offline',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'trainer' => 'required|array|min:1',
            'trainer.*' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        // Set default amount = 1 untuk kategori Private
        if (!isset($validated['amount']) || empty($validated['amount'])) {
            $validated['amount'] = 1;
        }

        $validated['slug'] = Str::slug($validated['name']);
        // Total pendapatan = jumlah siswa x harga per siswa - biaya operasional
        $validated['income'] = ($validated['amount'] * $validated['price']) - $validated['cost'];

        $clas->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Clas $clas)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        $clas->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    public function approve(Clas $clas)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        $clas->update(['status' => 'approved']);

        return redirect()->back()
            ->with('success', 'Kelas berhasil di-approve.');
    }

    public function reject(Clas $clas)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        $clas->update(['status' => 'rejected']);

        return redirect()->back()
            ->with('success', 'Kelas berhasil di-reject.');
    }


   public function track(){

        return view('admin.tracking.index');

   }



    public function showclas(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Get categories from kategoris table
        $categories = Kategori::all();
        
        $query = Clas::where('status', 'approved');
        
        // Filter by kategori_id if provided
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        
        $approvedClasses = $query->with('kategori')->latest()->get();
        
        return view('admin.classes.showclas', compact('approvedClasses', 'categories'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display list of all services grouped by category
     */
    public function index()
    {
        $user = auth()->user();
        
        // Filter by division for non-super admin
        $services = Service::with(['category', 'packages'])
            ->when(!$user->isSuperAdmin(), function ($query) use ($user) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('division', $user->division);
                });
            })
            ->ordered()
            ->get()
            ->groupBy('category.name');
        
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show edit form for a service
     */
    public function edit(Service $service)
    {
        $service->load(['category', 'packages']);
        $categories = ServiceCategory::ordered()->get();
        
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Update service details
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|string', // Format: 1.500.000
            'category_id' => 'required|exists:service_categories,id',
            'is_active' => 'boolean',
        ]);

        // Clean price format
        $basePrice = (int) str_replace('.', '', $validated['base_price']);

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $basePrice,
            'category_id' => $validated['category_id'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil diupdate!');
    }

    /**
     * Store a new package for a service
     */
    public function storePackage(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|string', // Format: 1.500.000
            'duration_days' => 'nullable|integer|min:1',
        ]);

        // Clean price format
        $price = (int) str_replace('.', '', $validated['price']);

        $service->packages()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $price,
            'duration_days' => $validated['duration_days'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.services.edit', $service)
            ->with('success', 'Paket berhasil ditambahkan!');
    }

    /**
     * Update package details
     */
    public function updatePackage(Request $request, ServicePackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|string', // Format: 1.500.000
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Clean price format
        $price = (int) str_replace('.', '', $validated['price']);

        $package->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $price,
            'duration_days' => $validated['duration_days'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.services.edit', $package->service_id)
            ->with('success', 'Paket berhasil diupdate!');
    }

    /**
     * Delete a package
     */
    public function destroyPackage(ServicePackage $package)
    {
        $serviceId = $package->service_id;
        $package->delete();

        return redirect()->route('admin.services.edit', $serviceId)
            ->with('success', 'Paket berhasil dihapus!');
    }
}

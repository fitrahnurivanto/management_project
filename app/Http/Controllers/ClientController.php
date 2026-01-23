<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Project;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $user = auth()->user();
        
        $query = Client::with(['orders.items.service.category', 'projects']);

        // Apply division filter for agency/academy admin
        if ($user->isAgencyAdmin()) {
            // Agency admin: only see clients with agency orders
            $query->whereHas('orders', function($q) {
                $q->where(function($subQ) {
                    $subQ->where('division', 'agency')
                         ->orWhereHas('items.service.category', function($itemQ) {
                             $itemQ->where('division', 'agency');
                         });
                });
            });
        } elseif ($user->isAcademyAdmin()) {
            // Academy admin: only see clients with academy orders (including registrations)
            $query->whereHas('orders', function($q) {
                $q->where(function($subQ) {
                    $subQ->where('division', 'academy')
                         ->orWhereHas('items.service.category', function($itemQ) {
                             $itemQ->where('division', 'academy');
                         });
                });
            });
        }
        // Super admin sees all clients (no filter)
        
        // Filter by division for super admin (dropdown filter)
        if ($user->isSuperAdmin() && $request->has('division') && $request->division !== 'all') {
            $query->whereHas('orders', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->where('division', $request->division)
                         ->orWhereHas('items.service.category', function($itemQ) use ($request) {
                             $itemQ->where('division', $request->division);
                         });
                });
            });
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereHas('projects', function($q) {
                    $q->whereIn('status', ['pending', 'in_progress']);
                });
            } elseif ($request->status === 'past') {
                $query->whereDoesntHave('projects', function($q) {
                    $q->whereIn('status', ['pending', 'in_progress']);
                })->whereHas('orders');
            }
        }

        $clients = $query->latest()->paginate(15)->appends($request->query());

        // Calculate stats for each client
        foreach ($clients as $client) {
            $client->total_orders = $client->orders->count();
            $client->total_projects = $client->projects->count();
            $client->total_revenue = $client->orders->sum('total_amount');
            $client->has_active_project = $client->projects()->whereIn('status', ['pending', 'in_progress'])->exists();
        }

        return view('admin.clients.index', compact('clients', 'user'));
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $client->load(['user', 'orders.items.service', 'projects']);

        // Calculate stats
        $stats = [
            'total_orders' => $client->orders->count(),
            'total_projects' => $client->projects->count(),
            'total_revenue' => $client->orders->sum('total_amount'),
            'active_projects' => $client->projects()->whereIn('status', ['pending', 'in_progress'])->count(),
            'completed_projects' => $client->projects()->where('status', 'completed')->count(),
        ];

        return view('admin.clients.show', compact('client', 'stats'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, Client $client)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Data client berhasil diupdate.');
    }
}

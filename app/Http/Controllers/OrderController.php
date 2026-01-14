<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Service;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Order::with(['client.user', 'items.service.category']);

        // Apply division filter based on user role
        if ($user->isAgencyAdmin()) {
            // Agency admin: only see orders with agency division
            $query->where(function($q) {
                $q->where('division', 'agency')
                  ->orWhereHas('items.service.category', function($subQ) {
                      $subQ->where('division', 'agency');
                  });
            });
        } elseif ($user->isAcademyAdmin()) {
            // Academy admin: only see orders with academy division (includes registrations)
            $query->where(function($q) {
                $q->where('division', 'academy')
                  ->orWhereHas('items.service.category', function($subQ) {
                      $subQ->where('division', 'academy');
                  });
            });
        }
        // Super admin sees all orders (no filter)

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by division for super admin (dropdown filter)
        if ($user->isSuperAdmin() && $request->has('division') && $request->division !== 'all') {
            $query->where(function($q) use ($request) {
                $q->where('division', $request->division)
                  ->orWhereHas('items.service.category', function($subQ) use ($request) {
                      $subQ->where('division', $request->division);
                  });
            });
        }

        // Filter by year - default to ALL years (don't filter by default)
        $selectedYear = $request->get('year', 'all');
        if ($selectedYear !== 'all') {
            $query->whereYear(DB::raw('COALESCE(order_date, created_at)'), $selectedYear);
        }

        // Get available years from orders (with same division filter as main query)
        $availableYearsQuery = Order::selectRaw('YEAR(COALESCE(order_date, created_at)) as year');
        
        // Apply same division filter to available years
        if ($user->isAgencyAdmin()) {
            $availableYearsQuery->where(function($q) {
                $q->where('division', 'agency')
                  ->orWhereHas('items.service.category', function($subQ) {
                      $subQ->where('division', 'agency');
                  });
            });
        } elseif ($user->isAcademyAdmin()) {
            $availableYearsQuery->where(function($q) {
                $q->where('division', 'academy')
                  ->orWhereHas('items.service.category', function($subQ) {
                      $subQ->where('division', 'academy');
                  });
            });
        } elseif ($user->isSuperAdmin() && $request->has('division') && $request->division !== 'all') {
            $availableYearsQuery->where(function($q) use ($request) {
                $q->where('division', $request->division)
                  ->orWhereHas('items.service.category', function($subQ) use ($request) {
                      $subQ->where('division', $request->division);
                  });
            });
        }
        
        $availableYears = $availableYearsQuery->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();

        // Urutan prioritas:
        // 1. Data terbaru di atas (created_at DESC)
        // 2. Status pending_review priority kedua
        // 3. Order_date sebagai fallback
        $query->orderBy('created_at', 'desc')
        ->orderByRaw("CASE 
            WHEN payment_status = 'pending_review' THEN 1
            WHEN payment_status = 'pending' THEN 2
            WHEN payment_status = 'paid' THEN 3
            WHEN payment_status = 'failed' THEN 4
            ELSE 5
        END")
        ->orderBy('order_date', 'desc');

        $orders = $query->paginate(20)->appends($request->query());
        
        // Count pending review orders for badge
        $pendingQuery = Order::where('payment_status', 'pending_review');
        
        if ($user->isAgencyAdmin()) {
            $pendingQuery->whereHas('items.service.category', function($q) {
                $q->where('division', 'agency');
            });
        } elseif ($user->isAcademyAdmin()) {
            $pendingQuery->whereHas('items.service.category', function($q) {
                $q->where('division', 'academy');
            });
        }
        
        $pendingCount = $pendingQuery->count();

        return view('admin.orders.index', compact('orders', 'pendingCount', 'user', 'selectedYear', 'availableYears'));
    }

    /**
     * Show the form for creating a new order (for client).
     */
    public function create()
    {
        $services = Service::with('category')
            ->active()
            ->ordered()
            ->get()
            ->groupBy('category.name');

        return view('orders.create', compact('services'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'business_type' => 'nullable|string',
            'npwp' => 'nullable|string',
            'contact_person' => 'required|string',
            'contact_phone' => 'required|string',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.specifications' => 'nullable|string',
            'payment_method' => 'required|string',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create or update client
            $client = Client::firstOrCreate(
                ['user_id' => auth()->id()],
                [
                    'company_name' => $validated['company_name'],
                    'company_address' => $validated['company_address'],
                    'business_type' => $validated['business_type'] ?? null,
                    'npwp' => $validated['npwp'] ?? null,
                    'contact_person' => $validated['contact_person'],
                    'contact_phone' => $validated['contact_phone'],
                ]
            );

            // Upload payment proof
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

            // Calculate total amount
            $totalAmount = 0;
            $orderItems = [];

            foreach ($validated['services'] as $serviceData) {
                $service = Service::find($serviceData['service_id']);
                $quantity = $serviceData['quantity'];
                $price = $service->base_price;
                $subtotal = $price * $quantity;
                
                $totalAmount += $subtotal;
                
                $orderItems[] = [
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'specifications' => $serviceData['specifications'] ?? null,
                ];
            }

            // Create order
            $order = Order::create([
                'client_id' => $client->id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_proof' => $paymentProofPath,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat. Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['client.user', 'items.service.category', 'project']);

        // Check authorization
        if (!auth()->user()->isAdmin() && $order->client->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Confirm payment by admin.
     */
    public function confirmPayment(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Calculate paid_amount when confirming payment
            $paidAmount = $order->total_amount - ($order->remaining_amount ?? 0);
            
            // Log untuk debugging
            \Log::info('ConfirmPayment', [
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'remaining_amount' => $order->remaining_amount ?? 0,
                'calculated_paid_amount' => $paidAmount,
            ]);
            
            $order->update([
                'payment_status' => 'paid',
                'paid_amount' => $paidAmount,
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order ' . $order->order_number . ' berhasil diapprove!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal approve order: ' . $e->getMessage());
        }
    }

    /**
     * Reject order by admin.
     */
    public function rejectOrder(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $order->update([
                'payment_status' => 'rejected',
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order ' . $order->order_number . ' ditolak.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menolak order: ' . $e->getMessage());
        }
    }

    /**
     * Get installment info for modal (AJAX).
     */
    public function getInstallmentInfo(Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return response()->json([
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'installment_count' => $order->installment_count,
            'paid_installments' => $order->paid_installments,
            'installment_amount' => $order->installment_amount,
            'remaining_amount' => $order->remaining_amount,
        ]);
    }

    /**
     * Update installment payment.
     */
    public function updateInstallment(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_amount_raw' => 'required|numeric|min:1',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $paymentAmount = $validated['payment_amount_raw'];
            
            // Validate payment amount
            if ($paymentAmount > $order->remaining_amount) {
                return back()->with('error', 'Nominal pembayaran melebihi sisa yang harus dibayar (Rp ' . number_format($order->remaining_amount, 0, ',', '.') . ')');
            }

            // Upload new payment proof
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

            // Update remaining amount
            $newRemainingAmount = max(0, $order->remaining_amount - $paymentAmount);
            $newPaidInstallments = $order->paid_installments + 1;
            
            // Calculate new paid_amount (total_amount - remaining_amount)
            $newPaidAmount = $order->total_amount - $newRemainingAmount;
            
            // Log untuk debugging
            \Log::info('UpdateInstallment', [
                'order_number' => $order->order_number,
                'payment_amount' => $paymentAmount,
                'old_remaining' => $order->remaining_amount,
                'new_remaining' => $newRemainingAmount,
                'old_paid_amount' => $order->paid_amount,
                'new_paid_amount' => $newPaidAmount,
                'total_amount' => $order->total_amount,
            ]);

            // If fully paid, mark as paid
            $newPaymentStatus = ($newRemainingAmount <= 0) ? 'paid' : $order->payment_status;

            // Append notes
            $noteText = "Cicilan ke-{$newPaidInstallments}: Rp " . number_format($paymentAmount, 0, ',', '.');
            if ($validated['notes']) {
                $noteText .= " - " . $validated['notes'];
            }
            $updatedNotes = $order->notes ? $order->notes . "\n\n[" . now()->format('d/m/Y H:i') . "] " . $noteText : $noteText;

            $order->update([
                'paid_installments' => $newPaidInstallments,
                'remaining_amount' => $newRemainingAmount,
                'paid_amount' => $newPaidAmount,
                'payment_status' => $newPaymentStatus,
                'payment_proof' => $paymentProofPath, // Update with latest proof
                'notes' => $updatedNotes,
            ]);

            DB::commit();

            $message = "Pembayaran cicilan ke-{$newPaidInstallments} sebesar Rp " . number_format($paymentAmount, 0, ',', '.') . " berhasil dicatat!";
            if ($newPaymentStatus === 'paid') {
                $message .= " Order sudah LUNAS.";
            } else {
                $message .= " Sisa: Rp " . number_format($newRemainingAmount, 0, ',', '.');
            }

            return redirect()->route('admin.orders.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update pembayaran: ' . $e->getMessage());
        }
    }
}

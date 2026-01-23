<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Service;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Presenters\OrderPresenter;
use App\Notifications\OrderReceivedNotification;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Order::with(['client.user', 'items.service.category', 'items.servicePackage']);

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

        // Attach presenters to orders
        $orders->getCollection()->transform(function ($order) {
            $order->presenter = new OrderPresenter($order);
            return $order;
        });

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

            // Send notification to admins
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new OrderReceivedNotification($order));
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat. Admin akan segera memproses pesanan Anda.');

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
            
            // Generate PKS number jika belum ada
            if (empty($order->pks_number)) {
                $pksNumber = $this->generatePksNumber($order);
            } else {
                $pksNumber = $order->pks_number;
            }
            
            $order->update([
                'payment_status' => 'paid',
                'paid_amount' => $paidAmount,
                'pks_number' => $pksNumber,
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);

            // Send notification to client
            if ($order->client && $order->client->user) {
                $order->client->user->notify(new \App\Notifications\PaymentReceivedNotification($order));
            }

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

    /**
     * Show PKS form for editing before printing
     */
    public function showPksForm(Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Only allow PKS for confirmed orders
        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'PKS hanya bisa dicetak untuk order yang sudah terkonfirmasi (paid).');
        }

        $order->load(['client', 'items.service']);

        // Generate PKS number if not exists
        if (!$order->pks_number) {
            $order->pks_number = $this->generatePksNumber($order);
        }

        // Set default duration if not exists
        if (!$order->duration) {
            $order->duration = '1 (satu) bulan';
        }

        // Set PKS date to order date
        if (!$order->pks_date) {
            $order->pks_date = $order->order_date ?? $order->created_at;
        }

        // Default position for client if not exists
        $clientPosition = $order->client->position ?? 'Direktur';

        return view('admin.orders.pks-form', compact('order', 'clientPosition'));
    }

    /**
     * Generate PDF from PKS data
     */
    public function generatePks(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'pks_number' => 'required|string',
            'pks_date' => 'required|date',
            'duration' => 'required|string',
            'client_name' => 'required|string',
            'client_position' => 'required|string',
            'client_address' => 'required|string',
            'service_description' => 'required|string',
            'payment_amount' => 'required|numeric',
            'client_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Update order with PKS data
        $order->update([
            'pks_number' => $validated['pks_number'],
            'pks_date' => $validated['pks_date'],
            'duration' => $validated['duration'],
        ]);

        // Update client position if provided
        if ($request->client_position) {
            $order->client->update(['position' => $request->client_position]);
        }

        // Handle client logo upload
        if ($request->hasFile('client_logo')) {
            // Delete old logo if exists
            if ($order->client->logo) {
                \Storage::delete('public/' . $order->client->logo);
            }

            // Store new logo
            $path = $request->file('client_logo')->store('logos/clients', 'public');
            $order->client->update(['logo' => $path]);
        }

        // Get company settings
        $companyLogo = \App\Models\Setting::get('company_logo');
        $companyName = \App\Models\Setting::get('company_name', 'Creativemu');
        $companyDirector = \App\Models\Setting::get('company_director', 'Agus Susanto');
        $companyAddress = \App\Models\Setting::get('company_address', 'Jl. Gn. Bulu No.89, RT.34, Argorejo, Kec. Sedayu, Kabupaten Bantul, Yogyakarta 55752');

        // Refresh client data to get latest logo
        $order->client->refresh();

        // Prepare data for PDF
        $data = [
            'order' => $order,
            'pks_number' => $validated['pks_number'],
            'pks_date' => \Carbon\Carbon::parse($validated['pks_date'])->locale('id'),
            'duration' => $validated['duration'],
            'client_name' => $validated['client_name'],
            'client_position' => $validated['client_position'],
            'client_address' => $validated['client_address'],
            'service_description' => $validated['service_description'],
            'payment_amount' => $validated['payment_amount'],
            // Creativemu data (Pihak Pertama)
            'company_name' => $companyName,
            'company_director' => $companyDirector,
            'company_address' => $companyAddress,
            'company_logo' => $companyLogo ? public_path('storage/' . $companyLogo) : null,
            'client_logo' => $order->client->logo ? public_path('storage/' . $order->client->logo) : null,
            'signing_location' => 'Yogyakarta',
        ];

        $pdf = \PDF::loadView('admin.orders.pks-pdf', $data);
        
        $filename = 'PKS-' . $order->order_number . '-' . date('Ymd') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate unique PKS number
     */
    private function generatePksNumber($order)
    {
        // Get last PKS number
        $lastOrder = Order::whereNotNull('pks_number')
            ->whereYear('created_at', date('Y'))
            ->orderBy('pks_number', 'desc')
            ->first();

        if ($lastOrder && $lastOrder->pks_number) {
            // Extract number from format: 089/PKS/CMU/I/2026
            preg_match('/(\d+)\/PKS/', $lastOrder->pks_number, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        // Get month in Roman numerals
        $orderDate = $order->order_date ?? $order->created_at;
        $month = \Carbon\Carbon::parse($orderDate)->month;
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romanMonths[$month - 1];

        $year = \Carbon\Carbon::parse($orderDate)->year;

        return "{$newNumber}/PKS/CMU/{$romanMonth}/{$year}";
    }
}

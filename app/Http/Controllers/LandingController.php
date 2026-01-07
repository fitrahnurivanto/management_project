<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingController extends Controller
{
    public function index()
    {
        $services = Service::with(['category', 'packages' => function($query) {
                $query->active()->ordered();
            }])
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('display_order')
            ->get();

        return view('landing', compact('services'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'package_selections' => 'required|json',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:Transfer,E-Wallet,Cash',
            'payment_type' => 'required|in:full,installment',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'package_selections.required' => 'Silakan pilih minimal satu paket layanan'
        ]);

        $packageSelections = json_decode($validated['package_selections'], true);
        
        if (empty($packageSelections)) {
            return back()->withErrors(['package_selections' => 'Silakan pilih minimal satu paket layanan'])->withInput();
        }

        // 1. Create client (public orders don't need user account)
        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'],
            'address' => 'N/A', // Will be filled later if needed
        ]);

        // 2. Calculate total from selected packages
        $totalAmount = 0;
        $orderItems = [];
        
        foreach ($packageSelections as $selection) {
            $package = ServicePackage::find($selection['package_id']);
            if ($package) {
                $totalAmount += $package->price;
                $orderItems[] = [
                    'service_id' => $selection['service_id'],
                    'service_package_id' => $package->id,
                    'package_name' => $package->name,
                    'quantity' => 1,
                    'price' => $package->price,
                    'subtotal' => $package->price,
                ];
            }
        }

        // 3. Handle payment proof upload
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // 4. Calculate installment details if payment type is installment
        $paymentType = $validated['payment_type'];
        $installmentCount = null;
        $paidInstallments = 0;
        $installmentAmount = null;
        $remainingAmount = null;

        if ($paymentType === 'installment') {
            // DP 50% + 1x pelunasan = 2 cicilan total
            $installmentCount = 2;
            $installmentAmount = $totalAmount / 2; // Each installment is 50%
            $paidInstallments = $paymentProofPath ? 1 : 0; // If upload proof, DP already paid
            $remainingAmount = $totalAmount - ($paidInstallments * $installmentAmount);
        }

        // Calculate paid_amount (amount already paid)
        $paidAmount = $totalAmount - ($remainingAmount ?? 0);

        // 5. Create order with pending_review status
        $order = Order::create([
            'client_id' => $client->id,
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'payment_status' => 'pending_review', // Admin review for both full & installment
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $paymentProofPath,
            'payment_type' => $paymentType,
            'installment_count' => $installmentCount,
            'paid_installments' => $paidInstallments,
            'installment_amount' => $installmentAmount,
            'remaining_amount' => $remainingAmount,
            'notes' => $validated['notes'],
        ]);

        // 6. Create order items with package info
        foreach ($orderItems as $item) {
            OrderItem::create(array_merge(['order_id' => $order->id], $item));
        }

        // 7. Success message with payment type info
        $successMessage = 'Terima kasih! Pesanan Anda telah diterima dengan nomor: ' . $order->order_number;
        
        if ($paymentType === 'installment') {
            $successMessage .= '. Tim kami akan menghubungi Anda untuk konfirmasi jadwal pelunasan cicilan.';
        } else {
            $successMessage .= '. Tim kami akan segera memverifikasi pembayaran Anda.';
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Show academy registration form
     */
    public function showAcademyRegisterForm()
    {
        $academyServices = Service::whereHas('category', function($query) {
                $query->where('name', 'Academy');
            })
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('academy-register', compact('academyServices'));
    }

    /**
     * Handle academy registration submission
     */
    public function submitAcademyRegistration(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'age' => 'required|integer|min:15|max:100',
            'address' => 'required|string',
            'education' => 'required|string|max:100',
            'employment_status' => 'required|string|max:100',
            'program' => 'required|exists:services,id',
            'motivation' => 'required|string',
            'experience' => 'nullable|string',
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'phone.required' => 'Nomor WhatsApp wajib diisi',
            'age.required' => 'Usia wajib diisi',
            'age.min' => 'Usia minimal 15 tahun',
            'age.max' => 'Usia tidak valid',
            'address.required' => 'Alamat lengkap wajib diisi',
            'education.required' => 'Pendidikan terakhir wajib dipilih',
            'employment_status.required' => 'Status pekerjaan wajib dipilih',
            'program.required' => 'Program academy wajib dipilih',
            'program.exists' => 'Program yang dipilih tidak valid',
            'motivation.required' => 'Motivasi mengikuti program wajib diisi',
        ]);

        // Get selected program/service
        $service = Service::findOrFail($validated['program']);

        // Create client profile
        $client = Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => null,
            'address' => $validated['address'],
        ]);

        // Create order for academy registration
        $order = Order::create([
            'client_id' => $client->id,
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => $service->base_price ?? 0,
            'paid_amount' => 0,
            'payment_status' => 'pending_review',
            'payment_method' => 'Transfer',
            'payment_type' => 'full',
            'notes' => "PENDAFTARAN ACADEMY\n\n" .
                       "Usia: {$validated['age']} tahun\n" .
                       "Pendidikan: {$validated['education']}\n" .
                       "Status Pekerjaan: {$validated['employment_status']}\n\n" .
                       "MOTIVASI:\n{$validated['motivation']}\n\n" .
                       ($validated['experience'] ? "PENGALAMAN:\n{$validated['experience']}" : ''),
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'service_package_id' => null,
            'package_name' => 'Pendaftaran Academy',
            'quantity' => 1,
            'price' => $service->base_price ?? 0,
            'subtotal' => $service->base_price ?? 0,
        ]);

        $successMessage = 'Terima kasih telah mendaftar! Nomor pendaftaran Anda: ' . $order->order_number . 
                         '. Tim kami akan menghubungi Anda via WhatsApp maksimal 2x24 jam untuk konfirmasi dan informasi lebih lanjut.';

        return redirect()->back()->with('success', $successMessage);
    }
}

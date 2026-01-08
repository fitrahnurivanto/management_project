<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Show magang registration form
     */
    public function magangForm()
    {
        return view('admin.registrations.magang');
    }

    /**
     * Show sertifikasi registration form
     */
    public function sertifikasiForm()
    {
        return view('admin.registrations.sertifikasi');
    }

    /**
     * Store magang registration
     */
    public function storeMagang(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'institution_name' => 'required|string|max:255',
            'address' => 'required|string',
            'age' => 'required|integer|min:15|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create or update client
            $client = Client::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]
            );

            // Generate unique order number
            do {
                $lastOrder = Order::whereDate('created_at', today())
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();
                $nextNumber = $lastOrder ? ((int)substr($lastOrder->order_number, -4) + 1) : 1;
                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            } while (Order::where('order_number', $orderNumber)->exists());

            // Create registration as order
            $registration = Order::create([
                'client_id' => $client->id,
                'division' => 'academy',
                'order_type' => 'registration',
                'registration_type' => 'magang',
                'order_number' => $orderNumber,
                'institution_name' => $validated['institution_name'],
                'participant_address' => $validated['address'],
                'participant_age' => $validated['age'],
                'total_amount' => 0,
                'paid_amount' => 0,
                'payment_status' => 'paid', // Gratis = sudah lunas
                'payment_type' => 'full',
                'notes' => $validated['notes'] ?? 'Pendaftaran Magang Gratis',
            ]);

            DB::commit();

            // Return JSON for AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pendaftaran magang berhasil! Nomor pendaftaran: ' . $orderNumber
                ]);
            }

            return redirect()->route('home')
                ->with('success', 'Pendaftaran magang berhasil! Nomor pendaftaran: ' . $orderNumber);

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendaftar: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }

    /**
     * Store sertifikasi registration
     */
    public function storeSertifikasi(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'institution_name' => 'required|string|max:255',
            'address' => 'required|string',
            'age' => 'required|integer|min:17|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create or update client
            $client = Client::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]
            );

            // Generate unique order number
            do {
                $lastOrder = Order::whereDate('created_at', today())
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();
                $nextNumber = $lastOrder ? ((int)substr($lastOrder->order_number, -4) + 1) : 1;
                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            } while (Order::where('order_number', $orderNumber)->exists());

            // Create registration as order
            $registration = Order::create([
                'client_id' => $client->id,
                'division' => 'academy',
                'order_type' => 'registration',
                'registration_type' => 'sertifikasi',
                'order_number' => $orderNumber,
                'institution_name' => $validated['institution_name'],
                'participant_address' => $validated['address'],
                'participant_age' => $validated['age'],
                'total_amount' => 0,
                'paid_amount' => 0,
                'payment_status' => 'paid', // Gratis = sudah lunas
                'payment_type' => 'full',
                'notes' => $validated['notes'] ?? 'Pendaftaran Sertifikasi BNSP Gratis',
            ]);

            DB::commit();

            // Return JSON for AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pendaftaran sertifikasi berhasil! Nomor pendaftaran: ' . $orderNumber
                ]);
            }

            return redirect()->route('home')
                ->with('success', 'Pendaftaran sertifikasi berhasil! Nomor pendaftaran: ' . $orderNumber);

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendaftar: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
    }
}

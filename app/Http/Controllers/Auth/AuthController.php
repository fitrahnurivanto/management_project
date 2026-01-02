<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Log activity
            \App\Models\ActivityLog::createLog(
                'login',
                'User',
                auth()->id(),
                auth()->user()->name . ' berhasil login'
            );

            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show register form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.register');
    }

    /**
     * Handle register request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create user with client role by default
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'phone' => $request->phone,
        ]);

        // Create client profile if company info provided
        if ($request->filled('company_name')) {
            \App\Models\Client::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'contact_person' => $request->name,
                'contact_phone' => $request->phone,
            ]);
        }

        // Log activity
        \App\Models\ActivityLog::createLog(
            'register',
            'User',
            $user->id,
            $user->name . ' mendaftar sebagai client baru'
        );

        // Auto login after register
        Auth::login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di sistem kami.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        \App\Models\ActivityLog::createLog(
            'logout',
            'User',
            auth()->id(),
            auth()->user()->name . ' logout'
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Redirect to appropriate dashboard based on role.
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    }
}

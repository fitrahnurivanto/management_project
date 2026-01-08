<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClasController extends Controller
{
    public function index()
    {
        // Ambil user yang sedang login
        $user = auth()->user();
        
        // Pastikan hanya academy admin atau super admin yang bisa akses
        if (!$user->canAccessAcademy()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Sementara kirim array kosong untuk $classes
        // Nanti akan diisi dari database ketika model Class sudah dibuat
        $classes = [];
        
        return view('admin.classes.index', compact('classes'));
    }
}

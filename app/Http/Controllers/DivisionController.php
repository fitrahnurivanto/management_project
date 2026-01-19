<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Set active division preference
     */
    public function setDivision(Request $request)
    {
        $division = $request->input('division', 'agency');
        
        // Validate division
        if (!in_array($division, ['agency', 'academy'])) {
            $division = 'agency';
        }
        
        // Store in session
        session(['active_division' => $division]);
        
        // Redirect to dashboard with division parameter to sync
        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard', ['division' => $division]);
        }
        
        return redirect()->back();
    }
}

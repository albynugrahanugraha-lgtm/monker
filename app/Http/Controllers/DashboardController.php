<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return match (auth()->user()->role) {
            'superadmin'   => redirect()->route('superadmin.dashboard'),
            'admin'        => redirect()->route('admin.dashboard'),
            'teknisi'      => redirect()->route('teknisi.dashboard'),
            'teknisi_cctv' => redirect()->route('teknisi_cctv.dashboard'),
            default        => abort(403, 'Role tidak dikenali.'),
        };
    }
}
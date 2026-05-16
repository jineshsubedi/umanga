<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return match (auth()->user()->role) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            'admin'       => redirect()->route('admin.dashboard'),
            'manager'     => redirect()->route('manager.meeting-minutes.index'),
            'client'      => redirect()->route('client.meeting-minutes.index'),
            default       => abort(403),
        };
    }
}

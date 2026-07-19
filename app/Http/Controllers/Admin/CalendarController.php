<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Get memos (exclude draft)
        $memos = DB::table('meeting_memos')
            ->where('company_id', $companyId)
            ->where('status', '!=', 'draft')
            ->select('id', 'title', 'status', 'meeting_date')
            ->get();

        // Get procurement requests
        $procurements = DB::table('procurement_requests')
            ->where('company_id', $companyId)
            ->select('id', 'item_name', 'quantity', 'estimated_cost', 'status', 'date', 'created_at')
            ->get();

        return Inertia::render('Admin/Calendar/Index', [
            'memos' => $memos,
            'procurements' => $procurements,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (in_array($user->role, ['manager', 'staff'])) {
            $memos = \App\Models\MeetingMemo::with(['creator', 'currentStep'])
                ->where('company_id', $user->company_id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($memo) {
                    return [
                        'id' => $memo->id,
                        'title' => $memo->title,
                        'type' => 'Memo',
                        'status' => $memo->status,
                        'creator' => $memo->creator->name ?? 'Unknown',
                        'current_step' => $memo->currentStep->step_title ?? null,
                        'created_at' => $memo->created_at,
                        'created_at_human' => $memo->created_at->diffForHumans(),
                        'url' => route('memos.show', $memo->id),
                    ];
                });

            $procurements = \App\Models\ProcurementRequest::with(['requester', 'currentStep'])
                ->where('company_id', $user->company_id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($proc) {
                    return [
                        'id' => $proc->id,
                        'title' => $proc->item_name,
                        'type' => 'Procurement',
                        'status' => $proc->status,
                        'creator' => $proc->requester->name ?? 'Unknown',
                        'current_step' => $proc->currentStep->step_title ?? null,
                        'created_at' => $proc->created_at,
                        'created_at_human' => $proc->created_at->diffForHumans(),
                        'url' => route('procurement.show', $proc->id),
                    ];
                });

            $activities = $memos->concat($procurements)
                ->sortByDesc('created_at')
                ->take(5)
                ->values()
                ->map(function ($activity) {
                    // Update created_at to use the human readable format for the frontend
                    $activity['created_at'] = $activity['created_at_human'];
                    unset($activity['created_at_human']);
                    return $activity;
                });

            return \Inertia\Inertia::render('Dashboard', [
                'activities' => $activities
            ]);
        }

        return match ($user->role) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            'admin'       => redirect()->route('admin.dashboard'),
            default       => abort(403),
        };
    }
}
    
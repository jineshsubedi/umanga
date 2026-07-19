<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role !== 'super_admin') {
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

            $memosQuery = \App\Models\MeetingMemo::where('company_id', $user->company_id);
            $procurementsQuery = \App\Models\ProcurementRequest::where('company_id', $user->company_id);

            if ($user->role === 'staff') {
                $memosQuery->where('created_by', $user->id);
                $procurementsQuery->where('requested_by', $user->id);
            }

            $memosStats = [
                'total' => (clone $memosQuery)->count(),
                'pending' => (clone $memosQuery)->where('status', 'like', 'pending%')->count(),
                'approved' => (clone $memosQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $memosQuery)->where('status', 'rejected')->count(),
            ];

            $procurementsStats = [
                'total' => (clone $procurementsQuery)->count(),
                'pending' => (clone $procurementsQuery)->where('status', 'Pending')->count(),
                'approved' => (clone $procurementsQuery)->where('status', 'Approved')->count(),
                'rejected' => (clone $procurementsQuery)->whereIn('status', ['Rejected', 'Returned'])->count(),
            ];

            $chartData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $memosChartQuery = \App\Models\MeetingMemo::where('company_id', $user->company_id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);

                $procurementsChartQuery = \App\Models\ProcurementRequest::where('company_id', $user->company_id)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);

                if ($user->role === 'staff') {
                    $memosChartQuery->where('created_by', $user->id);
                    $procurementsChartQuery->where('requested_by', $user->id);
                }

                $chartData[] = [
                    'label' => $date->format('M'),
                    'memos' => $memosChartQuery->count(),
                    'procurements' => $procurementsChartQuery->count(),
                ];
            }

            return \Inertia\Inertia::render('Dashboard', [
                'activities' => $activities,
                'memosStats' => $memosStats,
                'procurementsStats' => $procurementsStats,
                'chartData' => $chartData,
            ]);
        }

        return match ($user->role) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            default       => abort(403),
        };
    }
}
    
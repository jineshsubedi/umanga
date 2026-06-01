<?php

namespace App\Http\Middleware;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $todayAttendance = null;
        if ($request->user() && $request->user()->role !== 'super_admin') {
            $todayAttendance = Attendance::where('user_id', $request->user()->id)
                ->whereDate('date', Carbon::today())
                ->first();
        }

        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        if (!isset($appSettings['app_name'])) {
            $appSettings['app_name'] = config('app.name');
        }

        return [
            ...parent::share($request),
            'app_settings' => $appSettings,
            'auth' => [
                'user' => $request->user() ? [
                    'id'             => $request->user()->id,
                    'name'           => $request->user()->name,
                    'email'          => $request->user()->email,
                    'role'           => $request->user()->role,
                    'designation'    => $request->user()->designation,
                    'signature_path' => $request->user()->signature_path ?? null,
                    'company_id'     => $request->user()->company_id,
                    'email_notifications' => $request->user()->email_notifications,
                    'database_notifications' => $request->user()->database_notifications,
                    'company'        => $request->user()->company ? [
                        'name' => $request->user()->company->name,
                    ] : null,
                ] : null,
                'notifications' => $request->user() ? $request->user()->unreadNotifications()->take(5)->get() : [],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'today_attendance' => $todayAttendance ? [
                'id'        => $todayAttendance->id,
                'clock_in'  => $todayAttendance->clock_in,
                'clock_out' => $todayAttendance->clock_out,
                'formatted_clock_in'  => $todayAttendance->formatted_clock_in,
                'formatted_clock_out' => $todayAttendance->formatted_clock_out,
            ] : null,
        ];
    }
}

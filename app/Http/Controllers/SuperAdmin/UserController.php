<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('company:id,name')
            ->where('role', '!=', 'super_admin')
            ->withCount('meetingMemos')
            ->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('SuperAdmin/Users/Index', [
            'users'     => $users,
            'companies' => $companies,
            'filters'   => $request->only('company_id', 'role', 'status', 'search'),
        ]);
    }

    public function toggleStatus(User $user)
    {
        abort_if($user->role === 'super_admin', 403);
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);
        return back()->with('success', "User status updated successfully.");
    }
}

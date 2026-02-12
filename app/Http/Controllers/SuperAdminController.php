<?php

namespace App\Http\Controllers;

use App\Models\PrsRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Notifications\SuperadminOtpNotification;

class SuperAdminController extends Controller
{
    private const OTP_TTL_MINUTES = 5;
    private const OTP_CACHE_KEY = 'superadmin_otp:%s';

    private function ensureSuperadmin(): void
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'superadmin') {
            abort(403, 'Only superadmins can perform this action.');
        }
    }

    /**
     * Verify re-auth: valid OTP only (OTP expires in 5 minutes).
     */
    private function verifyReauth(Request $request): void
    {
        $user = auth()->user();
        $otp = $request->input('otp');

        if (empty($otp)) {
            throw ValidationException::withMessages([
                'otp' => ['Please enter the OTP sent to your email.'],
            ]);
        }

        $cacheKey = sprintf(self::OTP_CACHE_KEY, $user->id);
        if (Cache::get($cacheKey) === $otp) {
            Cache::forget($cacheKey);
            return;
        }

        throw ValidationException::withMessages([
            'otp' => ['Invalid or expired OTP. Please request a new code.'],
        ]);
    }

    /**
     * Send OTP to current superadmin's email.
     */
    public function sendOtp(Request $request)
    {
        $this->ensureSuperadmin();
        $user = auth()->user();
        $otp = (string) random_int(100000, 999999);
        $cacheKey = sprintf(self::OTP_CACHE_KEY, $user->id);
        Cache::put($cacheKey, $otp, now()->addMinutes(self::OTP_TTL_MINUTES));
        $user->notify(new SuperadminOtpNotification($otp));
        return response()->json(['message' => 'OTP sent to your email.']);
    }

    /**
     * Admin Management - list superadmin & approver users
     */
    public function admins()
    {
        $admins = User::whereIn('role', ['superadmin', 'approver'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at']);

        return view('superadmin.admins', compact('admins'));
    }

    /**
     * Approvers - list users with approver role only
     */
    public function approvers()
    {
        $approvers = User::where('role', 'approver')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'created_at']);

        return view('superadmin.approvers', compact('approvers'));
    }

    /**
     * All Requests - list all PRS requests
     */
    public function allRequests()
    {
        $requests = PrsRequest::with(['user', 'approvedBy', 'rejectedBy'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('superadmin.all-requests', compact('requests'));
    }

    /**
     * System Reports
     */
    public function reports()
    {
        $pendingCount = PrsRequest::where('status', 'Pending')->count();
        $approvedThisMonth = PrsRequest::where('status', 'Approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();
        $rejectedThisMonth = PrsRequest::where('status', 'Rejected')
            ->whereMonth('rejected_at', now()->month)
            ->whereYear('rejected_at', now()->year)
            ->count();
        $byStatus = PrsRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('superadmin.reports', compact(
            'pendingCount', 'approvedThisMonth', 'rejectedThisMonth', 'byStatus'
        ));
    }

    /**
     * System Settings (placeholder)
     */
    public function settings()
    {
        return view('superadmin.settings');
    }

    /**
     * Add admin/approver user (requires re-auth).
     */
    public function addUser(Request $request)
    {
        $this->ensureSuperadmin();
        $this->verifyReauth($request);

        $valid = $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:superadmin,approver',
        ]);

        $defaultPassword = Str::random(10);
        // Name will be set from their Google profile when they first sign in
        $placeholderName = Str::before($valid['email'], '@');
        User::create([
            'name' => $placeholderName,
            'email' => $valid['email'],
            'password' => Hash::make($defaultPassword),
            'role' => $valid['role'],
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.admins')
            ->with('message', 'User added. Send them the default password securely.');
    }

    /**
     * Update user role (requires re-auth).
     */
    public function updateRole(Request $request, User $user)
    {
        $this->ensureSuperadmin();
        $this->verifyReauth($request);

        if (! in_array($user->role, ['superadmin', 'approver'], true)) {
            return redirect()->route('superadmin.admins')->with('error', 'Can only change role for admins/approvers.');
        }

        $valid = $request->validate(['role' => 'required|in:superadmin,approver']);
        $user->update(['role' => $valid['role']]);

        return redirect()->route('superadmin.admins')->with('message', 'Role updated.');
    }

    /**
     * Deactivate user (requires re-auth).
     */
    public function deactivate(Request $request, User $user)
    {
        $this->ensureSuperadmin();
        $this->verifyReauth($request);

        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.admins')->with('error', 'You cannot deactivate yourself.');
        }
        $user->update(['is_active' => false]);
        return redirect()->route('superadmin.admins')->with('message', 'User deactivated.');
    }

    /**
     * Reactivate user (requires re-auth).
     */
    public function reactivate(Request $request, User $user)
    {
        $this->ensureSuperadmin();
        $this->verifyReauth($request);
        $user->update(['is_active' => true]);
        return redirect()->route('superadmin.admins')->with('message', 'User reactivated.');
    }
}

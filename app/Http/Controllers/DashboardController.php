<?php

namespace App\Http\Controllers;

use App\Models\PrsRequest;
use App\Models\RequestAction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * User Panel Dashboard
     */
    public function user()
    {
        $userId = auth()->id();
        if (! $userId) {
            return view('dashboards.user', [
                'activeRequests' => [],
                'pendingActions' => [],
                'completed' => [],
            ]);
        }

        $activeRequests = PrsRequest::where('user_id', $userId)
            ->whereIn('status', ['Pending', 'Processing', 'In Review'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->request_id,
                'pk' => $r->id,
                'item' => $r->item_name,
                'quantity' => $r->quantity ?? 1,
                'date' => $r->created_at->format('Y-m-d'),
                'status' => $r->status,
            ])
            ->toArray();

        $pendingActions = RequestAction::with('request')
            ->whereHas('request', fn ($r) => $r->where('user_id', $userId)->whereIn('status', ['Pending', 'Processing', 'In Review']))
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit(10)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->request_id,
                'action' => $a->description,
                'due' => $a->due_date ? $a->due_date->format('Y-m-d') : '-',
            ])
            ->toArray();

        $completed = PrsRequest::where('user_id', $userId)
            ->whereIn('status', ['Approved'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->request_id,
                'item' => $r->item_name,
                'quantity' => $r->quantity ?? 1,
                'date' => $r->updated_at->format('Y-m-d'),
            ])
            ->toArray();

        return view('dashboards.user', compact('activeRequests', 'pendingActions', 'completed'));
    }

    /**
     * Super Admin Dashboard (Overview with summary stats and charts)
     */
    public function superadmin()
    {
        $totalAdmins = User::whereIn('role', ['superadmin', 'approver'])->count();
        $pendingApprovals = PrsRequest::where('status', 'Pending')->count();
        $approvedRequests = PrsRequest::where('status', 'Approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();
        $byStatus = PrsRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Requests per day for the last 7 days (for bar chart)
        $requestsLast7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $requestsLast7Days[] = [
                'label' => $date->format('M j'),
                'count' => PrsRequest::whereDate('created_at', $date)->count(),
            ];
        }

        return view('dashboards.superadmin', compact(
            'totalAdmins', 'pendingApprovals', 'approvedRequests', 'byStatus', 'requestsLast7Days'
        ));
    }

    /**
     * Approver Dashboard
     * ?tab=pending | approved | (empty = dashboard with recent pending)
     */
    public function approver()
    {
        $tab = request()->query('tab', '');
        $pendingRequests = PrsRequest::notArchived()->where('status', 'Pending')->count();
        $approvedToday = PrsRequest::notArchived()->where('status', 'Approved')
            ->whereDate('approved_at', today())
            ->count();
        $completed = PrsRequest::notArchived()->whereIn('status', ['Approved', 'Rejected'])->count();

        if ($tab === 'pending') {
            $listRequests = PrsRequest::with('user')
                ->notArchived()
                ->where('status', 'Pending')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'request_id' => $r->request_id,
                    'approved_id' => $r->approved_id,
                    'requestor' => $r->user?->name ?? 'Unknown',
                    'item' => $r->item_name,
                    'quantity' => $r->quantity ?? 1,
                    'description' => $r->description ?? null,
                    'date' => $r->created_at->format('Y-m-d'),
                    'status' => $r->status,
                ])
                ->toArray();
        } elseif ($tab === 'approved') {
            $statusFilter = request()->query('status', 'all');
            $query = PrsRequest::with(['user', 'approvedBy', 'rejectedBy'])
                ->notArchived()
                ->whereIn('status', ['Approved', 'Rejected']);
            if ($statusFilter === 'approved') {
                $query->where('status', 'Approved');
            } elseif ($statusFilter === 'rejected') {
                $query->where('status', 'Rejected');
            }
            $listRequests = $query
                ->orderByRaw('COALESCE(approved_at, rejected_at) DESC')
                ->limit(50)
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'request_id' => $r->request_id,
                    'approved_id' => $r->approved_id,
                    'requestor' => $r->user?->name ?? 'Unknown',
                    'item' => $r->item_name,
                    'quantity' => $r->quantity ?? 1,
                    'description' => $r->description ?? null,
                    'date' => $r->created_at->format('Y-m-d'),
                    'status' => $r->status,
                    'decided_at' => $r->approved_at?->format('M d, Y H:i') ?? $r->rejected_at?->format('M d, Y H:i'),
                    'decided_by' => $r->status === 'Approved' ? ($r->approvedBy?->name ?? '—') : ($r->rejectedBy?->name ?? '—'),
                    'rejection_reason' => $r->rejection_reason,
                ])
                ->toArray();
        } else {
            $listRequests = PrsRequest::with('user')
                ->notArchived()
                ->where('status', 'Pending')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'request_id' => $r->request_id,
                    'approved_id' => $r->approved_id,
                    'requestor' => $r->user?->name ?? 'Unknown',
                    'item' => $r->item_name,
                    'quantity' => $r->quantity ?? 1,
                    'description' => $r->description ?? null,
                    'date' => $r->created_at->format('Y-m-d'),
                    'status' => $r->status,
                ])
                ->toArray();
        }

        $statusFilter = $tab === 'approved' ? request()->query('status', 'all') : 'all';
        return view('dashboards.approver', compact('pendingRequests', 'approvedToday', 'completed', 'listRequests', 'tab', 'statusFilter'));
    }

    /**
     * Approve a request
     */
    public function approveRequest(Request $request, $id)
    {
        $prsRequest = PrsRequest::findOrFail($id);

        if ($prsRequest->status !== 'Pending') {
            $msg = "Request {$prsRequest->request_id} is no longer pending.";
            return $this->approverRedirect($msg, 'error');
        }

        $prsRequest->update([
            'status' => 'Approved',
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
            'approved_id' => PrsRequest::generateApprovedId(),
        ]);

        RequestAction::where('request_id', $prsRequest->id)->update(['status' => 'completed']);

        return $this->approverRedirect("Request {$prsRequest->request_id} approved.");
    }

    /**
     * Reject a request
     */
    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $prsRequest = PrsRequest::findOrFail($id);

        if ($prsRequest->status !== 'Pending') {
            $msg = "Request {$prsRequest->request_id} is no longer pending.";
            return $this->approverRedirect($msg, 'error');
        }

        $prsRequest->update([
            'status' => 'Rejected',
            'rejected_by_id' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        RequestAction::where('request_id', $prsRequest->id)->update(['status' => 'completed']);

        return $this->approverRedirect("Request {$prsRequest->request_id} rejected.");
    }

    private function approverRedirect(string $message, string $key = 'message')
    {
        $tab = request('tab', '');
        $append = $tab !== '' ? '?tab=' . urlencode($tab) : '';
        if (request()->routeIs('approver.guest.*')) {
            return redirect()->to(route('approver.guest') . $append)->with($key, $message);
        }
        return redirect()->to(route('approver.dashboard') . $append)->with($key, $message);
    }
}

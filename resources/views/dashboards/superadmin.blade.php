@extends('layouts.superadmin')
@section('title', 'Dashboard')
@push('styles')
<style>
    .sa-overview-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }
    .sa-kpi {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 11px;
    }
    .sa-kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
    .sa-kpi-icon .material-icons { font-size: 18px; }
    .sa-kpi-icon.total { background: #2563eb; }
    .sa-kpi-icon.pending { background: #d97706; }
    .sa-kpi-icon.approved { background: #10b981; }
    .sa-kpi-icon.rejected { background: #ef4444; }
    .sa-kpi-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .sa-kpi-value {
        font-size: 22px;
        line-height: 0.95;
        font-weight: 800;
        color: #0f172a;
    }

    .sa-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        gap: 10px;
        margin-bottom: 10px;
    }
    .sa-stack {
        display: grid;
        gap: 8px;
        width: 100%;
        max-width: 290px;
        justify-self: end;
    }
    .sa-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        padding: 12px;
    }
    .sa-panel-recent {
        border-radius: 12px;
        padding: 12px;
    }
    .sa-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .sa-panel-head h2 {
        font-size: 14px;
        color: #0f172a;
        font-weight: 600;
    }
    .sa-subtle {
        font-size: 11px;
        color: #64748b;
    }

    .sa-table-wrap {
        overflow-x: auto;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
    }
    .sa-table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
    }
    .sa-table th,
    .sa-table td {
        text-align: left;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
    }
    .sa-table th {
        background: #f8fafc;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 11px;
        color: #475569;
        font-weight: 700;
    }
    .sa-table tr:last-child td { border-bottom: none; }
    .sa-panel-recent .sa-table-wrap {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }
    .sa-panel-recent .sa-table {
        min-width: 100%;
    }
    .sa-panel-recent .sa-table thead th {
        background: #f3f4f6;
        color: #4b5563;
        font-size: 10px;
        letter-spacing: 0.08em;
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
    }
    .sa-panel-recent .sa-table tbody td {
        padding: 11px 14px;
        border-bottom: 1px solid #eceff3;
        color: #334155;
        font-size: 12px;
    }
    .sa-panel-recent .sa-table tbody tr:last-child td {
        border-bottom: none;
    }
    .sa-panel-recent .sa-badge {
        min-width: 78px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .sa-panel-recent .sa-approved {
        background: #dfeee9;
        color: #2f7f68;
    }
    .sa-panel-recent .sa-pending {
        background: #f5ecdf;
        color: #a6673b;
    }
    .sa-panel-recent .sa-rejected {
        background: #f9dfe2;
        color: #b34856;
    }

    .sa-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 84px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .sa-approved { background: rgba(16, 185, 129, 0.14); color: #047857; }
    .sa-pending { background: rgba(245, 158, 11, 0.16); color: #b45309; }
    .sa-rejected { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }

    .sa-donut-wrap {
        display: grid;
        justify-items: center;
        gap: 8px;
    }
    .sa-donut {
        width: 124px;
        height: 124px;
        border-radius: 50%;
        display: grid;
        place-items: center;
    }
    .sa-donut-center {
        width: 74px;
        height: 74px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .sa-donut-center strong {
        font-size: 16px;
        color: #0f172a;
        line-height: 1;
    }
    .sa-donut-center span {
        margin-top: 2px;
        color: #64748b;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .sa-quote-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
    }
    .sa-quote-table th,
    .sa-quote-table td {
        padding: 5px 6px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 10px;
        color: #334155;
        text-align: left;
    }
    .sa-quote-table th {
        background: #f8fafc;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.04em;
    }
    .sa-quote-table tr:last-child td { border-bottom: none; }

    .sa-alerts {
        display: grid;
        gap: 6px;
    }
    .sa-alert {
        background: #f1f5f9;
        border: 1px solid #dbe3ee;
        border-radius: 16px;
        padding: 0;
    }
    .sa-alert-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
        text-decoration: none;
        color: inherit;
        padding: 7px 9px;
        border-radius: 16px;
        transition: background 0.2s ease;
    }
    .sa-alert-link:hover {
        background: #e2e8f0;
    }
    .sa-alert-left {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #0f172a;
        font-size: 11px;
        font-weight: 400;
    }
    .sa-alert-left .material-icons {
        font-size: 16px;
    }
    .sa-alert-icon-approved { color: #16a34a; }
    .sa-alert-icon-pending { color: #d97706; }
    .sa-alert-icon-rejected { color: #dc2626; }
    .sa-alert-id {
        white-space: nowrap;
        font-weight: 400;
    }
    .sa-alert-meta {
        font-size: 10px;
        color: #64748b;
        white-space: nowrap;
    }
    .sa-empty {
        text-align: center;
        color: #64748b;
        font-size: 13px;
        padding: 16px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
    }
    .sa-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .sa-quick-links a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        border-radius: 8px;
        padding: 6px 8px;
        font-size: 11px;
        font-weight: 600;
    }
    .sa-quick-links a .material-icons { font-size: 14px !important; }
    .sa-quick-primary { background: #2563eb; color: #fff; }
    .sa-quick-outline { background: #fff; color: #2563eb; border: 1px solid #2563eb; }
    .sa-quick-muted { background: #f1f5f9; color: #334155; }

    @media (max-width: 1200px) {
        .sa-overview-kpis { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
        .sa-layout { grid-template-columns: 1fr; }
        .sa-stack { max-width: 100%; justify-self: stretch; }
    }
    @media (max-width: 768px) {
        .sa-overview-kpis { grid-template-columns: 1fr; }
        .sa-panel { padding: 14px; border-radius: 14px; }
        .sa-table { min-width: 560px; }
        .sa-table th, .sa-table td { padding: 10px 10px; font-size: 12px; }
        .sa-quick-links { flex-direction: row; }
        .sa-quick-links a { justify-content: center; }
        .sa-kpi-value { font-size: 28px; }
    }
</style>
@endpush
@section('content')
@php
    $statusSummary = $statusSummary ?? ['approved' => 0, 'pending' => 0, 'rejected' => 0];
    $approvedCount = (int) ($statusSummary['approved'] ?? 0);
    $pendingCount = (int) ($statusSummary['pending'] ?? 0);
    $rejectedCount = (int) ($statusSummary['rejected'] ?? 0);
    $totalRequests = (int) ($totalRequests ?? ($approvedCount + $pendingCount + $rejectedCount));
    $chartTotal = max(1, $approvedCount + $pendingCount + $rejectedCount);
    $approvedStop = ($approvedCount / $chartTotal) * 360;
    $pendingStop = $approvedStop + (($pendingCount / $chartTotal) * 360);

    $statusBadge = function ($status) {
        $normalized = strtolower((string) $status);
        if (str_contains($normalized, 'approv')) {
            return 'sa-approved';
        }
        if (str_contains($normalized, 'reject')) {
            return 'sa-rejected';
        }
        return 'sa-pending';
    };
    $statusIcon = function ($status) {
        $normalized = strtolower((string) $status);
        if (str_contains($normalized, 'approv')) {
            return ['name' => 'check_circle', 'class' => 'sa-alert-icon-approved'];
        }
        if (str_contains($normalized, 'reject')) {
            return ['name' => 'cancel', 'class' => 'sa-alert-icon-rejected'];
        }
        return ['name' => 'schedule', 'class' => 'sa-alert-icon-pending'];
    };
@endphp
<div class="header-section">
    <h1>Superadmin Procurement Overview</h1>
    <p>Track procurement operations, quote outcomes, and approvals across the system.</p>
</div>

<div class="sa-overview-kpis">
    <div class="sa-kpi">
        <div class="sa-kpi-icon total"><span class="material-icons">inventory_2</span></div>
        <div>
            <div class="sa-kpi-label">Total Requests</div>
            <div class="sa-kpi-value">{{ $totalRequests }}</div>
        </div>
    </div>
    <div class="sa-kpi">
        <div class="sa-kpi-icon pending"><span class="material-icons">pending_actions</span></div>
        <div>
            <div class="sa-kpi-label">Pending</div>
            <div class="sa-kpi-value">{{ $pendingCount }}</div>
        </div>
    </div>
    <div class="sa-kpi">
        <div class="sa-kpi-icon approved"><span class="material-icons">assignment_turned_in</span></div>
        <div>
            <div class="sa-kpi-label">Approved</div>
            <div class="sa-kpi-value">{{ $approvedCount }}</div>
        </div>
    </div>
    <div class="sa-kpi">
        <div class="sa-kpi-icon rejected"><span class="material-icons">cancel</span></div>
        <div>
            <div class="sa-kpi-label">Rejected</div>
            <div class="sa-kpi-value">{{ $rejectedCount }}</div>
        </div>
    </div>
</div>

<div class="sa-layout">
    <div class="sa-panel sa-panel-recent">
        <div class="sa-panel-head">
            <h2>Recent Requests</h2>
                <span class="sa-subtle">Latest 10 records</span>
        </div>
        <div class="sa-table-wrap">
            <table class="sa-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Requestor</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests ?? [] as $request)
                    <tr>
                        <td>{{ $request['id'] ?? '-' }}</td>
                        <td>{{ $request['requestor'] ?? '-' }}</td>
                        <td>{{ $request['item'] ?? '-' }}</td>
                        <td>{{ $request['quantity'] ?? 1 }}</td>
                        <td>{{ $request['date'] ?? '-' }}</td>
                        <td><span class="sa-badge {{ $statusBadge($request['status'] ?? 'Pending') }}">{{ $request['status'] ?? 'Pending' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6"><div class="sa-empty">No request history available.</div></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="sa-stack">
        <div class="sa-panel">
            <div class="sa-panel-head">
                <h2>Quote Status</h2>
                <span class="sa-subtle">Compact status summary</span>
            </div>
            <div class="sa-donut-wrap">
                <div class="sa-donut" style="background: conic-gradient(#10b981 0deg {{ number_format($approvedStop, 2, '.', '') }}deg, #f59e0b {{ number_format($approvedStop, 2, '.', '') }}deg {{ number_format($pendingStop, 2, '.', '') }}deg, #ef4444 {{ number_format($pendingStop, 2, '.', '') }}deg 360deg);">
                    <div class="sa-donut-center">
                        <strong>{{ $totalRequests }}</strong>
                        <span>Total</span>
                    </div>
                </div>
                <table class="sa-quote-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="sa-badge sa-approved">Approved</span></td>
                            <td>{{ $approvedCount }}</td>
                        </tr>
                        <tr>
                            <td><span class="sa-badge sa-pending">Pending</span></td>
                            <td>{{ $pendingCount }}</td>
                        </tr>
                        <tr>
                            <td><span class="sa-badge sa-rejected">Rejected</span></td>
                            <td>{{ $rejectedCount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sa-panel">
            <div class="sa-panel-head">
                <h2>Approved Alerts</h2>
                <span class="sa-subtle">Most recent notifications</span>
            </div>
            <div class="sa-alerts">
                @forelse(array_slice($approvedAlerts ?? [], 0, 5) as $alert)
                @php $icon = $statusIcon($alert['status'] ?? 'Pending'); @endphp
                <div class="sa-alert">
                    <a href="{{ $alert['url'] ?? route('superadmin.requests') }}" class="sa-alert-link">
                        <div class="sa-alert-left">
                            <span class="material-icons {{ $icon['class'] }}">{{ $icon['name'] }}</span>
                            <span><span class="sa-alert-id">{{ $alert['id'] }}</span> · {{ $alert['item'] }}</span>
                        </div>
                        <span class="sa-alert-meta">{{ $alert['date'] }}</span>
                    </a>
                </div>
                @empty
                <div class="sa-empty">No request alerts available.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="sa-panel" style="margin-bottom: 20px;">
    <div class="sa-panel-head">
        <h2>Active Requests Pending</h2>
        <span class="sa-subtle">Requests currently in progress</span>
    </div>
    <div class="sa-table-wrap">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Requestor</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Submitted</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activePendingRequests ?? [] as $request)
                <tr>
                    <td>{{ $request['id'] ?? '-' }}</td>
                    <td>{{ $request['requestor'] ?? '-' }}</td>
                    <td>{{ $request['item'] ?? '-' }}</td>
                    <td>{{ $request['quantity'] ?? 1 }}</td>
                    <td>{{ $request['date'] ?? '-' }}</td>
                    <td><span class="sa-badge {{ $statusBadge($request['status'] ?? 'Pending') }}">{{ $request['status'] ?? 'Pending' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"><div class="sa-empty">No active pending requests.</div></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="sa-panel">
    <div class="sa-panel-head">
        <h2>Quick Actions</h2>
    </div>
    <div class="sa-quick-links">
        <a href="{{ route('superadmin.requests') }}" class="sa-quick-primary"><span class="material-icons">inventory_2</span> View all requests</a>
        <a href="{{ route('superadmin.admins') }}" class="sa-quick-outline"><span class="material-icons">groups</span> Manage users</a>
        <a href="{{ route('superadmin.approvers') }}" class="sa-quick-outline"><span class="material-icons">verified_user</span> Approvers</a>
        <a href="{{ route('superadmin.settings') }}" class="sa-quick-muted"><span class="material-icons">settings</span> Settings</a>
    </div>
</div>
@endsection

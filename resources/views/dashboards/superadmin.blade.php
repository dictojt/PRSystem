@extends('layouts.superadmin')
@section('title', 'Dashboard')
@push('styles')
<style>
    .sa-overview-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
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
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 11px;
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
    /* Match Quote Status badge size to Recent Requests badges */
    .sa-quote-table .sa-badge {
        min-width: 78px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
    }

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
        gap: 8px;
    }
    .sa-quick-links a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .sa-quick-links a .material-icons { font-size: 16px !important; }
    .sa-quick-primary { background: #2563eb; color: #fff; }
    .sa-quick-outline { background: #fff; color: #2563eb; border: 1px solid #2563eb; }
    .sa-quick-muted { background: #f1f5f9; color: #334155; }

    @media (max-width: 1200px) {
        .sa-overview-kpis { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
        .sa-layout { grid-template-columns: 1fr; }
        .sa-stack { max-width: 100%; justify-self: stretch; }
    }
    @media (max-width: 768px) {
        .sa-overview-kpis { grid-template-columns: 1fr; max-width: 100%; min-width: 0; }
        .sa-kpi { padding: 14px; min-width: 0; }
        .sa-kpi-value { font-size: 20px; }
        .sa-panel { padding: 14px; border-radius: 14px; max-width: 100%; min-width: 0; overflow-x: hidden; box-sizing: border-box; }
        .sa-layout { gap: 14px; max-width: 100%; min-width: 0; }
        .sa-stack { max-width: 100%; justify-self: stretch; min-width: 0; }
        /* Card-style table: fits in viewport, no horizontal scroll (same as approver/users) */
        .sa-table-wrap {
            overflow-x: hidden;
            border: none;
            margin: 0;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        .sa-table {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        .sa-table thead { display: none; }
        .sa-table tbody tr {
            display: block;
            margin-bottom: 12px;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            max-width: 100%;
            box-sizing: border-box;
        }
        .sa-table tbody tr:last-child { margin-bottom: 0; }
        .sa-table tbody td {
            display: block;
            padding: 6px 0 8px;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            text-align: left;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .sa-table tbody td:last-child { border-bottom: none; padding-bottom: 0; }
        .sa-table tbody td::before {
            content: attr(data-label);
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 2px;
        }
        .sa-table tbody tr.sa-empty-row,
        .sa-table tbody tr:has(td[colspan]) { padding: 14px; }
        .sa-table tbody tr.sa-empty-row td,
        .sa-table tbody tr:has(td[colspan]) td { display: block; padding: 0; border: none; }
        .sa-table tbody tr.sa-empty-row td::before,
        .sa-table tbody tr:has(td[colspan]) td::before { display: none; }
        .sa-table tbody tr.sa-empty-row .sa-empty,
        .sa-table tbody tr:has(td[colspan]) .sa-empty { margin: 0; }
        .sa-table tbody td .sa-badge,
        .sa-table tbody td[data-label="Status"] .sa-badge {
            padding: 2px 6px !important;
            font-size: 10px !important;
            border-radius: 4px !important;
            line-height: 1.2 !important;
            min-width: 0 !important;
        }
        .sa-quick-links { flex-direction: row; justify-content: flex-start; flex-wrap: wrap; }
        .sa-quick-links a { justify-content: center; }
    }
    @media (max-width: 480px) {
        .sa-kpi { padding: 12px; gap: 10px; }
        .sa-kpi-icon { width: 40px; height: 40px; font-size: 20px; }
        .sa-kpi-value { font-size: 18px; }
        .sa-panel { padding: 12px; }
        .sa-table tbody tr { padding: 12px; }
        .sa-table tbody td { font-size: 12px; }
    }
    .sa-row-clickable { cursor: pointer; }
    .sa-row-clickable:hover { background: #f8fafc !important; }
    #saViewRequestModal.is-open { visibility: visible !important; opacity: 1 !important; pointer-events: auto !important; }
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
                    <tr class="sa-row-clickable" role="button" tabindex="0" title="Click to view details">
                        <td data-label="Request ID">
                            <button type="button" class="sa-btn-view-request" style="display:none;position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;"
                                data-request-id="{{ e($request['id'] ?? '') }}"
                                data-requestor="{{ e($request['requestor'] ?? '') }}"
                                data-item="{{ e($request['item'] ?? '') }}"
                                data-quantity="{{ $request['quantity'] ?? 1 }}"
                                data-description="—"
                                data-date="{{ e($request['date'] ?? '') }}"
                                data-status="{{ e($request['status'] ?? '') }}"
                                data-decided-at="—"
                                data-decided-by="—"
                                data-rejection-reason="—"
                                data-approved-id="">View</button>
                            {{ $request['id'] ?? '-' }}
                        </td>
                        <td data-label="Requestor">{{ $request['requestor'] ?? '-' }}</td>
                        <td data-label="Item">{{ $request['item'] ?? '-' }}</td>
                        <td data-label="Qty">{{ $request['quantity'] ?? 1 }}</td>
                        <td data-label="Date">{{ $request['date'] ?? '-' }}</td>
                        <td data-label="Status"><span class="sa-badge {{ $statusBadge($request['status'] ?? 'Pending') }}">{{ $request['status'] ?? 'Pending' }}</span></td>
                    </tr>
                    @empty
                    <tr class="sa-empty-row">
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
                <h2>Request Activity Feed</h2>
                <span class="sa-subtle">Most recent approved, rejected, and pending notifications</span>
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

{{-- View request modal (dashboard Recent Requests – single-click row) --}}
<div id="saViewRequestModal" class="sa-view-request-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="saViewRequestModalTitle" aria-hidden="true" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:1100;visibility:hidden;opacity:0;pointer-events:none;display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box;">
    <div class="sa-view-request-modal-backdrop" id="saViewRequestModalBackdrop" style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:1;"></div>
    <div class="sa-view-request-modal-content" id="saViewRequestModalContent" style="position:relative;z-index:2;background:#fff;border-radius:12px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:440px;width:100%;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;">
        <div class="sa-view-request-modal-header" style="padding:20px 24px;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
            <h2 id="saViewRequestModalTitle" class="sa-view-request-modal-title" style="margin:0;font-size:18px;font-weight:600;color:#0f172a;">Request details</h2>
            <button type="button" class="sa-view-request-modal-close" id="saViewRequestModalClose" aria-label="Close" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:24px;cursor:pointer;color:#64748b;">&times;</button>
        </div>
        <div class="sa-view-request-modal-body" style="padding:24px;overflow-y:auto;flex:1;">
            <section class="sa-view-request-section" style="margin-bottom:16px;">
                <h3 style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin:0 0 8px 0;">Request Details</h3>
                <dl style="margin:0;font-size:14px;">
                    <dt style="margin:0;font-weight:600;color:#64748b;">Request ID</dt><dd id="sa-view-req-id" style="margin:0 0 8px 0;color:#0f172a;">—</dd>
                    <dt style="margin:0;font-weight:600;color:#64748b;">Requestor</dt><dd id="sa-view-req-requestor" style="margin:0 0 8px 0;color:#0f172a;">—</dd>
                    <dt style="margin:0;font-weight:600;color:#64748b;">Item</dt><dd id="sa-view-req-item" style="margin:0 0 8px 0;color:#0f172a;">—</dd>
                    <dt style="margin:0;font-weight:600;color:#64748b;">Quantity</dt><dd id="sa-view-req-quantity" style="margin:0 0 8px 0;color:#0f172a;">—</dd>
                    <dt style="margin:0;font-weight:600;color:#64748b;">Date</dt><dd id="sa-view-req-date" style="margin:0 0 8px 0;color:#0f172a;">—</dd>
                    <dt style="margin:0;font-weight:600;color:#64748b;">Status</dt><dd id="sa-view-req-status" style="margin:0;color:#0f172a;">—</dd>
                </dl>
            </section>
        </div>
        <div class="sa-view-request-modal-footer" style="padding:16px 24px;border-top:1px solid #e2e8f0;flex-shrink:0;">
            <a href="{{ route('superadmin.requests') }}" class="sa-view-request-btn-all" style="display:inline-block;padding:10px 18px;background:#1d4ed8;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">View all requests</a>
            <button type="button" class="sa-view-request-btn-close" id="saViewRequestModalBtnClose" style="margin-left:8px;padding:10px 18px;background:#f1f5f9;color:#334155;border:none;border-radius:8px;cursor:pointer;font-weight:500;">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var modal = document.getElementById('saViewRequestModal');
    var backdrop = document.getElementById('saViewRequestModalBackdrop');
    var closeBtn = document.getElementById('saViewRequestModalClose');
    var footerClose = document.getElementById('saViewRequestModalBtnClose');
    function openSaViewModal(btn) {
        if (!btn) return;
        var id = btn.getAttribute('data-request-id') || '—';
        var requestor = btn.getAttribute('data-requestor') || '—';
        var item = btn.getAttribute('data-item') || '—';
        var qty = btn.getAttribute('data-quantity') || '1';
        var date = btn.getAttribute('data-date') || '—';
        var status = btn.getAttribute('data-status') || '—';
        document.getElementById('sa-view-req-id').textContent = id;
        document.getElementById('sa-view-req-requestor').textContent = requestor;
        document.getElementById('sa-view-req-item').textContent = item;
        document.getElementById('sa-view-req-quantity').textContent = qty;
        document.getElementById('sa-view-req-date').textContent = date;
        document.getElementById('sa-view-req-status').textContent = status;
        if (modal) { modal.classList.add('is-open'); modal.style.visibility = 'visible'; modal.style.opacity = '1'; modal.style.pointerEvents = 'auto'; modal.setAttribute('aria-hidden', 'false'); }
        document.body && document.body.classList.add('modal-open');
    }
    function closeSaViewModal() {
        if (modal) { modal.classList.remove('is-open'); modal.style.visibility = 'hidden'; modal.style.opacity = '0'; modal.style.pointerEvents = 'none'; modal.setAttribute('aria-hidden', 'true'); }
        document.body && document.body.classList.remove('modal-open');
    }
    document.querySelectorAll('.sa-btn-view-request').forEach(function(btn) {
        btn.addEventListener('click', function(e) { e.stopPropagation(); openSaViewModal(btn); });
    });
    var saTable = document.querySelector('.sa-table');
    if (saTable) {
        saTable.addEventListener('click', function(e) {
            var row = e.target.closest('tr.sa-row-clickable');
            if (!row) return;
            var btn = row.querySelector('.sa-btn-view-request');
            if (btn) btn.click();
        });
        saTable.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var row = e.target.closest('tr.sa-row-clickable');
            if (!row) return;
            e.preventDefault();
            var btn = row.querySelector('.sa-btn-view-request');
            if (btn) btn.click();
        });
    }
    if (backdrop) backdrop.addEventListener('click', closeSaViewModal);
    if (closeBtn) closeBtn.addEventListener('click', closeSaViewModal);
    if (footerClose) footerClose.addEventListener('click', closeSaViewModal);
    if (modal) modal.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSaViewModal(); });
})();
</script>
@endpush

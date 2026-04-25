@php
    $statusSummary = $statusSummary ?? ['approved' => 0, 'pending' => 0, 'rejected' => 0];
    $approvedCount = (int) ($statusSummary['approved'] ?? 0);
    $pendingCount = (int) ($statusSummary['pending'] ?? 0);
    $rejectedCount = (int) ($statusSummary['rejected'] ?? 0);
    $totalRequests = $approvedCount + $pendingCount + $rejectedCount;

    $statusBadge = function ($status) {
        $normalized = strtolower((string) $status);
        if (str_contains($normalized, 'approv')) {
            return 'ov-approved';
        }
        if (str_contains($normalized, 'reject')) {
            return 'ov-rejected';
        }
        return 'ov-pending';
    };
@endphp

<style>
    .approver-overview-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .approver-overview-kpi {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .approver-overview-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
    .approver-overview-kpi-icon.total { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
    .approver-overview-kpi-icon.pending { background: linear-gradient(135deg, #d97706, #f59e0b); }
    .approver-overview-kpi-icon.approved { background: linear-gradient(135deg, #059669, #10b981); }
    .approver-overview-kpi-icon.rejected { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .approver-overview-kpi-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 4px;
    }
    .approver-overview-kpi-value {
        font-size: 24px;
        line-height: 1;
        color: #0f172a;
        font-weight: 700;
    }

    .approver-overview-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 18px;
        margin-bottom: 18px;
    }
    .approver-overview-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        padding: 18px;
    }
    .approver-overview-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .approver-overview-panel-head h2 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }
    .approver-overview-subtitle {
        font-size: 12px;
        color: #64748b;
    }
    .approver-overview-stack {
        display: grid;
        gap: 12px;
        width: 100%;
        max-width: 300px;
        justify-self: end;
    }
    .approver-overview-stack .approver-overview-panel {
        padding: 12px;
        border-radius: 12px;
    }
    .approver-overview-stack .approver-overview-panel-head {
        margin-bottom: 10px;
    }
    .approver-overview-stack .approver-overview-panel-head h2 {
        font-size: 14px;
    }
    .approver-overview-stack .approver-overview-subtitle {
        font-size: 11px;
    }
    .approver-overview-table-wrap {
        overflow-x: auto;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
    }
    .approver-overview-table {
        width: 100%;
        min-width: 600px;
        border-collapse: collapse;
    }
    .approver-overview-table th,
    .approver-overview-table td {
        text-align: left;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #334155;
    }
    .approver-overview-table th {
        background: #f8fafc;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 11px;
        font-weight: 700;
    }
    .approver-overview-table tr:last-child td { border-bottom: none; }
    .approver-overview-table th:last-child,
    .approver-overview-table td:last-child {
        width: 92px;
        text-align: center;
    }
    .ov-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 66px;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.1;
    }
    .ov-approved { background: rgba(16, 185, 129, 0.14); color: #047857; }
    .ov-pending { background: rgba(245, 158, 11, 0.16); color: #b45309; }
    .ov-rejected { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }

    .approver-overview-alerts {
        display: grid;
        gap: 6px;
    }
    .approver-overview-alert {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0;
    }
    .approver-overview-alert-link {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        padding: 8px 10px;
        text-decoration: none;
        color: #0f172a;
        border-radius: 10px;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .approver-overview-alert-link:hover {
        background: #eef2ff;
        color: #1d4ed8;
    }
    .approver-overview-alert-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .approver-overview-alert-label .material-icons {
        font-size: 16px;
        color: #059669;
    }
    .approver-overview-empty {
        text-align: center;
        color: #64748b;
        font-size: 13px;
        padding: 16px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
    }
    .approver-overview-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .approver-overview-actions a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        border-radius: 8px;
        padding: 5px 8px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.1;
    }
    .approver-overview-actions .ov-primary { background: #2563eb; color: #fff; }
    .approver-overview-actions .ov-outline { background: #fff; color: #2563eb; border: 1px solid #2563eb; }

    @media (max-width: 1200px) {
        .approver-overview-kpis { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
        .approver-overview-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .approver-overview-kpis { grid-template-columns: 1fr; }
        .approver-overview-panel { padding: 14px; border-radius: 14px; }
        .approver-overview-table { min-width: 540px; }
        .approver-overview-table th,
        .approver-overview-table td { padding: 10px 10px; font-size: 12px; }
        .approver-overview-actions { flex-direction: row; justify-content: flex-start; }
        .approver-overview-actions a { justify-content: center; }
    }
</style>

<div class="approver-overview-kpis">
    <div class="approver-overview-kpi">
        <div class="approver-overview-kpi-icon total"><span class="material-icons">inventory_2</span></div>
        <div>
            <div class="approver-overview-kpi-label">Total Requests</div>
            <div class="approver-overview-kpi-value">{{ $totalRequests }}</div>
        </div>
    </div>
    <div class="approver-overview-kpi">
        <div class="approver-overview-kpi-icon pending"><span class="material-icons">pending_actions</span></div>
        <div>
            <div class="approver-overview-kpi-label">Pending</div>
            <div class="approver-overview-kpi-value">{{ $pendingCount }}</div>
        </div>
    </div>
    <div class="approver-overview-kpi">
        <div class="approver-overview-kpi-icon approved"><span class="material-icons">task_alt</span></div>
        <div>
            <div class="approver-overview-kpi-label">Approved</div>
            <div class="approver-overview-kpi-value">{{ $approvedCount }}</div>
        </div>
    </div>
    <div class="approver-overview-kpi">
        <div class="approver-overview-kpi-icon rejected"><span class="material-icons">cancel</span></div>
        <div>
            <div class="approver-overview-kpi-label">Rejected</div>
            <div class="approver-overview-kpi-value">{{ $rejectedCount }}</div>
        </div>
    </div>
</div>

<div class="approver-overview-layout">
    <div class="approver-overview-panel">
        <div class="approver-overview-panel-head">
            <h2>Recent Requests</h2>
            <span class="approver-overview-subtitle">Latest 5 records</span>
        </div>
        <div class="approver-overview-table-wrap">
            <table class="approver-overview-table">
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
                        <td><span class="ov-badge {{ $statusBadge($request['status'] ?? 'Pending') }}">{{ $request['status'] ?? 'Pending' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6"><div class="approver-overview-empty">No request history available.</div></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="approver-overview-stack">
        <div class="approver-overview-panel">
            <div class="approver-overview-panel-head">
                <h2>Request Alerts</h2>
                <span class="approver-overview-subtitle">Most recent 3 pending requests</span>
            </div>
            <div class="approver-overview-alerts">
                @forelse($requestAlerts ?? [] as $alert)
                <div class="approver-overview-alert">
                    <a href="{{ $alert['url'] ?? '#' }}" class="approver-overview-alert-link">
                        <span class="approver-overview-alert-label">
                            <span class="material-icons">notifications_active</span>
                            Request ID: {{ $alert['id'] }}
                        </span>
                    </a>
                </div>
                @empty
                <div class="approver-overview-empty">No request alerts available.</div>
                @endforelse
            </div>
        </div>

        <div class="approver-overview-panel">
            <div class="approver-overview-panel-head">
                <h2>Quick Actions</h2>
            </div>
            @php
                $dashboardRoute = auth()->check() ? route('approver.dashboard') : route('approver.guest');
            @endphp
            <div class="approver-overview-actions">
                <a href="{{ $dashboardRoute }}?tab=pending" class="ov-primary"><span class="material-icons" style="font-size:14px;">pending_actions</span> Review pending</a>
                <a href="{{ $dashboardRoute }}?tab=approved" class="ov-outline"><span class="material-icons" style="font-size:14px;">list_alt</span> View all requests</a>
            </div>
        </div>
    </div>
</div>

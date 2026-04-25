@if(($tab ?? '') === 'approved')
<style>
/* Filter by status - match Super Admin / User for consistency */
.approver-all-request-card .status-filter-dropdown { position: relative; }
.approver-all-request-card .status-filter-menu {
    display: none !important;
    position: absolute !important;
    top: calc(100% + 6px) !important;
    right: 0 !important;
    min-width: 180px !important;
    padding: 6px !important;
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 25px rgba(0,0,0,.1) !important;
    z-index: 50 !important;
}
.approver-all-request-card .status-filter-dropdown.is-open .status-filter-menu { display: block !important; }
.approver-all-request-card .status-filter-trigger {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    min-width: 150px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    cursor: pointer !important;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(0,0,0,.04) !important;
}
.approver-all-request-card .status-filter-trigger:hover { background: #f8fafc !important; border-color: #e2e8f0 !important; }
.approver-all-request-card .status-filter-value { flex: 1 !important; text-align: left !important; }
.approver-all-request-card .status-filter-chevron {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 20px !important;
    height: 20px !important;
    flex-shrink: 0 !important;
    font-size: 20px !important;
    color: #64748b !important;
}
.approver-all-request-card .status-filter-dropdown.is-open .status-filter-chevron { transform: rotate(180deg); }
.approver-all-request-card .status-filter-wrap { display: flex !important; align-items: center !important; gap: 12px !important; }
.approver-all-request-card .status-filter-label {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: .05em !important;
}
.approver-all-request-card .status-filter-option {
    text-decoration: none !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 10px 12px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    border-radius: 8px !important;
}
.approver-all-request-card .status-filter-option:hover { background: #f1f5f9 !important; }
.approver-all-request-card .status-filter-option.selected { background: #eff6ff !important; color: #1d4ed8 !important; }
.approver-all-request-card .status-filter-option-dot {
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    flex-shrink: 0 !important;
}
.approver-all-request-card .status-dot-all { background: #94a3b8 !important; }
.approver-all-request-card .status-dot-approved { background: #10b981 !important; }
.approver-all-request-card .status-dot-rejected { background: #ef4444 !important; }
/* Reject reason on demand (Option B) */
.reject-reason-cell { display: inline-flex !important; align-items: center !important; gap: 6px !important; }
.reject-reason-view-btn {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    width: 22px !important; height: 22px !important; padding: 0 !important;
    border: none !important; background: #f1f5f9 !important; color: #64748b !important;
    border-radius: 6px !important; cursor: pointer !important;
}
.reject-reason-view-btn:hover { background: #e2e8f0 !important; color: #475569 !important; }
.reject-reason-view-btn .material-icons { font-size: 14px !important; }
.reject-reason-popover {
    display: none; position: fixed; z-index: 1000;
    max-width: 320px; padding: 12px 36px 12px 14px; background: #fff;
    border: 1px solid #e2e8f0; border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,.12);
    font-size: 13px; color: #334155; line-height: 1.5;
}
.reject-reason-popover.is-open { display: block; }
.reject-reason-popover .reject-reason-popover-title { font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; }
.reject-reason-popover .reject-reason-popover-body { white-space: pre-wrap; word-break: break-word; }
.reject-reason-popover .reject-reason-popover-close {
    position: absolute; top: 8px; right: 8px;
    width: 24px; height: 24px; padding: 0; border: none; background: none;
    color: #94a3b8; cursor: pointer; border-radius: 4px; font-size: 18px; line-height: 1;
}
.reject-reason-popover .reject-reason-popover-close:hover { background: #f1f5f9; color: #475569; }
</style>
@endif
{{-- Critical: stat cards + table look (apply even if built CSS is cached/stale) --}}
<style>
body.panel-approver .stat-cards {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;
    margin-bottom: 24px !important;
    width: 100% !important;
    max-width: 100% !important;
}
body.panel-approver .stat-cards .stat-card {
    border-radius: 12px !important;
    min-width: 0 !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
}
@media (max-width: 900px) {
    body.panel-approver .stat-cards {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
}
body.panel-approver .table-card {
    background: #ffffff !important;
    border-radius: 12px !important;
    border: 1px solid #e5e7eb !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    padding: 24px !important;
    margin-bottom: 24px !important;
}
body.panel-approver .table-card .card-title-bar {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
    margin-bottom: 20px !important;
    padding-bottom: 14px !important;
    border-bottom: 2px solid #e5e7eb !important;
    background: transparent !important;
}
body.panel-approver .table-card .card-title-bar h2 {
    font-size: 15px !important;
    font-weight: 600 !important;
    color: #374151 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin: 0 !important;
}
body.panel-approver .table-responsive {
    overflow-x: auto !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
}
body.panel-approver .data-table th,
body.panel-approver .data-table td {
    padding: 14px 18px !important;
    text-align: left !important;
    border: 1px solid #e5e7eb !important;
}
body.panel-approver .data-table thead th {
    font-weight: 600 !important;
    color: #374151 !important;
    background: #f3f4f6 !important;
    font-size: 13px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}
body.panel-approver .data-table tbody td {
    color: #1e293b !important;
    vertical-align: middle !important;
}
body.panel-approver .data-table tbody tr:nth-child(even) {
    background: #fafafa !important;
}
body.panel-approver .data-table tbody tr:hover {
    background: #f0f9ff !important;
}
</style>
@if(($tab ?? '') === 'pending')
<style>
/* Approve/Reject icon buttons - ensure correct design (blue/red, no border) */
.approver-action-buttons { display: inline-flex; align-items: center; gap: 8px; flex-wrap: nowrap; }
.approver-form-inline { display: inline-flex; align-items: center; }
.approver-actions-cell { white-space: nowrap; vertical-align: middle; }
.btn-icon-action {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    width: 38px !important; height: 38px !important; min-width: 38px !important; min-height: 38px !important;
    padding: 0 !important; border: none !important; border-radius: 10px !important;
    cursor: pointer !important; box-sizing: border-box !important;
}
.btn-icon-action .material-icons { font-size: 22px !important; flex-shrink: 0 !important; line-height: 1 !important; }
.btn-icon-view { background: #f1f5f9 !important; color: #475569 !important; }
.btn-icon-view:hover { background: #e2e8f0 !important; color: #1e293b !important; }
.btn-icon-approve { background: #059669 !important; color: #fff !important; }
.btn-icon-approve:hover { background: #047857 !important; color: #fff !important; }
.btn-icon-reject { background: #dc2626 !important; color: #fff !important; }
.btn-icon-reject:hover { background: #b91c1c !important; color: #fff !important; }
</style>
@endif
@if(!auth()->check())
<div class="guest-notice">
    <span class="material-icons">info</span>
    <div>
        <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
    </div>
</div>
@endif

<div class="header-section">
    <h1>
        @if(($tab ?? '') === 'pending')
            Pending Requests
        @elseif(($tab ?? '') === 'approved')
            All Request
        @else
            Approver Dashboard
        @endif
    </h1>
    <p>
        @if(($tab ?? '') === 'pending')
            All requests waiting for your action. Approve or reject below.
        @elseif(($tab ?? '') === 'approved')
            View all requests and their status.
        @else
            Review and approve or reject pending requests.
        @endif
    </p>
</div>

@if(session('message'))
<div class="alert-success">
    <span>{{ session('message') }}</span>
    <button type="button" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

@if(($tab ?? '') === '')
    @include('dashboards.partials.approver-overview')
@else
@if(($tab ?? '') !== 'approved')
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon"><span class="material-icons">pending_actions</span></div>
        <div><div class="stat-label">Pending Requests</div><div class="stat-value">{{ $pendingRequests ?? 0 }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-icons">assignment_turned_in</span></div>
        <div><div class="stat-label">Approved Today</div><div class="stat-value">{{ $approvedToday ?? 0 }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-icons">check_circle</span></div>
        <div><div class="stat-label">Completed</div><div class="stat-value">{{ $completed ?? 0 }}</div></div>
    </div>
</div>
@endif

<div class="table-card {{ ($tab ?? '') === 'approved' ? 'approver-all-request-card' : '' }}">
    <div class="card-title-bar">
        @if(($tab ?? '') === 'pending')
            <h2>All Pending Requests (up to 50)</h2>
        @elseif(($tab ?? '') === 'approved')
            <h2>All requests</h2>
            @php
                $approverDashboard = auth()->check() ? route('approver.dashboard') : route('approver.guest');
                $statusFilter = $statusFilter ?? 'all';
                $filterLabels = ['all' => 'All', 'approved' => 'Approved', 'rejected' => 'Rejected'];
                $filterLabel = $filterLabels[$statusFilter] ?? 'All';
            @endphp
            <div class="status-filter-wrap">
                <span class="status-filter-label">Filter by status</span>
                <div class="status-filter-dropdown" id="approver-status-filter-dropdown">
                    <button type="button" class="status-filter-trigger" id="approver-status-filter-trigger" aria-haspopup="listbox" aria-expanded="false" aria-label="Filter by status">
                        <span class="status-filter-value" id="approver-status-filter-value">{{ $filterLabel }}</span>
                        <span class="status-filter-chevron material-icons" aria-hidden="true">expand_more</span>
                    </button>
                    <div class="status-filter-menu" id="approver-status-filter-menu" role="listbox" aria-hidden="true">
                        <a href="{{ $approverDashboard }}?tab=approved&status=all" class="status-filter-option {{ $statusFilter === 'all' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-all"></span>
                            <span>All</span>
                        </a>
                        <a href="{{ $approverDashboard }}?tab=approved&status=approved" class="status-filter-option {{ $statusFilter === 'approved' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-approved"></span>
                            <span>Approved</span>
                        </a>
                        <a href="{{ $approverDashboard }}?tab=approved&status=rejected" class="status-filter-option {{ $statusFilter === 'rejected' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-rejected"></span>
                            <span>Rejected</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <h2>Recent 10 Pending</h2>
        @endif
    </div>
    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Requestor</th>
                <th>Item</th>
                @if(($tab ?? '') === 'approved')
                    <th>Quantity</th>
                    <th>Decided</th>
                @elseif(($tab ?? '') === 'pending')
                    <th>Actions</th>
                @endif
                <th>Status</th>
                @if(($tab ?? '') !== 'pending')
                    <th>View</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse(($listRequests ?? []) as $req)
            <tr data-request-row-id="{{ e($req['request_id'] ?? $req['id']) }}">
                <td>{{ (($req['status'] ?? '') === 'Approved' && !empty($req['approved_id'] ?? null)) ? $req['approved_id'] : ($req['request_id'] ?? $req['id']) }}</td>
                <td>{{ $req['requestor'] }}</td>
                <td class="item-cell-truncate" title="{{ e($req['item'] . (isset($req['quantity']) && $req['quantity'] > 1 ? ' (Qty: ' . $req['quantity'] . ')' : '')) }}">{{ $req['item'] }}</td>
                @if(($tab ?? '') === 'approved')
                    <td>{{ $req['quantity'] ?? 1 }}</td>
                    <td>{{ $req['decided_at'] ?? '—' }}</td>
                @elseif(($tab ?? '') === 'pending')
                    <td class="approver-actions-cell">
                        <div class="approver-action-buttons">
                            <button type="button" class="btn-view-request btn-icon-action btn-icon-view" title="View details" aria-label="View request details"
                                data-request-id="{{ e($req['request_id'] ?? $req['id']) }}"
                                data-requestor="{{ e($req['requestor'] ?? '') }}"
                                data-item="{{ e($req['item'] ?? '') }}"
                                data-quantity="{{ $req['quantity'] ?? 1 }}"
                                data-description="{{ e($req['description'] ?? '') }}"
                                data-date="{{ e($req['date'] ?? '') }}"
                                data-status="{{ e($req['status'] ?? '') }}"
                                data-decided-at="{{ e($req['decided_at'] ?? '') }}"
                                data-decided-by="{{ e($req['decided_by'] ?? '') }}"
                                data-rejection-reason="{{ e($req['rejection_reason'] ?? '') }}"
                                data-approved-id="{{ e($req['approved_id'] ?? '') }}"><span class="material-icons" aria-hidden="true">visibility</span></button>
                            @if(($req['status'] ?? '') === 'Pending')
                                @php
                                    $approveRoute = request()->routeIs('approver.guest') ? 'approver.guest.approve' : 'approver.approve';
                                    $rejectRoute = request()->routeIs('approver.guest') ? 'approver.guest.reject' : 'approver.reject';
                                @endphp
                                <form id="approve-form-{{ $req['id'] }}" action="{{ route($approveRoute, ['id' => $req['id']]) }}?tab={{ urlencode($tab ?? '') }}" method="POST" class="form-inline approver-form-inline">
                                    @csrf
                                    <input type="hidden" name="tab" value="{{ $tab ?? '' }}">
                                    <button type="button" class="btn-icon-action btn-icon-approve approve-trigger" data-form-id="approve-form-{{ $req['id'] }}" data-request-id="{{ $req['request_id'] ?? $req['id'] }}" title="Approve" aria-label="Approve request" style="background:#059669;color:#fff;border:none;"><span class="material-icons" aria-hidden="true">check_circle</span></button>
                                </form>
                                <button type="button" class="btn-icon-action btn-icon-reject reject-trigger" data-reject-url="{{ route($rejectRoute, ['id' => $req['id']]) }}" data-tab="{{ $tab ?? '' }}" data-request-id="{{ $req['request_id'] ?? $req['id'] }}" title="Reject" aria-label="Reject request" style="background:#dc2626;color:#fff;border:none;"><span class="material-icons" aria-hidden="true">cancel</span></button>
                            @endif
                        </div>
                    </td>
                @endif
                <td>
                    @if(($req['status'] ?? '') === 'Pending')
                        <span class="badge badge-pending">Pending</span>
                    @elseif(($req['status'] ?? '') === 'Approved')
                        <span class="badge badge-approved">Approved</span>
                    @else
                        @php
                            $rejectReason = $req['rejection_reason'] ?? null;
                            $hasReason = !empty(trim((string) $rejectReason));
                        @endphp
                        <span class="reject-reason-cell">
                            <span class="badge badge-rejected">Rejected</span>
                            @if($hasReason)
                                <button type="button" class="reject-reason-view-btn" data-reason="{{ e($rejectReason) }}" title="Reason for rejection" aria-label="Reason for rejection">
                                    <span class="material-icons" aria-hidden="true">info</span>
                                </button>
                            @endif
                        </span>
                    @endif
                </td>
                @if(($tab ?? '') !== 'pending')
                <td>
                    <button type="button" class="btn-view-request btn-sm btn-icon-view" title="View details" aria-label="View request details"
                        data-request-id="{{ e($req['request_id'] ?? $req['id']) }}"
                        data-requestor="{{ e($req['requestor'] ?? '') }}"
                        data-item="{{ e($req['item'] ?? '') }}"
                        data-quantity="{{ $req['quantity'] ?? 1 }}"
                        data-description="{{ e($req['description'] ?? '') }}"
                        data-date="{{ e($req['date'] ?? '') }}"
                        data-status="{{ e($req['status'] ?? '') }}"
                        data-decided-at="{{ e($req['decided_at'] ?? '') }}"
                        data-decided-by="{{ e($req['decided_by'] ?? '') }}"
                        data-rejection-reason="{{ e($req['rejection_reason'] ?? '') }}"
                        data-approved-id="{{ e($req['approved_id'] ?? '') }}"> <span class="material-icons" aria-hidden="true">visibility</span></button>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($tab ?? '') === 'approved' ? 7 : (($tab ?? '') === 'pending' ? 6 : 5) }}" style="text-align: center; padding: 40px; color: #94a3b8;">
                    @if(($tab ?? '') === 'pending')
                        No pending requests.
                    @elseif(($tab ?? '') === 'approved')
                        No approved or rejected requests yet.
                    @else
                        No recent requests for approval.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@if(($tab ?? '') === 'approved')
{{-- Popover for reject reason (Option B: on demand) --}}
<div id="reject-reason-popover" class="reject-reason-popover" role="tooltip" aria-hidden="true">
    <button type="button" class="reject-reason-popover-close" aria-label="Close">&times;</button>
    <div class="reject-reason-popover-title">Reason for rejection</div>
    <div class="reject-reason-popover-body"></div>
</div>
@endif
@endif

<style>
.request-row-highlight {
    background: #dbeafe !important;
    box-shadow: inset 0 0 0 1px #60a5fa;
}
</style>

{{-- View request details modal (approver) - styled like Super Admin View Modal --}}
<style>
#viewRequestModal {
    position: fixed !important; top: 0 !important; left: 0 !important;
    width: 100% !important; height: 100% !important; z-index: 1100 !important;
    visibility: hidden; opacity: 0; transition: visibility 0.2s, opacity 0.2s;
    pointer-events: none; display: flex !important; align-items: center; justify-content: center;
    padding: 24px; box-sizing: border-box;
}
#viewRequestModal.is-open {
    visibility: visible !important; opacity: 1 !important; pointer-events: auto !important;
}
#viewRequestModal .view-request-modal-backdrop {
    position: fixed !important; top: 0 !important; left: 0 !important;
    width: 100% !important; height: 100% !important;
    background: rgba(15, 23, 42, 0.5) !important; z-index: 1 !important;
}
#viewRequestModal .view-request-modal-content {
    position: relative !important; z-index: 2 !important;
    background: #fff !important; border-radius: 12px !important;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25) !important;
    min-width: 360px; max-width: 480px; width: 100%;
    max-height: calc(100vh - 48px); overflow: hidden; display: flex; flex-direction: column;
}
#viewRequestModal .view-request-modal-header {
    background: #2563eb !important; color: #fff !important;
    padding: 16px 20px !important; border-radius: 12px 12px 0 0 !important;
    display: flex !important; align-items: center !important; justify-content: space-between !important;
}
#viewRequestModal .view-request-modal-title {
    margin: 0 !important; font-size: 18px !important; font-weight: 600 !important; color: #fff !important;
}
#viewRequestModal .view-request-modal-close {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    width: 32px !important; height: 32px !important; padding: 0 !important;
    background: rgba(255,255,255,0.2) !important; border: none !important; border-radius: 8px !important;
    color: #fff !important; font-size: 20px !important; line-height: 1 !important; cursor: pointer !important;
}
#viewRequestModal .view-request-modal-close:hover {
    background: rgba(255,255,255,0.3) !important;
}
#viewRequestModal .view-request-modal-body { padding: 24px !important; overflow-y: auto !important; flex: 1; min-height: 0; }
#viewRequestModal .view-request-section { margin-bottom: 20px !important; }
#viewRequestModal .view-request-section:last-of-type { margin-bottom: 0 !important; }
#viewRequestModal .view-request-section-title {
    font-size: 13px !important; font-weight: 700 !important; color: #475569 !important;
    text-transform: uppercase !important; letter-spacing: 0.05em !important;
    margin: 0 0 12px 0 !important; padding-bottom: 8px !important; border-bottom: 1px solid #e2e8f0 !important;
}
#viewRequestModal .view-request-dl {
    display: grid !important; grid-template-columns: 120px 1fr !important;
    gap: 10px 20px !important; margin: 0 !important; font-size: 14px !important;
}
#viewRequestModal .view-request-dl dt { margin: 0 !important; font-weight: 600 !important; color: #64748b !important; }
#viewRequestModal .view-request-dl dd { margin: 0 !important; color: #0f172a !important; word-break: break-word !important; }
#viewRequestModal .view-request-modal-footer {
    padding: 16px 24px !important; border-top: 1px solid #e2e8f0 !important;
    display: flex !important; justify-content: flex-end !important; gap: 12px !important;
}
#viewRequestModal .view-request-btn-close {
    padding: 10px 20px !important; font-size: 14px !important; font-weight: 500 !important;
    background: #2563eb !important; color: #fff !important; border: none !important;
    border-radius: 8px !important; cursor: pointer !important;
}
#viewRequestModal .view-request-btn-close:hover { background: #1d4ed8 !important; }
#viewRequestModal .view-req-rejection-label { margin-top: 10px !important; padding-top: 10px !important; border-top: 1px solid #e2e8f0 !important; }
#viewRequestModal .view-req-rejection-value { margin-top: 10px !important; padding-top: 10px !important; }
</style>
<div id="viewRequestModal" class="view-request-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="viewRequestModalTitle" aria-hidden="true">
    <div class="view-request-modal-backdrop" id="viewRequestModalBackdrop"></div>
    <div class="view-request-modal-content" id="viewRequestModalContent">
        <div class="view-request-modal-header">
            <h2 id="viewRequestModalTitle" class="view-request-modal-title">Request details</h2>
            <button type="button" class="view-request-modal-close" id="viewRequestModalClose" aria-label="Close">&times;</button>
        </div>
        <div class="view-request-modal-body">
            <section class="view-request-section">
                <h3 class="view-request-section-title">Request Details</h3>
                <dl class="view-request-dl">
                    <dt>Request ID</dt><dd id="view-req-id">—</dd>
                    <dt>Requestor</dt><dd id="view-req-requestor">—</dd>
                    <dt>Request date</dt><dd id="view-req-date">—</dd>
                </dl>
            </section>
            <section class="view-request-section">
                <h3 class="view-request-section-title">Item Details</h3>
                <dl class="view-request-dl">
                    <dt>Item name</dt><dd id="view-req-item">—</dd>
                    <dt>Quantity</dt><dd id="view-req-quantity">—</dd>
                    <dt>Description</dt><dd id="view-req-description">—</dd>
                </dl>
            </section>
            <section class="view-request-section">
                <h3 class="view-request-section-title">Status &amp; Decision</h3>
                <dl class="view-request-dl">
                    <dt>Status</dt><dd id="view-req-status">—</dd>
                    <dt>Decided</dt><dd id="view-req-decided-at">—</dd>
                    <dt>By</dt><dd id="view-req-decided-by">—</dd>
                    <dt class="view-req-rejection-label">Rejection reason</dt><dd class="view-req-rejection-value" id="view-req-rejection-reason">—</dd>
                </dl>
            </section>
        </div>
        <div class="view-request-modal-footer">
            <button type="button" class="view-request-btn-close" id="viewRequestModalBtnClose">Close</button>
        </div>
    </div>
</div>

<footer class="footer">
    <p class="copyright">© {{ date('Y') }} Product Request System - DICT</p>
</footer>

{{-- Approve confirmation modal (approver) --}}
<div id="approveModal" class="approve-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle" aria-hidden="true">
    <div class="approve-modal-backdrop" id="approveModalBackdrop"></div>
    <div class="approve-modal-content" id="approveModalContent">
        <div class="approve-modal-header">
            <h2 id="approveModalTitle" class="approve-modal-title">Approve Request</h2>
            <button type="button" class="approve-modal-close" id="approveModalClose" aria-label="Close">&times;</button>
        </div>
        <p id="approveModalMessage" class="approve-modal-desc">Approve this request? This will generate a 6-digit approved ID and mark the item as approved.</p>
        <div class="approve-modal-actions">
            <button type="button" id="approveModalCancel" class="btn-approve-cancel">Cancel</button>
            <button type="button" id="approveModalConfirm" class="btn-approve-confirm">Approve</button>
        </div>
    </div>
</div>

<script>
(function() {
    var trigger = document.getElementById('approver-status-filter-trigger');
    var menu = document.getElementById('approver-status-filter-menu');
    var dropdown = document.getElementById('approver-status-filter-dropdown');
    if (trigger && menu && dropdown) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = dropdown.classList.toggle('is-open');
            menu.setAttribute('aria-hidden', !open);
            trigger.setAttribute('aria-expanded', open);
        });
        document.addEventListener('click', function() {
            if (dropdown.classList.contains('is-open')) {
                dropdown.classList.remove('is-open');
                menu.setAttribute('aria-hidden', 'true');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();
</script>
<script>
(function() {
    var popover = document.getElementById('reject-reason-popover');
    if (!popover) return;
    var bodyEl = popover.querySelector('.reject-reason-popover-body');
    var closeBtn = popover.querySelector('.reject-reason-popover-close');
    var hideTimer = null;
    function hide() {
        hideTimer = setTimeout(function() {
            popover.classList.remove('is-open');
            popover.setAttribute('aria-hidden', 'true');
        }, 150);
    }
    function cancelHide() {
        if (hideTimer) clearTimeout(hideTimer);
        hideTimer = null;
    }
    function show(reason, anchor) {
        if (!bodyEl) return;
        cancelHide();
        bodyEl.textContent = reason || 'No reason provided.';
        var rect = anchor.getBoundingClientRect();
        popover.style.left = Math.min(rect.left, window.innerWidth - 330) + 'px';
        popover.style.top = (rect.bottom + 8) + 'px';
        popover.classList.add('is-open');
        popover.setAttribute('aria-hidden', 'false');
    }
    function bindTrigger(btn) {
        var reason = btn.getAttribute('data-reason') || '';
        btn.addEventListener('mouseenter', function() { show(reason, btn); });
        btn.addEventListener('mouseleave', function() { hide(); });
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            show(reason, btn);
        });
    }
    document.querySelectorAll('.reject-reason-view-btn').forEach(bindTrigger);
    document.querySelectorAll('.reject-reason-cell .badge.badge-rejected').forEach(function(badge) {
        var btn = badge.parentElement && badge.parentElement.querySelector('.reject-reason-view-btn');
        if (btn) {
            var reason = btn.getAttribute('data-reason') || '';
            badge.addEventListener('mouseenter', function() { show(reason, badge); });
            badge.addEventListener('mouseleave', function() { hide(); });
        }
    });
    popover.addEventListener('mouseenter', cancelHide);
    popover.addEventListener('mouseleave', hide);
    if (closeBtn) closeBtn.addEventListener('click', function() {
        cancelHide();
        popover.classList.remove('is-open');
        popover.setAttribute('aria-hidden', 'true');
    });
    document.addEventListener('click', function() {
        if (popover.classList.contains('is-open')) {
            cancelHide();
            popover.classList.remove('is-open');
            popover.setAttribute('aria-hidden', 'true');
        }
    });
    popover.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
<script>
(function() {
    var focusRequest = @json((string) request('focus_request', ''));
    if (!focusRequest) return;

    var rows = document.querySelectorAll('tr[data-request-row-id]');
    var targetRow = null;
    rows.forEach(function(row) {
        if (row.getAttribute('data-request-row-id') === focusRequest) {
            targetRow = row;
        }
    });
    if (!targetRow) return;

    targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    targetRow.classList.add('request-row-highlight');
    setTimeout(function() {
        targetRow.classList.remove('request-row-highlight');
    }, 2200);
})();
</script>
<script>
(function() {
    var modal = document.getElementById('rejectModal');
    var backdrop = document.getElementById('rejectModalBackdrop');
    var form = document.getElementById('rejectForm');
    var tabInput = document.getElementById('rejectFormTab');
    var textarea = document.getElementById('rejection_reason');
    var charCount = document.getElementById('rejectCharCount');
    var cancelBtn = document.getElementById('rejectModalCancel');
    var triggers = document.querySelectorAll('.reject-trigger');

    function openModal(rejectUrl, tab) {
        if (!form || !modal) return;
        form.action = rejectUrl;
        if (tabInput) tabInput.value = tab || '';
        if (textarea) textarea.value = '';
        updateCharCount();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        if (document.body) document.body.classList.add('modal-open');
        if (document.documentElement) document.documentElement.classList.add('modal-open');
        if (textarea) textarea.focus();
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }
        if (document.body) document.body.classList.remove('modal-open');
        if (document.documentElement) document.documentElement.classList.remove('modal-open');
    }

    function updateCharCount() {
        if (charCount && textarea) charCount.textContent = (textarea.value.length) + ' / 500';
    }

    triggers.forEach(function(btn) {
        btn.addEventListener('click', function() {
            openModal(btn.getAttribute('data-reject-url'), btn.getAttribute('data-tab'));
        });
    });
    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (textarea) textarea.addEventListener('input', updateCharCount);

    if (modal) modal.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Approve confirmation modal
    var approveModal = document.getElementById('approveModal');
    var approveBackdrop = document.getElementById('approveModalBackdrop');
    var approveMessage = document.getElementById('approveModalMessage');
    var approveCancel = document.getElementById('approveModalCancel');
    var approveConfirm = document.getElementById('approveModalConfirm');
    var approveClose = document.getElementById('approveModalClose');
    var pendingApproveFormId = null;

    function openApproveModal(formId, requestId) {
        pendingApproveFormId = formId;
        if (approveMessage) approveMessage.innerHTML = 'Approve request <strong>' + (requestId || '') + '</strong>? This will generate a 6-digit approved ID and mark the item as approved.';
        if (approveModal) {
            approveModal.classList.add('is-open');
            approveModal.setAttribute('aria-hidden', 'false');
        }
        if (document.body) document.body.classList.add('modal-open');
        if (document.documentElement) document.documentElement.classList.add('modal-open');
    }

    function closeApproveModal() {
        pendingApproveFormId = null;
        if (approveModal) {
            approveModal.classList.remove('is-open');
            approveModal.setAttribute('aria-hidden', 'true');
        }
        if (document.body) document.body.classList.remove('modal-open');
        if (document.documentElement) document.documentElement.classList.remove('modal-open');
    }

    document.querySelectorAll('.approve-trigger').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var formId = btn.getAttribute('data-form-id');
            var requestId = btn.getAttribute('data-request-id') || '';
            if (formId) openApproveModal(formId, requestId);
        });
    });
    if (approveConfirm) approveConfirm.addEventListener('click', function() {
        if (pendingApproveFormId) {
            var form = document.getElementById(pendingApproveFormId);
            if (form) form.submit();
        }
        closeApproveModal();
    });
    if (approveCancel) approveCancel.addEventListener('click', closeApproveModal);
    if (approveClose) approveClose.addEventListener('click', closeApproveModal);
    if (approveBackdrop) approveBackdrop.addEventListener('click', closeApproveModal);
    if (approveModal) approveModal.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeApproveModal();
    });

    // View request details modal
    var viewModal = document.getElementById('viewRequestModal');
    var viewBackdrop = document.getElementById('viewRequestModalBackdrop');
    var viewClose = document.getElementById('viewRequestModalClose');
    var viewIds = ['view-req-id', 'view-req-requestor', 'view-req-item', 'view-req-quantity', 'view-req-description', 'view-req-date', 'view-req-status', 'view-req-decided-at', 'view-req-decided-by', 'view-req-rejection-reason'];

    function openViewModal(btn) {
        var id = btn.getAttribute('data-request-id') || '—';
        var approvedId = btn.getAttribute('data-approved-id') || '';
        var displayId = approvedId && (btn.getAttribute('data-status') === 'Approved') ? approvedId : id;
        var requestor = btn.getAttribute('data-requestor') || '—';
        var item = btn.getAttribute('data-item') || '—';
        var qty = btn.getAttribute('data-quantity') || '1';
        var desc = btn.getAttribute('data-description') || '';
        if (!desc || desc.trim() === '') desc = '—';
        var date = btn.getAttribute('data-date') || '—';
        var status = btn.getAttribute('data-status') || '—';
        var decidedAt = btn.getAttribute('data-decided-at') || '—';
        var decidedBy = btn.getAttribute('data-decided-by') || '—';
        var rejectionReason = btn.getAttribute('data-rejection-reason') || '';
        if (!rejectionReason || rejectionReason.trim() === '') rejectionReason = '—';

        document.getElementById('view-req-id').textContent = displayId;
        document.getElementById('view-req-requestor').textContent = requestor;
        document.getElementById('view-req-item').textContent = item;
        document.getElementById('view-req-quantity').textContent = qty;
        document.getElementById('view-req-description').textContent = desc;
        document.getElementById('view-req-date').textContent = date;
        document.getElementById('view-req-status').textContent = status;
        document.getElementById('view-req-decided-at').textContent = decidedAt;
        document.getElementById('view-req-decided-by').textContent = decidedBy;
        document.getElementById('view-req-rejection-reason').textContent = rejectionReason;

        var rejLabel = document.querySelector('.view-req-rejection-label');
        var rejVal = document.querySelector('.view-req-rejection-value');
        if (rejLabel && rejVal) {
            rejLabel.style.display = status === 'Rejected' ? '' : 'none';
            rejVal.style.display = status === 'Rejected' ? '' : 'none';
        }

        if (viewModal) {
            viewModal.classList.add('is-open');
            viewModal.setAttribute('aria-hidden', 'false');
        }
        if (document.body) document.body.classList.add('modal-open');
        if (document.documentElement) document.documentElement.classList.add('modal-open');
    }

    function closeViewModal() {
        if (viewModal) {
            viewModal.classList.remove('is-open');
            viewModal.setAttribute('aria-hidden', 'true');
        }
        if (document.body) document.body.classList.remove('modal-open');
        if (document.documentElement) document.documentElement.classList.remove('modal-open');
    }

    document.querySelectorAll('.btn-view-request').forEach(function(btn) {
        btn.addEventListener('click', function() { openViewModal(btn); });
    });
    if (viewBackdrop) viewBackdrop.addEventListener('click', closeViewModal);
    if (viewClose) viewClose.addEventListener('click', closeViewModal);
    var viewBtnClose = document.getElementById('viewRequestModalBtnClose');
    if (viewBtnClose) viewBtnClose.addEventListener('click', closeViewModal);
    if (viewModal) viewModal.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeViewModal();
    });
})();
</script>

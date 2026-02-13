<nav class="breadcrumb">
    Home &gt; <a href="{{ $approverDashboard ?? '#' }}">Approver</a> &gt;
    @if(($tab ?? '') === 'pending') Pending Requests
    @elseif(($tab ?? '') === 'approved') Approved & Rejected
    @else Overview
    @endif
</nav>

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
            Approved & Rejected
        @else
            Approver Dashboard
        @endif
    </h1>
    <p>
        @if(($tab ?? '') === 'pending')
            All requests waiting for your action. Approve or reject below.
        @elseif(($tab ?? '') === 'approved')
            History of approved and rejected requests.
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

<div class="table-card">
    <div class="card-title-bar">
        @if(($tab ?? '') === 'pending')
            All Pending Requests (up to 50)
        @elseif(($tab ?? '') === 'approved')
            Approved & Rejected History
        @else
            Recent 10 Pending — Quick Actions
        @endif
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Requestor</th>
                <th>Item Details</th>
                <th>Request Date</th>
                <th>Status</th>
                @if(($tab ?? '') === 'approved')
                    <th>Decided</th>
                    <th>By</th>
                @else
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse(($listRequests ?? []) as $req)
            <tr>
                <td>{{ (($req['status'] ?? '') === 'Approved' && !empty($req['approved_id'] ?? null)) ? $req['approved_id'] : ($req['request_id'] ?? $req['id']) }}</td>
                <td>{{ $req['requestor'] }}</td>
                <td>{{ $req['item'] }}{{ isset($req['quantity']) && $req['quantity'] > 1 ? ' (Qty: ' . $req['quantity'] . ')' : '' }}</td>
                <td>{{ $req['date'] }}</td>
                <td>
                    @if(($req['status'] ?? '') === 'Pending')
                        <span class="badge badge-pending">Pending</span>
                    @elseif(($req['status'] ?? '') === 'Approved')
                        <span class="badge badge-approved">Approved</span>
                    @else
                        <span class="badge badge-rejected">Rejected</span>
                    @endif
                </td>
                @if(($tab ?? '') === 'approved')
                    <td>{{ $req['decided_at'] ?? '—' }}</td>
                    <td>{{ $req['decided_by'] ?? '—' }}</td>
                @else
                    <td>
                        @if(($req['status'] ?? '') === 'Pending')
                            @php
                                $approveRoute = request()->routeIs('approver.guest') ? 'approver.guest.approve' : 'approver.approve';
                                $rejectRoute = request()->routeIs('approver.guest') ? 'approver.guest.reject' : 'approver.reject';
                            @endphp
                            <form id="approve-form-{{ $req['id'] }}" action="{{ route($approveRoute, ['id' => $req['id']]) }}?tab={{ urlencode($tab ?? '') }}" method="POST" class="form-inline" style="display:inline;">
                                @csrf
                                <input type="hidden" name="tab" value="{{ $tab ?? '' }}">
                                <button type="button" class="btn-sm btn-approve approve-trigger" data-form-id="approve-form-{{ $req['id'] }}" data-request-id="{{ $req['request_id'] ?? $req['id'] }}">Approve</button>
                            </form>
                            <button type="button" class="btn-sm btn-reject reject-trigger" data-reject-url="{{ route($rejectRoute, ['id' => $req['id']]) }}" data-tab="{{ $tab ?? '' }}" data-request-id="{{ $req['request_id'] ?? $req['id'] }}">Reject</button>
                        @endif
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($tab ?? '') === 'approved' ? 7 : 6 }}" style="text-align: center; padding: 40px; color: #94a3b8;">
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
        if (textarea) textarea.focus();
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }
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
    }

    function closeApproveModal() {
        pendingApproveFormId = null;
        if (approveModal) {
            approveModal.classList.remove('is-open');
            approveModal.setAttribute('aria-hidden', 'true');
        }
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
})();
</script>

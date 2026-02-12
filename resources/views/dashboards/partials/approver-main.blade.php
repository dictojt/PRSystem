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
                <th>Request ID</th>
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
                <td>{{ $req['request_id'] ?? $req['id'] }}</td>
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
                            <form action="{{ route($approveRoute, ['id' => $req['id']]) }}?tab={{ urlencode($tab ?? '') }}" method="POST" class="form-inline" style="display:inline;">
                                @csrf
                                <input type="hidden" name="tab" value="{{ $tab ?? '' }}">
                                <button type="submit" class="btn-sm btn-approve">Approve</button>
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
})();
</script>

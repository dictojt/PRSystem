@extends('layouts.superadmin')
@section('title', 'All Requests')
@push('styles')
    <style>
        /* Tighter table spacing - All Requests only; smaller right padding to reduce gap */
        .table-card.all-requests-table .data-table th,
        .table-card.all-requests-table .data-table td {
            padding: 6px 10px;
        }
        .table-card.all-requests-table .data-table th:last-child,
        .table-card.all-requests-table .data-table td:last-child {
            padding-right: 6px;
        }
        .table-card.all-requests-table .data-table thead th {
            font-size: 11px;
        }

        /* More spacing between sections */
        .all-requests-page .header-section {
            margin-bottom: 24px;
        }
        .table-card.all-requests-table .card-title-bar {
            margin-bottom: 20px;
        }
        .table-card.all-requests-table .data-table tbody tr {
            border-spacing: 0;
        }
        .table-card.all-requests-table .data-table tbody td {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        /* Status tabs */
        .status-tabs .tab-link {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            color: #64748b;
            background: #f1f5f9;
        }

        .status-tabs .tab-link:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .status-tabs .tab-link.active {
            background: #1d4ed8;
            color: #fff;
        }

        /* Actions column: flexible so buttons expand to fill remaining space; smaller right gap */
        .all-requests-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            width: 100%;
            min-width: 0;
        }

        .all-requests-actions .btn-sm {
            white-space: nowrap;
        }

        .all-requests-actions form {
            display: flex;
            flex: 1;
            min-width: 32px;
        }

        .all-requests-actions form .btn-icon {
            flex: 1;
        }

        /* Each direct child (button or form) expands to fill space equally */
        .all-requests-actions > .btn-icon {
            flex: 1;
            min-width: 32px;
        }

        /* Icon-only action buttons with tooltip (title) */
        .all-requests-actions .btn-icon {
            width: 32px;
            min-width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            cursor: pointer;
            border: none;
        }
        .all-requests-actions form .btn-icon {
            width: auto;
        }
        .all-requests-actions .btn-icon .material-icons {
            font-size: 18px;
        }

        /* Edit modal */
        .edit-request-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .edit-request-modal-overlay.is-open {
            display: flex;
        }

        .edit-request-modal {
            background: #fff;
            border-radius: 12px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .edit-request-modal .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .edit-request-modal .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
        }

        .edit-request-modal .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #64748b;
            font-size: 22px;
            line-height: 1;
        }

        .edit-request-modal .modal-body {
            padding: 24px;
        }

        .edit-request-modal .form-group {
            margin-bottom: 16px;
        }

        .edit-request-modal .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            font-size: 14px;
            color: #334155;
        }

        .edit-request-modal .form-group input,
        .edit-request-modal .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .edit-request-modal .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .edit-request-modal .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .edit-request-modal .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }

        .edit-request-modal .btn-primary {
            background: #1d4ed8;
            color: #fff;
        }

        .edit-request-modal .btn-primary:hover {
            background: #1e40af;
        }

        .edit-request-modal .btn-secondary {
            background: #f1f5f9;
            color: #334155;
        }

        .edit-request-modal .btn-secondary:hover {
            background: #e2e8f0;
        }

        /* Confirmation modals (Approve / Reject) - unified success/danger design */
        .confirm-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1001;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirm-modal-overlay.is-open {
            display: flex;
        }

        .confirm-modal {
            background: #fff;
            border-radius: 12px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .confirm-modal .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .confirm-modal .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
        }

        .confirm-modal .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: #64748b;
            font-size: 22px;
            line-height: 1;
        }

        .confirm-modal .modal-close:hover {
            color: #334155;
        }

        .confirm-modal .modal-body {
            padding: 24px;
        }

        .confirm-modal .modal-body p {
            margin: 0 0 12px;
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
        }

        .confirm-modal .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }

        .confirm-modal .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }

        .confirm-modal .btn-cancel {
            background: #f1f5f9;
            color: #334155;
        }

        .confirm-modal .btn-cancel:hover {
            background: #e2e8f0;
        }

        /* Success action (Approve) - green */
        .confirm-modal .btn-success {
            background: #059669;
            color: #fff;
        }

        .confirm-modal .btn-success:hover {
            background: #047857;
        }

        /* Danger action (Reject) - red */
        .confirm-modal .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .confirm-modal .btn-danger:hover {
            background: #b91c1c;
        }

        .confirm-modal .form-group {
            margin-top: 12px;
        }

        .confirm-modal .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 4px;
        }

        .confirm-modal .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            min-height: 72px;
            resize: vertical;
        }

        /* Table action buttons */
        .all-requests-actions .btn-sm {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }

        .all-requests-actions .btn-edit-request {
            background: #1d4ed8;
            color: #fff;
        }

        .all-requests-actions .btn-edit-request:hover {
            background: #1e40af;
        }

        .all-requests-actions .btn-approve-request {
            background: #059669;
            color: #fff;
        }

        .all-requests-actions .btn-approve-request:hover {
            background: #047857;
        }

        .all-requests-actions .btn-reject-request {
            background: #dc2626;
            color: #fff;
        }

        .all-requests-actions .btn-reject-request:hover {
            background: #b91c1c;
        }

        .all-requests-actions .btn-archive-sm {
            background: #64748b;
            color: #fff;
        }

        .all-requests-actions .btn-archive-sm:hover {
            background: #475569;
        }

        .all-requests-actions .btn-success-sm {
            background: #059669;
            color: #fff;
        }

        .all-requests-actions .btn-success-sm:hover {
            background: #047857;
        }
    </style>
@endpush
@section('content')
    <div class="all-requests-page">
    @if(session('error'))
        <div class="alert-danger"
            style="margin-bottom: 16px; padding: 12px 16px; border-radius: 8px; background: #fef2f2; color: #b91c1c;">
            {{ session('error') }}</div>
    @endif
    <div class="header-section">
        <h1>All Requests</h1>
        <p>View and monitor all product requests.</p>
    </div>
    <div class="table-card all-requests-table">
        <div class="card-title-bar"
            style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <span>Requests</span>
            <nav class="status-tabs" style="display: flex; gap: 4px; flex-wrap: wrap;">
                <a href="{{ route('superadmin.requests', ['status' => 'all']) }}"
                    class="tab-link {{ ($filter ?? 'all') === 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('superadmin.requests', ['status' => 'pending']) }}"
                    class="tab-link {{ ($filter ?? 'all') === 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('superadmin.requests', ['status' => 'approved']) }}"
                    class="tab-link {{ ($filter ?? 'all') === 'approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('superadmin.requests', ['status' => 'rejected']) }}"
                    class="tab-link {{ ($filter ?? 'all') === 'rejected' ? 'active' : '' }}">Rejected</a>
                <a href="{{ route('superadmin.requests', ['status' => 'archived']) }}"
                    class="tab-link {{ ($filter ?? 'all') === 'archived' ? 'active' : '' }}">Archived</a>
            </nav>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Requestor</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr class="request-row" data-id="{{ $req->id }}" data-item-name="{{ e($req->item_name) }}"
                        data-quantity="{{ $req->quantity ?? 1 }}" data-description="{{ e($req->description ?? '') }}"
                        data-request-id="{{ e($req->request_id) }}" data-status="{{ e($req->status) }}">
                        <td>{{ $req->status === 'Approved' && $req->approved_id ? $req->approved_id : $req->request_id }}</td>
                        <td>{{ $req->user?->name ?? '—' }}</td>
                        <td>{{ $req->item_name }}</td>
                        <td>{{ $req->quantity ?? 1 }}</td>
                        <td>
                            @if($req->status === 'Pending')
                                <span class="badge badge-pending">Pending</span>
                            @elseif($req->status === 'Approved')
                                <span class="badge badge-approved">Approved</span>
                            @else
                                <span class="badge badge-rejected">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $req->created_at?->format('M d, Y') }}</td>
                        <td>
                            <div class="all-requests-actions">
                                @if(($filter ?? '') === 'archived')
                                    <button type="button" class="btn-sm btn-icon btn-edit-request" title="Edit"><span class="material-icons" aria-hidden="true">edit</span></button>
                                    <form action="{{ route('superadmin.requests.restore', $req->id) }}" method="POST"
                                        onsubmit="return confirm('Restore this request? It will appear in the main list again.');">
                                        @csrf
                                        <button type="submit" class="btn-sm btn-icon btn-success-sm" title="Restore"><span class="material-icons" aria-hidden="true">restore</span></button>
                                    </form>
                                @else
                                    @if($req->status === 'Pending')
                                        <button type="button" class="btn-sm btn-icon btn-approve-request" title="Approve"><span class="material-icons" aria-hidden="true">check_circle</span></button>
                                        <button type="button" class="btn-sm btn-icon btn-reject-request" title="Reject"><span class="material-icons" aria-hidden="true">cancel</span></button>
                                    @endif
                                    <button type="button" class="btn-sm btn-icon btn-edit-request" title="Edit"><span class="material-icons" aria-hidden="true">edit</span></button>
                                    <form action="{{ route('superadmin.requests.archive', $req->id) }}" method="POST"
                                        onsubmit="return confirm('Archive this request? It will be hidden from the main list.');">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $filter ?? 'all' }}">
                                        <button type="submit" class="btn-sm btn-icon btn-archive-sm" title="Archive"><span class="material-icons" aria-hidden="true">archive</span></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                            @if(($filter ?? 'all') === 'archived')
                                No archived requests.
                            @elseif(($filter ?? 'all') !== 'all')
                                No {{ $filter === 'pending' ? 'pending' : ($filter === 'approved' ? 'approved' : 'rejected') }}
                                requests.
                            @else
                                No requests yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(method_exists($requests, 'links'))
            <div style="padding: 16px 24px;">{{ $requests->links() }}</div>
        @endif
    </div>
    </div>

    {{-- Edit Request Modal --}}
    <div id="edit-request-modal-overlay" class="edit-request-modal-overlay" aria-hidden="true">
        <div class="edit-request-modal" role="dialog" aria-labelledby="edit-request-modal-title">
            <div class="modal-header">
                <h3 id="edit-request-modal-title">Edit Request</h3>
                <button type="button" class="modal-close" id="edit-request-modal-close" aria-label="Close">&times;</button>
            </div>
            <form id="edit-request-form" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="{{ $filter ?? 'all' }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-item-name">Item name</label>
                        <input type="text" id="edit-item-name" name="item_name" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Description</label>
                        <textarea id="edit-description" name="description" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-quantity">Quantity</label>
                        <input type="number" id="edit-quantity" name="quantity" required min="1" max="99999" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="edit-request-modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Approve Request Confirmation Modal (success style) --}}
    <div id="approve-request-modal-overlay" class="confirm-modal-overlay" aria-hidden="true">
        <div class="confirm-modal" role="dialog" aria-labelledby="approve-request-modal-title">
            <div class="modal-header">
                <h3 id="approve-request-modal-title">Approve Request</h3>
                <button type="button" class="modal-close" id="approve-request-modal-close"
                    aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="approve-request-modal-message">Approve this request? This will generate a 6-digit approved ID and
                    mark the item as approved.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="approve-request-modal-cancel">Cancel</button>
                <form id="approve-request-form" method="POST" action="" style="display:inline;">
                    @csrf
                    <input type="hidden" name="status" value="{{ $filter ?? 'all' }}">
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Request Confirmation Modal (danger style, optional reason) --}}
    <div id="reject-request-modal-overlay" class="confirm-modal-overlay" aria-hidden="true">
        <div class="confirm-modal" role="dialog" aria-labelledby="reject-request-modal-title">
            <div class="modal-header">
                <h3 id="reject-request-modal-title">Reject Request</h3>
                <button type="button" class="modal-close" id="reject-request-modal-close"
                    aria-label="Close">&times;</button>
            </div>
            <form id="reject-request-form" method="POST" action="">
                @csrf
                <input type="hidden" name="status" value="{{ $filter ?? 'all' }}">
                <div class="modal-body">
                    <p id="reject-request-modal-message">Reject this request? The requestor may see the reason if you
                        provide one below.</p>
                    <div class="form-group">
                        <label for="reject-reason">Reason (optional)</label>
                        <textarea id="reject-reason" name="rejection_reason" rows="3" maxlength="500"
                            placeholder="e.g. Budget not approved for this quarter."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" id="reject-request-modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                var overlay = document.getElementById('edit-request-modal-overlay');
                var form = document.getElementById('edit-request-form');
                var closeBtn = document.getElementById('edit-request-modal-close');
                var cancelBtn = document.getElementById('edit-request-modal-cancel');
                var baseUrl = '{{ url("/superadmin/requests") }}';
                var statusParam = '{{ $filter ?? "all" }}';
                document.querySelectorAll('.btn-edit-request').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var row = btn.closest('tr.request-row');
                        if (!row) return;
                        var id = row.getAttribute('data-id');
                        var itemName = row.getAttribute('data-item-name') || '';
                        var quantity = row.getAttribute('data-quantity') || '1';
                        var description = row.getAttribute('data-description') || '';
                        form.setAttribute('action', baseUrl + '/' + id + '?status=' + encodeURIComponent(statusParam));
                        document.getElementById('edit-item-name').value = itemName;
                        document.getElementById('edit-quantity').value = quantity;
                        document.getElementById('edit-description').value = description;
                        form.querySelector('input[name="status"]').value = statusParam;
                        overlay.classList.add('is-open');
                        overlay.setAttribute('aria-hidden', 'false');
                    });
                });
                function closeModal() {
                    overlay.classList.remove('is-open');
                    overlay.setAttribute('aria-hidden', 'true');
                }
                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) closeModal();
                });

                // Approve confirmation modal
                var approveOverlay = document.getElementById('approve-request-modal-overlay');
                var approveForm = document.getElementById('approve-request-form');
                var approveCloseBtn = document.getElementById('approve-request-modal-close');
                var approveCancelBtn = document.getElementById('approve-request-modal-cancel');
                if (approveOverlay && approveForm) {
                    document.querySelectorAll('.btn-approve-request').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var row = btn.closest('tr.request-row');
                            if (!row) return;
                            var id = row.getAttribute('data-id');
                            var requestId = row.getAttribute('data-request-id') || ('#' + id);
                            approveForm.setAttribute('action', '{{ url("/superadmin/requests") }}/' + id + '/approve?status=' + encodeURIComponent('{{ $filter ?? "all" }}'));
                            approveForm.querySelector('input[name="status"]').value = '{{ $filter ?? "all" }}';
                            var msgEl = document.getElementById('approve-request-modal-message');
                            if (msgEl) msgEl.innerHTML = 'Approve request <strong>' + (requestId || ('ID ' + id)) + '</strong>? This will generate a 6-digit approved ID and mark the item as approved.';
                            approveOverlay.classList.add('is-open');
                            approveOverlay.setAttribute('aria-hidden', 'false');
                        });
                    });
                    function closeApproveModal() {
                        approveOverlay.classList.remove('is-open');
                        approveOverlay.setAttribute('aria-hidden', 'true');
                    }
                    if (approveCloseBtn) approveCloseBtn.addEventListener('click', closeApproveModal);
                    if (approveCancelBtn) approveCancelBtn.addEventListener('click', closeApproveModal);
                    approveOverlay.addEventListener('click', function (e) {
                        if (e.target === approveOverlay) closeApproveModal();
                    });
                }

                // Reject confirmation modal
                var rejectOverlay = document.getElementById('reject-request-modal-overlay');
                var rejectForm = document.getElementById('reject-request-form');
                var rejectCloseBtn = document.getElementById('reject-request-modal-close');
                var rejectCancelBtn = document.getElementById('reject-request-modal-cancel');
                if (rejectOverlay && rejectForm) {
                    document.querySelectorAll('.btn-reject-request').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var row = btn.closest('tr.request-row');
                            if (!row) return;
                            var id = row.getAttribute('data-id');
                            var requestId = row.getAttribute('data-request-id') || ('#' + id);
                            rejectForm.setAttribute('action', '{{ url("/superadmin/requests") }}/' + id + '/reject');
                            rejectForm.querySelector('input[name="status"]').value = '{{ $filter ?? "all" }}';
                            var msgEl = document.getElementById('reject-request-modal-message');
                            if (msgEl) msgEl.innerHTML = 'Reject request <strong>' + (requestId || ('ID ' + id)) + '</strong>? The requestor may see the reason if you provide one below.';
                            var reasonEl = document.getElementById('reject-reason');
                            if (reasonEl) reasonEl.value = '';
                            rejectOverlay.classList.add('is-open');
                            rejectOverlay.setAttribute('aria-hidden', 'false');
                        });
                    });
                    function closeRejectModal() {
                        rejectOverlay.classList.remove('is-open');
                        rejectOverlay.setAttribute('aria-hidden', 'true');
                    }
                    if (rejectCloseBtn) rejectCloseBtn.addEventListener('click', closeRejectModal);
                    if (rejectCancelBtn) rejectCancelBtn.addEventListener('click', closeRejectModal);
                    rejectOverlay.addEventListener('click', function (e) {
                        if (e.target === rejectOverlay) closeRejectModal();
                    });
                }
            })();
        </script>
    @endpush
@endsection
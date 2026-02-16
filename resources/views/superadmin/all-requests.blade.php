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

        /* Filter by status dropdown (same as user View Request) */
        .all-requests-page .status-filter-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .all-requests-page .status-filter-dropdown {
            position: relative;
        }
        .all-requests-page .status-filter-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-width: 150px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .all-requests-page .status-filter-value {
            flex: 1;
            text-align: left;
        }
        .all-requests-page .status-filter-chevron {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat center;
        }
        .all-requests-page .status-filter-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 180px;
            padding: 6px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,.1);
            z-index: 50;
            display: none;
        }
        .all-requests-page .status-filter-dropdown.is-open .status-filter-menu {
            display: block;
        }
        .all-requests-page .status-filter-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .all-requests-page .status-filter-option:hover {
            background: #f1f5f9;
        }
        .all-requests-page .status-filter-option.selected {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .all-requests-page .status-filter-option-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .all-requests-page .status-dot-all { background: #94a3b8; }
        .all-requests-page .status-dot-pending { background: #f59e0b; }
        .all-requests-page .status-dot-approved { background: #10b981; }
        .all-requests-page .status-dot-rejected { background: #ef4444; }
        .all-requests-page .status-dot-archived { background: #64748b; }
        .all-requests-page .status-filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
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

        .all-requests-actions .btn-view-request-sa {
            background: #f1f5f9;
            color: #475569;
        }
        .all-requests-actions .btn-view-request-sa:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .table-card.all-requests-table .data-table td.item-cell-truncate {
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* View request modal - same as Approver (blue header, sections, footer) */
        #view-request-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
        }
        #view-request-modal-overlay.is-open {
            display: flex;
        }
        #view-request-modal-overlay .view-request-modal {
            background: #fff;
            border-radius: 12px;
            min-width: 360px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-height: calc(100vh - 48px);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        #view-request-modal-overlay .view-request-modal .modal-header {
            background: #2563eb;
            color: #fff;
            padding: 16px 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #view-request-modal-overlay .view-request-modal .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }
        #view-request-modal-overlay .view-request-modal .modal-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }
        #view-request-modal-overlay .view-request-modal .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        #view-request-modal-overlay .view-request-modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
            min-height: 0;
        }
        #view-request-modal-overlay .view-request-section {
            margin-bottom: 20px;
        }
        #view-request-modal-overlay .view-request-section:last-of-type {
            margin-bottom: 0;
        }
        #view-request-modal-overlay .view-request-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        #view-request-modal-overlay .view-request-dl {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 10px 20px;
            margin: 0;
            font-size: 14px;
        }
        #view-request-modal-overlay .view-request-dl dt {
            margin: 0;
            font-weight: 600;
            color: #64748b;
        }
        #view-request-modal-overlay .view-request-dl dd {
            margin: 0;
            color: #0f172a;
            word-break: break-word;
        }
        #view-request-modal-overlay .view-request-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-left: auto;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-action,
        #view-request-modal-overlay .view-request-modal-footer-actions .view-request-btn-archive,
        #view-request-modal-overlay .view-request-modal-footer-actions .view-request-btn-restore {
            display: none;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-action.is-visible {
            display: inline-flex;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-archive-form.is-visible,
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-restore-form.is-visible {
            display: inline-block;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-archive-form.is-visible .view-request-btn-archive,
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-restore-form.is-visible .view-request-btn-restore {
            display: inline-flex;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions .sa-view-modal-action .material-icons,
        #view-request-modal-overlay .view-request-modal-footer-actions .view-request-btn-archive .material-icons,
        #view-request-modal-overlay .view-request-modal-footer-actions .view-request-btn-restore .material-icons {
            font-size: 18px;
        }
        #view-request-modal-overlay .view-request-btn-approve {
            background: #10b981;
            color: #fff;
        }
        #view-request-modal-overlay .view-request-btn-approve:hover {
            background: #059669;
        }
        #view-request-modal-overlay .view-request-btn-reject {
            background: #ef4444;
            color: #fff;
        }
        #view-request-modal-overlay .view-request-btn-reject:hover {
            background: #dc2626;
        }
        #view-request-modal-overlay .view-request-btn-archive {
            background: #64748b;
            color: #fff;
        }
        #view-request-modal-overlay .view-request-btn-archive:hover {
            background: #475569;
        }
        #view-request-modal-overlay .view-request-btn-restore {
            background: #0ea5e9;
            color: #fff;
        }
        #view-request-modal-overlay .view-request-btn-restore:hover {
            background: #0284c7;
        }
        #view-request-modal-overlay .view-request-modal-footer-actions form {
            display: inline-block;
        }
        #view-request-modal-overlay .sa-view-req-rejection-label {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }
        #view-request-modal-overlay .sa-view-req-rejection-value {
            margin-top: 10px;
            padding-top: 10px;
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

        .confirm-modal .btn-archive-confirm {
            background: #64748b;
            color: #fff;
        }
        .confirm-modal .btn-archive-confirm:hover {
            background: #475569;
        }
        .confirm-modal .btn-restore-confirm {
            background: #0ea5e9;
            color: #fff;
        }
        .confirm-modal .btn-restore-confirm:hover {
            background: #0284c7;
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

        /* Pagination bar below table - clear layout and spacing */
        .all-requests-pagination {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            background: #fafbfc;
        }
        .all-requests-pagination nav[role="navigation"] {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        /* Hide mobile-only block on desktop so we don't get duplicate Previous/Next */
        .all-requests-pagination nav > div:first-child {
            display: none;
        }
        .all-requests-pagination nav > div:last-child {
            display: flex;
            flex: 1;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .all-requests-pagination nav p {
            margin: 0;
            font-size: 14px;
            color: #475569;
        }
        .all-requests-pagination nav p .font-medium {
            font-weight: 600;
            color: #1e293b;
        }
        /* Previous/Next + page numbers group */
        .all-requests-pagination nav > div:last-child > div:last-child {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .all-requests-pagination nav a,
        .all-requests-pagination nav span[aria-disabled] span,
        .all-requests-pagination nav span[aria-current="page"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            transition: background .15s, color .15s, border-color .15s;
        }
        .all-requests-pagination nav a:hover {
            background: #f1f5f9;
            color: #1d4ed8;
            border-color: #c7d2fe;
        }
        /* Active page: filled blue button, no underline */
        .all-requests-pagination nav span[aria-current="page"] span {
            background: #1d4ed8 !important;
            color: #fff !important;
            border-color: #1d4ed8 !important;
            border-bottom: 1px solid #1d4ed8 !important;
            box-shadow: none;
        }
        .all-requests-pagination nav span[aria-disabled] span {
            opacity: 0.6;
            cursor: not-allowed;
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
        <div class="card-title-bar" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <h2 style="margin: 0; font-size: 14px; font-weight: 600; color: #334155; text-transform: uppercase; letter-spacing: .05em;">Requests</h2>
            <div class="status-filter-wrap">
                <span class="status-filter-label">Filter by status</span>
                <div class="status-filter-dropdown" id="superadmin-status-filter-dropdown">
                    <button type="button" class="status-filter-trigger" id="superadmin-status-filter-trigger" aria-haspopup="listbox" aria-expanded="false" aria-label="Filter by status">
                        @php
                            $f = $filter ?? 'all';
                            $filterLabels = ['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'archived' => 'Archived'];
                            $filterLabel = $filterLabels[$f] ?? 'All';
                        @endphp
                        <span class="status-filter-value" id="superadmin-status-filter-value">{{ $filterLabel }}</span>
                        <span class="status-filter-chevron" aria-hidden="true"></span>
                    </button>
                    <div class="status-filter-menu" id="superadmin-status-filter-menu" role="listbox" aria-hidden="true">
                        <a href="{{ route('superadmin.requests', ['status' => 'all']) }}" class="status-filter-option {{ ($filter ?? 'all') === 'all' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-all"></span>
                            <span>All</span>
                        </a>
                        <a href="{{ route('superadmin.requests', ['status' => 'pending']) }}" class="status-filter-option {{ ($filter ?? 'all') === 'pending' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-pending"></span>
                            <span>Pending</span>
                        </a>
                        <a href="{{ route('superadmin.requests', ['status' => 'approved']) }}" class="status-filter-option {{ ($filter ?? 'all') === 'approved' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-approved"></span>
                            <span>Approved</span>
                        </a>
                        <a href="{{ route('superadmin.requests', ['status' => 'rejected']) }}" class="status-filter-option {{ ($filter ?? 'all') === 'rejected' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-rejected"></span>
                            <span>Rejected</span>
                        </a>
                        <a href="{{ route('superadmin.requests', ['status' => 'archived']) }}" class="status-filter-option {{ ($filter ?? 'all') === 'archived' ? 'selected' : '' }}">
                            <span class="status-filter-option-dot status-dot-archived"></span>
                            <span>Archived</span>
                        </a>
                    </div>
                </div>
            </div>
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
                    @php
                        $decidedAt = $req->approved_at ? $req->approved_at->format('M d, Y H:i') : ($req->rejected_at ? $req->rejected_at->format('M d, Y H:i') : '');
                        $decidedBy = $req->status === 'Approved' ? ($req->approvedBy?->name ?? '—') : ($req->status === 'Rejected' ? ($req->rejectedBy?->name ?? '—') : '');
                    @endphp
                    <tr class="request-row" data-id="{{ $req->id }}" data-item-name="{{ e($req->item_name) }}"
                        data-quantity="{{ $req->quantity ?? 1 }}" data-description="{{ e($req->description ?? '') }}"
                        data-request-id="{{ e($req->request_id) }}" data-status="{{ e($req->status) }}"
                        data-requestor="{{ e($req->user?->name ?? '—') }}" data-date="{{ $req->created_at?->format('M d, Y') }}"
                        data-decided-at="{{ e($decidedAt) }}" data-decided-by="{{ e($decidedBy) }}" data-rejection-reason="{{ e($req->rejection_reason ?? '') }}" data-approved-id="{{ e($req->approved_id ?? '') }}">
                        <td>{{ $req->status === 'Approved' && $req->approved_id ? $req->approved_id : $req->request_id }}</td>
                        <td>{{ $req->user?->name ?? '—' }}</td>
                        <td class="item-cell-truncate" title="{{ e($req->item_name) }}">{{ $req->item_name }}</td>
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
                                <button type="button" class="btn-sm btn-icon btn-view-request-sa" title="View details" aria-label="View request details"
                                    data-request-id="{{ $req->status === 'Approved' && $req->approved_id ? e($req->approved_id) : e($req->request_id) }}"
                                    data-requestor="{{ e($req->user?->name ?? '—') }}"
                                    data-item="{{ e($req->item_name) }}"
                                    data-quantity="{{ $req->quantity ?? 1 }}"
                                    data-description="{{ e($req->description ?? '') }}"
                                    data-date="{{ $req->created_at?->format('M d, Y') }}"
                                    data-status="{{ e($req->status) }}"
                                    data-decided-at="{{ e($decidedAt) }}"
                                    data-decided-by="{{ e($decidedBy) }}"
                                    data-rejection-reason="{{ e($req->rejection_reason ?? '') }}"><span class="material-icons" aria-hidden="true">visibility</span></button>
                                <button type="button" class="btn-sm btn-icon btn-edit-request" title="Edit"><span class="material-icons" aria-hidden="true">edit</span></button>
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
            <div class="all-requests-pagination">
                {{ $requests->links() }}
            </div>
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

    {{-- Archive Request Confirmation Modal --}}
    <div id="archive-request-modal-overlay" class="confirm-modal-overlay" aria-hidden="true">
        <div class="confirm-modal" role="dialog" aria-labelledby="archive-request-modal-title">
            <div class="modal-header">
                <h3 id="archive-request-modal-title">Archive Request</h3>
                <button type="button" class="modal-close" id="archive-request-modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="archive-request-modal-message">Archive this request? It will be hidden from the main list.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="archive-request-modal-cancel">Cancel</button>
                <button type="button" class="btn btn-archive-confirm" id="archive-request-modal-confirm">Archive</button>
            </div>
        </div>
    </div>

    {{-- Restore Request Confirmation Modal --}}
    <div id="restore-request-modal-overlay" class="confirm-modal-overlay" aria-hidden="true">
        <div class="confirm-modal" role="dialog" aria-labelledby="restore-request-modal-title">
            <div class="modal-header">
                <h3 id="restore-request-modal-title">Restore Request</h3>
                <button type="button" class="modal-close" id="restore-request-modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="restore-request-modal-message">Restore this request? It will appear in the main list again.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="restore-request-modal-cancel">Cancel</button>
                <button type="button" class="btn btn-restore-confirm" id="restore-request-modal-confirm">Restore</button>
            </div>
        </div>
    </div>

    {{-- View request details modal (Super Admin) - same structure as Approver --}}
    <div id="view-request-modal-overlay" class="view-request-modal-overlay" aria-hidden="true">
        <div class="view-request-modal" role="dialog" aria-labelledby="view-request-modal-title">
            <div class="modal-header">
                <h3 id="view-request-modal-title">Request details</h3>
                <button type="button" class="modal-close" id="view-request-modal-close" aria-label="Close">&times;</button>
            </div>
            <div class="view-request-modal-body">
                <section class="view-request-section">
                    <h3 class="view-request-section-title">Request Details</h3>
                    <dl class="view-request-dl">
                        <dt>Request ID</dt><dd id="sa-view-req-id">—</dd>
                        <dt>Requestor</dt><dd id="sa-view-req-requestor">—</dd>
                        <dt>Request date</dt><dd id="sa-view-req-date">—</dd>
                    </dl>
                </section>
                <section class="view-request-section">
                    <h3 class="view-request-section-title">Item Details</h3>
                    <dl class="view-request-dl">
                        <dt>Item name</dt><dd id="sa-view-req-item">—</dd>
                        <dt>Quantity</dt><dd id="sa-view-req-quantity">—</dd>
                        <dt>Description</dt><dd id="sa-view-req-description">—</dd>
                    </dl>
                </section>
                <section class="view-request-section">
                    <h3 class="view-request-section-title">Status &amp; Decision</h3>
                    <dl class="view-request-dl">
                        <dt>Status</dt><dd id="sa-view-req-status">—</dd>
                        <dt>Decided</dt><dd id="sa-view-req-decided-at">—</dd>
                        <dt>By</dt><dd id="sa-view-req-decided-by">—</dd>
                        <dt class="sa-view-req-rejection-label">Rejection reason</dt><dd class="sa-view-req-rejection-value" id="sa-view-req-rejection-reason">—</dd>
                    </dl>
                </section>
            </div>
            <div class="view-request-modal-footer">
                <div class="view-request-modal-footer-actions" id="sa-view-modal-footer-actions">
                    <button type="button" class="view-request-btn-approve sa-view-modal-action" id="sa-view-modal-btn-approve" title="Approve"><span class="material-icons">check_circle</span> Approve</button>
                    <button type="button" class="view-request-btn-reject sa-view-modal-action" id="sa-view-modal-btn-reject" title="Reject"><span class="material-icons">cancel</span> Reject</button>
                    <form action="" method="POST" class="sa-view-modal-archive-form" id="sa-view-modal-archive-form">
                        @csrf
                        <input type="hidden" name="status" value="{{ $filter ?? 'all' }}">
                        <button type="submit" class="view-request-btn-archive sa-view-modal-action" id="sa-view-modal-btn-archive" title="Archive"><span class="material-icons">archive</span> Archive</button>
                    </form>
                    <form action="" method="POST" class="sa-view-modal-restore-form" id="sa-view-modal-restore-form">
                        @csrf
                        <button type="submit" class="view-request-btn-restore sa-view-modal-action" id="sa-view-modal-btn-restore" title="Restore"><span class="material-icons">restore</span> Restore</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                var trigger = document.getElementById('superadmin-status-filter-trigger');
                var menu = document.getElementById('superadmin-status-filter-menu');
                var dropdown = document.getElementById('superadmin-status-filter-dropdown');
                if (trigger && menu && dropdown) {
                    trigger.addEventListener('click', function (e) {
                        e.stopPropagation();
                        var open = dropdown.classList.toggle('is-open');
                        menu.setAttribute('aria-hidden', !open);
                        trigger.setAttribute('aria-expanded', open);
                    });
                    document.addEventListener('click', function () {
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
                        if (document.body) document.body.classList.add('modal-open');
                        if (document.documentElement) document.documentElement.classList.add('modal-open');
                    });
                });
                function closeModal() {
                    overlay.classList.remove('is-open');
                    overlay.setAttribute('aria-hidden', 'true');
                    if (document.body) document.body.classList.remove('modal-open');
                    if (document.documentElement) document.documentElement.classList.remove('modal-open');
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
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        });
                    });
                    function closeApproveModal() {
                        approveOverlay.classList.remove('is-open');
                        approveOverlay.setAttribute('aria-hidden', 'true');
                        if (document.body) document.body.classList.remove('modal-open');
                        if (document.documentElement) document.documentElement.classList.remove('modal-open');
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
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        });
                    });
                    function closeRejectModal() {
                        rejectOverlay.classList.remove('is-open');
                        rejectOverlay.setAttribute('aria-hidden', 'true');
                        if (document.body) document.body.classList.remove('modal-open');
                        if (document.documentElement) document.documentElement.classList.remove('modal-open');
                    }
                    if (rejectCloseBtn) rejectCloseBtn.addEventListener('click', closeRejectModal);
                    if (rejectCancelBtn) rejectCancelBtn.addEventListener('click', closeRejectModal);
                    rejectOverlay.addEventListener('click', function (e) {
                        if (e.target === rejectOverlay) closeRejectModal();
                    });
                }

                // View request details modal
                var viewOverlay = document.getElementById('view-request-modal-overlay');
                var viewCloseBtn = document.getElementById('view-request-modal-close');
                var viewBaseUrl = '{{ url("/superadmin/requests") }}';
                var viewStatusParam = '{{ $filter ?? "all" }}';
                if (viewOverlay) {
                    document.querySelectorAll('.btn-view-request-sa').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var row = btn.closest('tr.request-row');
                            var dataId = row ? row.getAttribute('data-id') : null;
                            var id = btn.getAttribute('data-request-id') || '—';
                            var requestor = btn.getAttribute('data-requestor') || '—';
                            var item = btn.getAttribute('data-item') || '—';
                            var qty = btn.getAttribute('data-quantity') || '1';
                            var desc = (btn.getAttribute('data-description') || '').trim() || '—';
                            var date = btn.getAttribute('data-date') || '—';
                            var status = btn.getAttribute('data-status') || '—';
                            var decidedAt = (btn.getAttribute('data-decided-at') || '').trim() || '—';
                            var decidedBy = (btn.getAttribute('data-decided-by') || '').trim() || '—';
                            var rejectionReason = (btn.getAttribute('data-rejection-reason') || '').trim() || '—';
                            document.getElementById('sa-view-req-id').textContent = id;
                            document.getElementById('sa-view-req-requestor').textContent = requestor;
                            document.getElementById('sa-view-req-item').textContent = item;
                            document.getElementById('sa-view-req-quantity').textContent = qty;
                            document.getElementById('sa-view-req-description').textContent = desc;
                            document.getElementById('sa-view-req-date').textContent = date;
                            document.getElementById('sa-view-req-status').textContent = status;
                            document.getElementById('sa-view-req-decided-at').textContent = decidedAt;
                            document.getElementById('sa-view-req-decided-by').textContent = decidedBy;
                            document.getElementById('sa-view-req-rejection-reason').textContent = rejectionReason;
                            var rejLabel = document.querySelector('.sa-view-req-rejection-label');
                            var rejVal = document.querySelector('.sa-view-req-rejection-value');
                            if (rejLabel && rejVal) {
                                rejLabel.style.display = status === 'Rejected' ? '' : 'none';
                                rejVal.style.display = status === 'Rejected' ? '' : 'none';
                            }
                            viewOverlay.setAttribute('data-current-id', dataId || '');
                            viewOverlay.setAttribute('data-current-status', status);
                            viewOverlay.setAttribute('data-current-status-filter', viewStatusParam);
                            var btnApprove = document.getElementById('sa-view-modal-btn-approve');
                            var btnReject = document.getElementById('sa-view-modal-btn-reject');
                            var formArchive = document.getElementById('sa-view-modal-archive-form');
                            var formRestore = document.getElementById('sa-view-modal-restore-form');
                            btnApprove.classList.remove('is-visible');
                            btnReject.classList.remove('is-visible');
                            formArchive.classList.remove('is-visible');
                            formRestore.classList.remove('is-visible');
                            if (status === 'Pending') {
                                btnApprove.classList.add('is-visible');
                                btnReject.classList.add('is-visible');
                            }
                            if (viewStatusParam === 'archived') {
                                formRestore.classList.add('is-visible');
                                if (formRestore) formRestore.setAttribute('action', viewBaseUrl + '/' + dataId + '/restore');
                            } else {
                                formArchive.classList.add('is-visible');
                                if (formArchive) {
                                    formArchive.setAttribute('action', viewBaseUrl + '/' + dataId + '/archive');
                                    var archiveStatusInput = formArchive.querySelector('input[name="status"]');
                                    if (archiveStatusInput) archiveStatusInput.value = viewStatusParam;
                                }
                            }
                            viewOverlay.classList.add('is-open');
                            viewOverlay.setAttribute('aria-hidden', 'false');
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        });
                    });
                    function closeViewModal() {
                        viewOverlay.classList.remove('is-open');
                        viewOverlay.setAttribute('aria-hidden', 'true');
                        if (document.body) document.body.classList.remove('modal-open');
                        if (document.documentElement) document.documentElement.classList.remove('modal-open');
                    }
                    if (viewCloseBtn) viewCloseBtn.addEventListener('click', closeViewModal);
                    viewOverlay.addEventListener('click', function (e) {
                        if (e.target === viewOverlay) closeViewModal();
                    });
                    viewOverlay.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') closeViewModal();
                    });
                    var saViewBtnApprove = document.getElementById('sa-view-modal-btn-approve');
                    var saViewBtnReject = document.getElementById('sa-view-modal-btn-reject');
                    if (saViewBtnApprove && approveForm && approveOverlay) {
                        saViewBtnApprove.addEventListener('click', function () {
                            var dataId = viewOverlay.getAttribute('data-current-id');
                            var requestId = document.getElementById('sa-view-req-id').textContent;
                            if (!dataId) return;
                            approveForm.setAttribute('action', viewBaseUrl + '/' + dataId + '/approve?status=' + encodeURIComponent(viewStatusParam));
                            approveForm.querySelector('input[name="status"]').value = viewStatusParam;
                            var msgEl = document.getElementById('approve-request-modal-message');
                            if (msgEl) msgEl.innerHTML = 'Approve request <strong>' + (requestId || ('ID ' + dataId)) + '</strong>? This will generate a 6-digit approved ID and mark the item as approved.';
                            closeViewModal();
                            approveOverlay.classList.add('is-open');
                            approveOverlay.setAttribute('aria-hidden', 'false');
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        });
                    }
                    if (saViewBtnReject && rejectForm && rejectOverlay) {
                        saViewBtnReject.addEventListener('click', function () {
                            var dataId = viewOverlay.getAttribute('data-current-id');
                            var requestId = document.getElementById('sa-view-req-id').textContent;
                            if (!dataId) return;
                            rejectForm.setAttribute('action', viewBaseUrl + '/' + dataId + '/reject?status=' + encodeURIComponent(viewStatusParam));
                            rejectForm.querySelector('input[name="status"]').value = viewStatusParam;
                            var msgEl = document.getElementById('reject-request-modal-message');
                            if (msgEl) msgEl.innerHTML = 'Reject request <strong>' + (requestId || ('ID ' + dataId)) + '</strong>? The requestor may see the reason if you provide one below.';
                            closeViewModal();
                            rejectOverlay.classList.add('is-open');
                            rejectOverlay.setAttribute('aria-hidden', 'false');
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        });
                    }
                    var formArchive = document.getElementById('sa-view-modal-archive-form');
                    var formRestore = document.getElementById('sa-view-modal-restore-form');
                    var archiveOverlay = document.getElementById('archive-request-modal-overlay');
                    var archiveCloseBtn = document.getElementById('archive-request-modal-close');
                    var archiveCancelBtn = document.getElementById('archive-request-modal-cancel');
                    var archiveConfirmBtn = document.getElementById('archive-request-modal-confirm');
                    var restoreOverlay = document.getElementById('restore-request-modal-overlay');
                    var restoreCloseBtn = document.getElementById('restore-request-modal-close');
                    var restoreCancelBtn = document.getElementById('restore-request-modal-cancel');
                    var restoreConfirmBtn = document.getElementById('restore-request-modal-confirm');
                    function openArchiveConfirmModal() {
                        if (archiveOverlay) {
                            archiveOverlay.classList.add('is-open');
                            archiveOverlay.setAttribute('aria-hidden', 'false');
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        }
                    }
                    function closeArchiveConfirmModal() {
                        if (archiveOverlay) {
                            archiveOverlay.classList.remove('is-open');
                            archiveOverlay.setAttribute('aria-hidden', 'true');
                            if (document.body) document.body.classList.remove('modal-open');
                            if (document.documentElement) document.documentElement.classList.remove('modal-open');
                        }
                    }
                    function openRestoreConfirmModal() {
                        if (restoreOverlay) {
                            restoreOverlay.classList.add('is-open');
                            restoreOverlay.setAttribute('aria-hidden', 'false');
                            if (document.body) document.body.classList.add('modal-open');
                            if (document.documentElement) document.documentElement.classList.add('modal-open');
                        }
                    }
                    function closeRestoreConfirmModal() {
                        if (restoreOverlay) {
                            restoreOverlay.classList.remove('is-open');
                            restoreOverlay.setAttribute('aria-hidden', 'true');
                            if (document.body) document.body.classList.remove('modal-open');
                            if (document.documentElement) document.documentElement.classList.remove('modal-open');
                        }
                    }
                    if (formArchive) {
                        formArchive.addEventListener('submit', function (e) {
                            e.preventDefault();
                            openArchiveConfirmModal();
                        });
                    }
                    if (formRestore) {
                        formRestore.addEventListener('submit', function (e) {
                            e.preventDefault();
                            openRestoreConfirmModal();
                        });
                    }
                    if (archiveConfirmBtn && formArchive) {
                        archiveConfirmBtn.addEventListener('click', function () {
                            closeArchiveConfirmModal();
                            formArchive.submit();
                        });
                    }
                    if (archiveCloseBtn) archiveCloseBtn.addEventListener('click', closeArchiveConfirmModal);
                    if (archiveCancelBtn) archiveCancelBtn.addEventListener('click', closeArchiveConfirmModal);
                    if (archiveOverlay) archiveOverlay.addEventListener('click', function (e) { if (e.target === archiveOverlay) closeArchiveConfirmModal(); });
                    if (restoreConfirmBtn && formRestore) {
                        restoreConfirmBtn.addEventListener('click', function () {
                            closeRestoreConfirmModal();
                            formRestore.submit();
                        });
                    }
                    if (restoreCloseBtn) restoreCloseBtn.addEventListener('click', closeRestoreConfirmModal);
                    if (restoreCancelBtn) restoreCancelBtn.addEventListener('click', closeRestoreConfirmModal);
                    if (restoreOverlay) restoreOverlay.addEventListener('click', function (e) { if (e.target === restoreOverlay) closeRestoreConfirmModal(); });
                }
            })();
        </script>
    @endpush
@endsection
@if(request()->header('X-Partial-Content') === '1')
@php
    $__approverTitle = 'Approver Panel - Product Request System | DICT';
    if (($tab ?? '') === 'pending') { $__approverTitle = 'Pending Requests - Product Request System | DICT'; }
    elseif (($tab ?? '') === 'approved') { $__approverTitle = 'Approved & Rejected - Product Request System | DICT'; }
@endphp
<div data-page-title="{{ e($__approverTitle) }}">
    @include('dashboards.partials.approver-main')
</div>
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approver Panel - Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/approver.css', 'resources/js/approver.js'])
    <style>
        /* Critical reject modal - inline so it always applies (avoids cache/override) */
        #rejectModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 9999 !important;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.2s, opacity 0.2s;
            pointer-events: none;
        }
        #rejectModal.is-open {
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        #rejectModal .reject-modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0, 0, 0, 0.5) !important;
            z-index: 1 !important;
        }
        #rejectModal .reject-modal-content {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            z-index: 2 !important;
            background: #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.04) !important;
            padding: 28px 28px 24px !important;
            width: calc(100% - 48px) !important;
            max-width: 440px !important;
            max-height: calc(100vh - 48px) !important;
            overflow-y: auto !important;
        }
        #rejectModal .reject-modal-header {
            margin-bottom: 6px !important;
        }
        #rejectModal .reject-modal-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
        #rejectModal .reject-modal-title .material-icons {
            font-size: 24px !important;
            color: #dc2626 !important;
        }
        #rejectModal .reject-modal-desc {
            font-size: 14px !important;
            color: #64748b !important;
            line-height: 1.5 !important;
            margin: 0 0 20px 0 !important;
        }
        #rejectModal .reject-modal-field {
            margin-bottom: 6px !important;
        }
        #rejectModal .reject-modal-label {
            display: block !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 8px !important;
        }
        #rejectModal .reject-modal-textarea {
            display: block !important;
            width: 100% !important;
            min-height: 108px !important;
            height: 108px !important;
            padding: 12px 14px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            font-size: 14px !important;
            font-family: inherit !important;
            box-sizing: border-box !important;
            resize: vertical !important;
            background: #f8fafc !important;
            transition: border-color 0.2s, box-shadow 0.2s !important;
        }
        #rejectModal .reject-modal-textarea::placeholder {
            color: #94a3b8 !important;
        }
        #rejectModal .reject-modal-textarea:focus {
            outline: none !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
            background: #fff !important;
        }
        #rejectModal .reject-modal-char {
            display: block !important;
            font-size: 12px !important;
            color: #94a3b8 !important;
            margin-top: 6px !important;
            text-align: right !important;
        }
        #rejectModal .reject-modal-actions {
            display: flex !important;
            justify-content: flex-end !important;
            gap: 12px !important;
            margin-top: 24px !important;
            padding-top: 20px !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        #rejectModal .btn-reject-modal-cancel {
            padding: 10px 18px !important;
            border-radius: 10px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }
        #rejectModal .btn-reject-modal-confirm {
            padding: 10px 18px !important;
            border-radius: 10px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            background: #dc2626 !important;
            color: #fff !important;
            border: none !important;
        }
        #rejectModal .btn-reject-modal-confirm:hover {
            background: #b91c1c !important;
        }
    </style>
</head>
<body>
 
<div id="rejectModal" class="reject-modal" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle" aria-hidden="true">
    <div class="reject-modal-backdrop" id="rejectModalBackdrop"></div>
    <div class="reject-modal-content">
        <div class="reject-modal-header">
            <h2 id="rejectModalTitle" class="reject-modal-title">
                <span class="material-icons" aria-hidden="true">cancel</span>
                Reject Request
            </h2>
        </div>
        <p class="reject-modal-desc">Provide a reason for rejection (optional). The requestor will see this reason.</p>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <input type="hidden" name="tab" id="rejectFormTab" value="">
            <div class="reject-modal-field">
                <label for="rejection_reason" class="reject-modal-label">Rejection reason</label>
                <textarea name="rejection_reason" id="rejection_reason" class="reject-modal-textarea" rows="4" maxlength="500" placeholder="e.g. Budget not approved for this quarter."></textarea>
                <span class="reject-modal-char" id="rejectCharCount">0 / 500</span>
            </div>
            <div class="reject-modal-actions">
                <button type="button" class="btn-sm btn-reject-modal-cancel" id="rejectModalCancel">Cancel</button>
                <button type="submit" class="btn-sm btn-reject-modal-confirm">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

<div class="container">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><span class="material-icons">verified_user</span><span class="sidebar-label">PRS Approver</span></h2>
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Collapse sidebar">
                <span class="material-icons">chevron_left</span>
            </button>
        </div>
        @php $u = auth()->user(); @endphp
        <div class="profile-card">
            <div class="profile-avatar">{{ $u ? strtoupper(substr($u->name ?? 'U', 0, 1)) : 'G' }}</div>
            <div class="profile-info">
                <div class="profile-name">{{ $u ? ($u->name ?? 'Approver') : 'Guest' }}</div>
                <div class="profile-email">{{ $u ? ($u->email ?? '') : '' }}</div>
            </div>
        </div>
        <div class="menu-top">
            @php
                $approverDashboard = auth()->check() ? route('approver.dashboard') : route('approver.guest');
            @endphp
            <a href="{{ $approverDashboard }}" class="{{ ($tab ?? '') === '' ? 'active' : '' }}" title="Overview">
                <span class="material-icons">dashboard</span><span class="sidebar-label">Overview</span>
            </a>
            <a href="{{ $approverDashboard }}?tab=pending" class="{{ ($tab ?? '') === 'pending' ? 'active' : '' }}" title="Pending Requests">
                <span class="material-icons">pending_actions</span><span class="sidebar-label">Pending Requests</span>
            </a>
            <a href="{{ $approverDashboard }}?tab=approved" class="{{ ($tab ?? '') === 'approved' ? 'active' : '' }}" title="Approved & Rejected">
                <span class="material-icons">assignment_turned_in</span><span class="sidebar-label">Approved & Rejected</span>
            </a>
        </div>
        <div class="logout">
            @auth
            <a href="{{ route('logout') }}" class="logout-link-get" onclick="return confirm('Are you sure you want to logout?')" style="text-decoration: none; width: 100%;" title="Logout">
                <span class="material-icons">logout</span><span class="sidebar-label">Logout</span>
            </a>
            @else
            <a href="{{ route('home') }}" class="logout-link" title="Sign in">
                <span class="material-icons">login</span><span class="sidebar-label">Sign in</span>
            </a>
            @endauth
        </div>
    </div>

    <div class="main">
        @include('dashboards.partials.approver-main')
    </div>
</div>

</body>
</html>
@endif

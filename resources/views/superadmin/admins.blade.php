@extends('layouts.superadmin')
@section('title', 'Admin Management')
@section('content')
    <div class="header-section">
        <h1>Admin Management</h1>
        <p>All system users — administrators, approvers, and requesters.</p>
    </div>

    @if(session('error'))
        <div class="alert-danger" style="margin-bottom: 16px;">{{ session('error') }}</div>
    @endif
    @if($errors->has('otp'))
        <div class="alert-danger" style="margin-bottom: 16px;">{{ $errors->first('otp') }}</div>
    @endif

    @auth
        @if(auth()->user()->role === 'superadmin')
            <div class="table-card admin-management-card" style="margin-bottom: 24px;">
                <div class="card-title-bar admin-management-header">
                    <div class="admin-management-filters">
                        <label class="admin-search-wrap" for="adminSearchInput">
                            <span class="material-icons">search</span>
                            <input id="adminSearchInput" type="text" placeholder="Search">
                        </label>
                        <select id="adminRoleFilter" class="admin-filter-select" aria-label="Filter by role">
                            <option value="all">Role</option>
                            <option value="superadmin">Superadmin</option>
                            <option value="approver">Approver</option>
                            <option value="user">User</option>
                        </select>
                        <select id="adminStatusFilter" class="admin-filter-select" aria-label="Filter by status">
                            <option value="all">Status</option>
                            <option value="active">Active</option>
                            <option value="deactivated">Deactivated</option>
                        </select>
                        <select id="adminDateFilter" class="admin-filter-select" aria-label="Filter by date">
                            <option value="all">Date</option>
                            <option value="last30">Last 30 days</option>
                            <option value="thisYear">This year</option>
                            <option value="older">Older</option>
                        </select>
                    </div>
                    <button type="button" class="admin-add-user-btn" onclick="openAddUserModal()">
                        <span>Add User</span>
                        <span class="material-icons">person_add</span>
                    </button>
                </div>
                <div class="admin-table-wrap">
                <table class="data-table admin-management-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                            <th>Last Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins ?? [] as $admin)
                            @php
                                $isDeactivated = isset($admin->is_active) && $admin->is_active === false;
                                $statusValue = $isDeactivated ? 'deactivated' : 'active';
                                $statusLabel = $isDeactivated ? 'Deactivated' : 'Active';
                                $username = \Illuminate\Support\Str::before((string) $admin->email, '@');
                                $joinedAt = $admin->created_at;
                                $lastActiveAt = $admin->updated_at ?? $admin->created_at;
                            @endphp
                            <tr data-admin-row
                                data-name="{{ strtolower((string) ($admin->name ?? '')) }}"
                                data-email="{{ strtolower((string) ($admin->email ?? '')) }}"
                                data-username="{{ strtolower((string) $username) }}"
                                data-role="{{ strtolower((string) ($admin->role ?? 'user')) }}"
                                data-status="{{ $statusValue }}"
                                data-joined-ts="{{ $joinedAt?->timestamp ?? 0 }}">
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $username !== '' ? $username : '-' }}</td>
                                <td>
                                    <span class="admin-status-badge {{ $statusValue === 'active' ? 'status-active' : 'status-deactivated' }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td><span
                                        class="badge {{ $admin->role === 'superadmin' ? 'badge-approved' : ($admin->role === 'approver' ? 'badge-pending' : '') }}">{{ ucfirst($admin->role ?? 'user') }}</span>
                                </td>
                                <td>{{ $joinedAt?->format('M d, Y') }}</td>
                                <td>{{ $lastActiveAt?->diffForHumans() ?? '-' }}</td>
                                <td>
                                    @if(auth()->id() !== $admin->id)
                                        <div class="action-buttons">
                                            <button type="button" class="btn-sm btn-action-icon"
                                                onclick="openEditRoleModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ $admin->role }}')"
                                                title="Edit role">
                                                <span class="material-icons">edit</span>
                                            </button>
                                            @if($isDeactivated)
                                                <form method="post" action="{{ route('superadmin.admins.reactivate', $admin) }}"
                                                    style="display:inline;" class="form-require-reauth" data-action="reactivate"
                                                    data-user-id="{{ $admin->id }}" data-label="Reactivate {{ $admin->name }}">
                                                    @csrf
                                                    <button type="button" class="btn-sm btn-action-icon"
                                                        title="Reactivate user">
                                                        <span class="material-icons">check_circle</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="post" action="{{ route('superadmin.admins.deactivate', $admin) }}"
                                                    style="display:inline;" class="form-require-reauth" data-action="deactivate"
                                                    data-user-id="{{ $admin->id }}" data-label="Deactivate {{ $admin->name }}">
                                                    @csrf
                                                    <button type="button" class="btn-sm btn-action-icon deactivate"
                                                        title="Deactivate user">
                                                        <span class="material-icons">block</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 12px;">(you)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

        @else
            <div class="table-card">
                <div class="card-title-bar">All Users</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins ?? [] as $admin)
                            <tr>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td><span
                                        class="badge {{ $admin->role === 'superadmin' ? 'badge-approved' : ($admin->role === 'approver' ? 'badge-pending' : '') }}">{{ ucfirst($admin->role ?? 'user') }}</span>
                                </td>
                                <td>{{ $admin->created_at?->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <div class="table-card">
            <div class="card-title-bar">All Users</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins ?? [] as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td><span
                                    class="badge {{ $admin->role === 'superadmin' ? 'badge-approved' : ($admin->role === 'approver' ? 'badge-pending' : '') }}">{{ ucfirst($admin->role ?? 'user') }}</span>
                            </td>
                            <td>{{ $admin->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endauth

    {{-- Add User Modal --}}
    <div id="addUserModal" class="modal-overlay" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Add User</h3>
                <button type="button" class="modal-close" onclick="closeAddUserModal()">&times;</button>
            </div>
            <form method="post" action="{{ route('superadmin.admins.add') }}" id="addUserForm">
                @csrf
                <div class="modal-body">
                    <label>Email <input type="email" name="email" required placeholder="email@example.com"></label>
                    <label>Role
                        <select name="role" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="approver">Approver</option>
                        </select>
                    </label>
                    @include('superadmin.partials.reauth-fields')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Add user</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Role Modal --}}
    <div id="editRoleModal" class="modal-overlay" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Role</h3>
                <button type="button" class="modal-close" onclick="closeEditRoleModal()">&times;</button>
            </div>
            <form method="post" action="" id="editRoleForm" class="form-put">
                @csrf
                @method('put')
                <div class="modal-body">
                    <p id="editRoleUserName" style="margin-bottom: 12px;"></p>
                    <label>Role
                        <select name="role" id="editRoleSelect" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="approver">Approver</option>
                        </select>
                    </label>
                    @include('superadmin.partials.reauth-fields')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditRoleModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update role</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Re-auth confirmation modal (for Deactivate / Reactivate) --}}
    <div id="reauthModal" class="modal-overlay" style="display: none;">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Confirm your identity</h3>
                <button type="button" class="modal-close" onclick="closeReauthModal()">&times;</button>
            </div>
            <form method="post" action="" id="reauthForm">
                @csrf
                <div class="modal-body">
                    <p id="reauthLabel" style="margin-bottom: 12px;"></p>
                    @include('superadmin.partials.reauth-fields')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeReauthModal()">Cancel</button>
                    <button type="submit" class="btn-primary" id="reauthSubmitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: var(--card-white);
            border-radius: 12px;
            max-width: 440px;
            width: 100%;
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #64748b;
            line-height: 1;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-body label {
            display: block;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .modal-body label input,
        .modal-body label select {
            display: block;
            width: 100%;
            margin-top: 6px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .admin-management-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px;
        }

        .admin-management-filters {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            flex: 1;
        }

        .admin-search-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 220px;
            background: #fff;
            border: 1px solid #d7deea;
            border-radius: 10px;
            padding: 8px 10px;
        }

        .admin-search-wrap .material-icons {
            font-size: 18px;
            color: #64748b;
        }

        .admin-search-wrap input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 13px;
            color: #0f172a;
        }

        .admin-filter-select {
            min-width: 120px;
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #fff;
            padding: 8px 10px;
            font-size: 13px;
            color: #334155;
            outline: none;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .admin-filter-select:hover {
            border-color: #93c5fd;
            background: #f8fbff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .admin-filter-select:focus {
            border-color: #2563eb;
            background: #f8fbff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .admin-filter-select option {
            color: #1e293b;
            background: #ffffff;
        }

        .admin-filter-select option:hover,
        .admin-filter-select option:focus {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .admin-filter-select option:checked {
            background: #dbeafe;
            color: #1e3a8a;
        }

        #adminStatusFilter option[value="deactivated"] {
            color: #b91c1c;
        }

        #adminStatusFilter option[value="deactivated"]:hover,
        #adminStatusFilter option[value="deactivated"]:focus,
        #adminStatusFilter option[value="deactivated"]:checked {
            background: #fee2e2;
            color: #991b1b;
        }

        .admin-add-user-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #2748c8;
            background: linear-gradient(180deg, #5b7bff 0%, #4169e1 100%);
            color: #ffffff;
            padding: 0 24px;
            min-height: 44px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.12s ease;
            box-shadow: 0 1px 2px rgba(65, 105, 225, 0.24);
        }

        .admin-add-user-btn:hover {
            background: linear-gradient(180deg, #4b6de8 0%, #355bd5 100%);
            box-shadow: 0 4px 10px rgba(65, 105, 225, 0.32);
        }

        .admin-add-user-btn:active {
            transform: translateY(1px);
        }

        .admin-add-user-btn .material-icons {
            font-size: 19px;
            color: #ffffff;
            opacity: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 9px;
            background: rgba(18, 52, 172, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.32);
        }

        .admin-table-wrap {
            overflow-x: auto;
        }

        .admin-management-table th {
            font-size: 11px;
            letter-spacing: 0.04em;
        }

        .admin-management-table td {
            vertical-align: middle;
            font-size: 13px;
            color: #334155;
        }

        .admin-status-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .admin-status-badge.status-active {
            background: #dcfce7;
            color: #15803d;
        }

        .admin-status-badge.status-deactivated {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 300;
            cursor: pointer;
            font-size: 10px;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-dark);
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-action-icon {
            width: 30px;
            height: 30px;
            border: 1px solid #dbe4f0;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .btn-action-icon:hover {
            background: #f8fafc;
        }

        .btn-action-icon .material-icons {
            font-size: 16px;
        }

        .btn-action-icon.deactivate {
            color: #b91c1c;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .btn-action-icon.deactivate:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .alert-danger {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .reauth-block {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .reauth-block p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .send-otp-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            margin-bottom: 8px;
        }

        .send-otp-btn:hover {
            background: #e2e8f0;
        }

        .otp-sent {
            font-size: 12px;
            color: var(--success);
            margin-bottom: 8px;
        }

        @media (max-width: 860px) {
            .admin-search-wrap {
                min-width: 100%;
            }

            .admin-filter-select {
                min-width: 100%;
            }

            .admin-add-user-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            // This page can be injected by sidebar AJAX navigation after DOMContentLoaded.
            // Use delegated handlers and bind once globally.
            if (window.__superadminAdminsHandlersBound) return;
            window.__superadminAdminsHandlersBound = true;

            var OTP_COOLDOWN_SECONDS = 300; // 5 minutes

            function formatCountdown(sec) {
                var m = Math.floor(sec / 60);
                var s = sec % 60;
                return m + ':' + (s < 10 ? '0' : '') + s;
            }

            function startOtpCooldown(btnEl) {
                var block = btnEl.closest('.reauth-block');
                var sent = block && block.querySelector('.otp-sent');
                if (sent) sent.style.display = 'block';
                var err = block && block.querySelector('.otp-error');
                if (err) err.style.display = 'none';
                btnEl.disabled = true;

                var remaining = OTP_COOLDOWN_SECONDS;
                btnEl.textContent = 'Resend in ' + formatCountdown(remaining);
                var interval = setInterval(function () {
                    remaining--;
                    btnEl.textContent = 'Resend in ' + formatCountdown(remaining);
                    if (remaining <= 0) {
                        clearInterval(interval);
                        btnEl.disabled = false;
                        btnEl.textContent = 'Resend OTP';
                    }
                }, 1000);
            }

            function applyAdminFilters() {
                var rows = document.querySelectorAll('[data-admin-row]');
                if (!rows.length) return;

                var searchEl = document.getElementById('adminSearchInput');
                var roleEl = document.getElementById('adminRoleFilter');
                var statusEl = document.getElementById('adminStatusFilter');
                var dateEl = document.getElementById('adminDateFilter');

                var searchValue = (searchEl && searchEl.value ? searchEl.value : '').trim().toLowerCase();
                var roleValue = roleEl ? roleEl.value : 'all';
                var statusValue = statusEl ? statusEl.value : 'all';
                var dateValue = dateEl ? dateEl.value : 'all';

                var nowSec = Math.floor(Date.now() / 1000);
                var thirtyDaysAgo = nowSec - (30 * 24 * 60 * 60);
                var thisYear = new Date().getFullYear();

                rows.forEach(function (row) {
                    var haystack = [
                        row.dataset.name || '',
                        row.dataset.email || '',
                        row.dataset.username || ''
                    ].join(' ');

                    var joinedTs = parseInt(row.dataset.joinedTs || '0', 10);
                    var joinedDate = joinedTs > 0 ? new Date(joinedTs * 1000) : null;
                    var matchesSearch = searchValue === '' || haystack.indexOf(searchValue) !== -1;
                    var matchesRole = roleValue === 'all' || (row.dataset.role || '') === roleValue;
                    var matchesStatus = statusValue === 'all' || (row.dataset.status || '') === statusValue;
                    var matchesDate = true;

                    if (dateValue === 'last30') {
                        matchesDate = joinedTs >= thirtyDaysAgo;
                    } else if (dateValue === 'thisYear') {
                        matchesDate = !!joinedDate && joinedDate.getFullYear() === thisYear;
                    } else if (dateValue === 'older') {
                        matchesDate = joinedTs > 0 && joinedTs < thirtyDaysAgo;
                    }

                    row.style.display = (matchesSearch && matchesRole && matchesStatus && matchesDate) ? '' : 'none';
                });
            }

            document.addEventListener('click', function (event) {
                var target = event.target;
                if (!target || typeof target.closest !== 'function') return;

                var otpButton = target.closest('.send-otp-btn');
                if (otpButton) {
                    if (otpButton.disabled) return;

                    otpButton.disabled = true;
                    var tokenEl = document.querySelector('meta[name="csrf-token"]');
                    var token = tokenEl ? tokenEl.content : '';

                    fetch('{{ route("superadmin.send-otp") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token || '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({})
                    }).then(function (r) {
                        return r.json().then(function (data) {
                            return { ok: r.ok, data: data };
                        });
                    }).then(function (result) {
                        var block = otpButton.closest('.reauth-block');
                        if (result.ok) {
                            startOtpCooldown(otpButton);
                            return;
                        }

                        var err = block && block.querySelector('.otp-error');
                        if (err) {
                            err.textContent = (result.data && result.data.message) || 'Failed to send OTP. Try again.';
                            err.style.display = 'block';
                        }
                        var sent = block && block.querySelector('.otp-sent');
                        if (sent) sent.style.display = 'none';
                        otpButton.disabled = false;
                        if (otpButton.textContent.indexOf('Resend in') !== 0) otpButton.textContent = 'Send OTP to my email';
                    }).catch(function () {
                        var block = otpButton.closest('.reauth-block');
                        var err = block && block.querySelector('.otp-error');
                        if (err) {
                            err.textContent = 'Failed to send OTP. Check your connection and try again.';
                            err.style.display = 'block';
                        }
                        otpButton.disabled = false;
                        if (otpButton.textContent.indexOf('Resend in') !== 0) otpButton.textContent = 'Send OTP to my email';
                    });

                    return;
                }

                var reauthTrigger = target.closest('.form-require-reauth button[type="button"]');
                if (reauthTrigger) {
                    var form = reauthTrigger.closest('.form-require-reauth');
                    if (form) openReauthModal(form);
                }
            });

            document.addEventListener('input', function (event) {
                var t = event.target;
                if (!t) return;
                if (t.id === 'adminSearchInput') applyAdminFilters();
            });

            document.addEventListener('change', function (event) {
                var t = event.target;
                if (!t) return;
                if (t.id === 'adminRoleFilter' || t.id === 'adminStatusFilter' || t.id === 'adminDateFilter') {
                    applyAdminFilters();
                }
            });

            applyAdminFilters();

            @if($errors->has('email'))
            openAddUserModal();
            @endif
        })();

        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
        }
        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }

        function openEditRoleModal(userId, name, currentRole) {
            document.getElementById('editRoleUserName').textContent = 'User: ' + name;
            document.getElementById('editRoleForm').action = '{{ url("/superadmin/admins") }}/' + userId + '/role';
            document.getElementById('editRoleSelect').value = currentRole;
            document.getElementById('editRoleModal').style.display = 'flex';
        }
        function closeEditRoleModal() {
            document.getElementById('editRoleModal').style.display = 'none';
        }

        function openReauthModal(form) {
            document.getElementById('reauthForm').action = form.action;
            document.getElementById('reauthLabel').textContent = form.dataset.label || 'Confirm your identity with OTP.';
            document.getElementById('reauthModal').style.display = 'flex';
        }
        function closeReauthModal() {
            document.getElementById('reauthModal').style.display = 'none';
        }
    </script>
@endpush
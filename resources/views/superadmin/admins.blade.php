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
                    <div class="admin-management-controls">
                        <div class="admin-search-row">
                        <label class="admin-search-wrap" for="adminSearchInput">
                            <span class="material-icons">search</span>
                            <input id="adminSearchInput" type="text" placeholder="Search">
                        </label>
                        </div>
                        <div class="admin-management-filters">
                        <div class="admin-filter-dropdown" data-filter-dropdown>
                            <button type="button" class="admin-filter-trigger" id="adminRoleFilterTrigger"
                                aria-haspopup="listbox" aria-expanded="false" aria-controls="adminRoleFilterMenu"
                                aria-label="Filter by role">
                                <span class="admin-filter-trigger-text">Role</span>
                                <span class="material-icons admin-filter-chevron" aria-hidden="true">expand_more</span>
                            </button>
                            <ul id="adminRoleFilterMenu" class="admin-filter-menu" role="listbox" aria-label="Filter by role"
                                hidden>
                                <li class="admin-filter-option" role="option" data-value="all" aria-selected="true" tabindex="0">
                                    Role</li>
                                <li class="admin-filter-option" role="option" data-value="superadmin" aria-selected="false"
                                    tabindex="-1">Superadmin</li>
                                <li class="admin-filter-option" role="option" data-value="approver" aria-selected="false"
                                    tabindex="-1">Approver</li>
                                <li class="admin-filter-option" role="option" data-value="user" aria-selected="false"
                                    tabindex="-1">User</li>
                            </ul>
                            <select id="adminRoleFilter" class="admin-filter-native-select" aria-label="Filter by role"
                                tabindex="-1">
                                <option value="all">Role</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="approver">Approver</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="admin-filter-dropdown" data-filter-dropdown>
                            <button type="button" class="admin-filter-trigger" id="adminStatusFilterTrigger"
                                aria-haspopup="listbox" aria-expanded="false" aria-controls="adminStatusFilterMenu"
                                aria-label="Filter by status">
                                <span class="admin-filter-trigger-text">Status</span>
                                <span class="material-icons admin-filter-chevron" aria-hidden="true">expand_more</span>
                            </button>
                            <ul id="adminStatusFilterMenu" class="admin-filter-menu" role="listbox"
                                aria-label="Filter by status" hidden>
                                <li class="admin-filter-option" role="option" data-value="all" aria-selected="true" tabindex="0">
                                    Status</li>
                                <li class="admin-filter-option" role="option" data-value="active" aria-selected="false"
                                    tabindex="-1">Active</li>
                                <li class="admin-filter-option" role="option" data-value="deactivated" aria-selected="false"
                                    tabindex="-1">Deactivated</li>
                            </ul>
                            <select id="adminStatusFilter" class="admin-filter-native-select" aria-label="Filter by status"
                                tabindex="-1">
                                <option value="all">Status</option>
                                <option value="active">Active</option>
                                <option value="deactivated">Deactivated</option>
                            </select>
                        </div>
                        <div class="admin-filter-dropdown" data-filter-dropdown>
                            <button type="button" class="admin-filter-trigger" id="adminDateFilterTrigger"
                                aria-haspopup="listbox" aria-expanded="false" aria-controls="adminDateFilterMenu"
                                aria-label="Filter by date">
                                <span class="admin-filter-trigger-text">Date</span>
                                <span class="material-icons admin-filter-chevron" aria-hidden="true">expand_more</span>
                            </button>
                            <ul id="adminDateFilterMenu" class="admin-filter-menu" role="listbox" aria-label="Filter by date"
                                hidden>
                                <li class="admin-filter-option" role="option" data-value="all" aria-selected="true" tabindex="0">
                                    Date</li>
                                <li class="admin-filter-option" role="option" data-value="last30" aria-selected="false"
                                    tabindex="-1">Last 30 days</li>
                                <li class="admin-filter-option" role="option" data-value="thisYear" aria-selected="false"
                                    tabindex="-1">This year</li>
                                <li class="admin-filter-option" role="option" data-value="older" aria-selected="false"
                                    tabindex="-1">Older</li>
                            </ul>
                            <select id="adminDateFilter" class="admin-filter-native-select" aria-label="Filter by date"
                                tabindex="-1">
                                <option value="all">Date</option>
                                <option value="last30">Last 30 days</option>
                                <option value="thisYear">This year</option>
                                <option value="older">Older</option>
                            </select>
                        </div>
                        </div>
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
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px;
            position: relative;
            z-index: 40;
            overflow: visible;
        }

        .admin-management-controls {
            display: grid;
            gap: 10px;
            flex: 0 1 560px;
            width: 100%;
            max-width: 560px;
            min-width: 0;
            position: relative;
            z-index: 45;
        }

        .admin-search-row {
            display: block;
            width: 100%;
        }

        .admin-management-filters {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            width: 100%;
            align-items: stretch;
        }

        .admin-search-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            min-width: 0;
            min-height: 38px;
            background: #fff;
            border: 1px solid #bcc7d6;
            border-radius: 8px;
            padding: 6px 12px;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, background-color 0.16s ease;
        }

        .admin-search-wrap:hover {
            border-color: #95a3b8;
            background: #fcfdff;
        }

        .admin-search-wrap:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.16);
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

        .admin-filter-dropdown {
            position: relative;
            min-width: 0;
            z-index: 1;
        }

        .admin-filter-dropdown.is-open {
            z-index: 80;
        }

        .admin-filter-trigger {
            width: 100%;
            min-height: 44px;
            border: 1px solid #d0d5dd;
            border-radius: 6px;
            background: #ffffff;
            padding: 2px 3px;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.08);
            cursor: pointer;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, background-color 0.16s ease;
        }

        .admin-filter-trigger:hover {
            border-color: #b8c2d3;
            background: #fcfdff;
        }

        .admin-filter-trigger:focus-visible {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.08), 0 0 0 3px rgba(59, 130, 246, 0.22);
        }

        .admin-filter-trigger-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-filter-chevron {
            font-size: 18px;
            color: #64748b;
            transition: transform 0.16s ease;
        }

        .admin-filter-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: auto;
            margin: 0;
            padding: 6px;
            list-style: none;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.14);
            z-index: 1200;
            min-width: 160px;
            max-height: 280px;
            overflow-y: auto;
            white-space: nowrap;
        }

        .admin-filter-option {
            padding: 10px 12px;
            border-radius: 6px;
            color: #0f172a;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.25;
            cursor: pointer;
            transition: background-color 0.14s ease, color 0.14s ease;
        }

        .admin-filter-option:hover,
        .admin-filter-option:focus-visible {
            background: #f8fafc;
            outline: none;
        }

        .admin-filter-option[aria-selected="true"] {
            background: #eaf2ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        .admin-filter-dropdown.is-open .admin-filter-trigger {
            border-color: #3b82f6;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.08), 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .admin-filter-dropdown.is-open .admin-filter-chevron {
            transform: rotate(180deg);
        }

        .admin-filter-dropdown.is-disabled .admin-filter-trigger {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
            box-shadow: none;
            cursor: not-allowed;
        }

        .admin-filter-dropdown.is-disabled .admin-filter-chevron {
            color: #94a3b8;
        }

        .admin-filter-native-select {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: 0;
            padding: 0;
            border: 0;
            opacity: 0;
            pointer-events: none;
        }

        .admin-add-user-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            align-self: flex-start;
            margin-left: auto;
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
            position: relative;
            z-index: 1;
            overflow-x: auto;
        }

        .admin-management-card {
            overflow: visible !important;
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
            .admin-management-controls {
                max-width: none;
                flex: 1 1 100%;
            }

            .admin-search-wrap {
                min-width: 100%;
            }

            .admin-management-filters {
                grid-template-columns: 1fr;
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

            function getDropdownParts(dropdown) {
                if (!dropdown) return null;
                return {
                    trigger: dropdown.querySelector('.admin-filter-trigger'),
                    menu: dropdown.querySelector('.admin-filter-menu'),
                    options: Array.prototype.slice.call(dropdown.querySelectorAll('.admin-filter-option')),
                    select: dropdown.querySelector('.admin-filter-native-select')
                };
            }

            function syncFilterDropdown(dropdown) {
                var parts = getDropdownParts(dropdown);
                if (!parts || !parts.trigger || !parts.select) return;

                var selectedValue = parts.select.value || 'all';
                var selectedOption = parts.options.find(function (option) {
                    return option.getAttribute('data-value') === selectedValue;
                }) || parts.options[0];

                parts.options.forEach(function (option) {
                    var isSelected = option === selectedOption;
                    option.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                    option.setAttribute('tabindex', isSelected ? '0' : '-1');
                });

                var textNode = parts.trigger.querySelector('.admin-filter-trigger-text');
                if (textNode && selectedOption) {
                    textNode.textContent = selectedOption.textContent.trim();
                }

                var isDisabled = !!parts.select.disabled;
                dropdown.classList.toggle('is-disabled', isDisabled);
                parts.trigger.disabled = isDisabled;
            }

            function closeFilterDropdown(dropdown, focusTrigger) {
                var parts = getDropdownParts(dropdown);
                if (!parts || !parts.trigger || !parts.menu) return;
                dropdown.classList.remove('is-open');
                parts.trigger.setAttribute('aria-expanded', 'false');
                parts.menu.hidden = true;
                if (focusTrigger) {
                    parts.trigger.focus();
                }
            }

            function closeAllFilterDropdowns(exceptDropdown) {
                document.querySelectorAll('[data-filter-dropdown]').forEach(function (dropdown) {
                    if (!exceptDropdown || dropdown !== exceptDropdown) {
                        closeFilterDropdown(dropdown, false);
                    }
                });
            }

            function openFilterDropdown(dropdown) {
                var parts = getDropdownParts(dropdown);
                if (!parts || !parts.trigger || !parts.menu || !parts.options.length || dropdown.classList.contains('is-disabled')) {
                    return;
                }
                closeAllFilterDropdowns(dropdown);
                dropdown.classList.add('is-open');
                parts.trigger.setAttribute('aria-expanded', 'true');
                parts.menu.hidden = false;
                var selectedOption = parts.options.find(function (option) {
                    return option.getAttribute('aria-selected') === 'true';
                }) || parts.options[0];
                if (selectedOption) selectedOption.focus();
            }

            function selectFilterOption(optionEl) {
                var dropdown = optionEl.closest('[data-filter-dropdown]');
                var parts = getDropdownParts(dropdown);
                if (!parts || !parts.select) return;
                var nextValue = optionEl.getAttribute('data-value') || 'all';
                parts.select.value = nextValue;
                syncFilterDropdown(dropdown);
                parts.select.dispatchEvent(new Event('change', { bubbles: true }));
                closeFilterDropdown(dropdown, true);
            }

            function moveDropdownOptionFocus(optionEl, direction) {
                var dropdown = optionEl.closest('[data-filter-dropdown]');
                var parts = getDropdownParts(dropdown);
                if (!parts || !parts.options.length) return;
                var currentIndex = parts.options.indexOf(optionEl);
                if (currentIndex < 0) return;
                var nextIndex = currentIndex + direction;
                if (nextIndex < 0) nextIndex = 0;
                if (nextIndex > parts.options.length - 1) nextIndex = parts.options.length - 1;
                parts.options[nextIndex].focus();
            }

            function initializeFilterDropdowns() {
                document.querySelectorAll('[data-filter-dropdown]').forEach(function (dropdown) {
                    syncFilterDropdown(dropdown);
                    closeFilterDropdown(dropdown, false);
                });
            }

            document.addEventListener('click', function (event) {
                var target = event.target;
                if (!target || typeof target.closest !== 'function') return;

                var trigger = target.closest('.admin-filter-trigger');
                if (trigger) {
                    var triggerDropdown = trigger.closest('[data-filter-dropdown]');
                    if (triggerDropdown && triggerDropdown.classList.contains('is-open')) {
                        closeFilterDropdown(triggerDropdown, false);
                    } else {
                        openFilterDropdown(triggerDropdown);
                    }
                    return;
                }

                var option = target.closest('.admin-filter-option');
                if (option) {
                    selectFilterOption(option);
                    return;
                }

                if (!target.closest('[data-filter-dropdown]')) {
                    closeAllFilterDropdowns(null);
                }

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

            document.addEventListener('keydown', function (event) {
                var target = event.target;
                if (!target || typeof target.closest !== 'function') return;

                var trigger = target.closest('.admin-filter-trigger');
                if (trigger) {
                    var triggerDropdown = trigger.closest('[data-filter-dropdown]');
                    if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openFilterDropdown(triggerDropdown);
                    } else if (event.key === 'Escape') {
                        closeFilterDropdown(triggerDropdown, true);
                    }
                    return;
                }

                var option = target.closest('.admin-filter-option');
                if (!option) return;

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    moveDropdownOptionFocus(option, 1);
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    moveDropdownOptionFocus(option, -1);
                } else if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectFilterOption(option);
                } else if (event.key === 'Escape') {
                    event.preventDefault();
                    closeFilterDropdown(option.closest('[data-filter-dropdown]'), true);
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
                    var dropdown = t.closest('[data-filter-dropdown]');
                    if (dropdown) syncFilterDropdown(dropdown);
                    applyAdminFilters();
                }
            });

            initializeFilterDropdowns();
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
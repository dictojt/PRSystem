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
            <div class="table-card" style="margin-bottom: 24px;">
                <div class="card-title-bar">
                    <span>All Users</span>
                    <button type="button" class="btn-primary" onclick="openAddUserModal()">
                        <span class="material-icons" style="font-size: 10px; vertical-align: middle;">person_add</span> Add User
                    </button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
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
                                <td>
                                    @if(isset($admin->is_active) && $admin->is_active === false)
                                        <span class="badge badge-rejected">Deactivated</span>
                                    @else
                                        <span class="badge badge-approved">Active</span>
                                    @endif
                                </td>
                                <td>{{ $admin->created_at?->format('M d, Y') }}</td>
                                <td>
                                    @if(auth()->id() !== $admin->id)
                                        <div class="action-buttons">
                                            <button type="button" class="btn-sm btn-role"
                                                onclick="openEditRoleModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ $admin->role }}')"
                                                title="Edit role">Role</button>
                                            @if(isset($admin->is_active) && $admin->is_active === false)
                                                <form method="post" action="{{ route('superadmin.admins.reactivate', $admin) }}"
                                                    style="display:inline;" class="form-require-reauth" data-action="reactivate"
                                                    data-user-id="{{ $admin->id }}" data-label="Reactivate {{ $admin->name }}">
                                                    @csrf
                                                    <button type="button" class="btn-sm btn-success">Reactivate</button>
                                                </form>
                                            @else
                                                <form method="post" action="{{ route('superadmin.admins.deactivate', $admin) }}"
                                                    style="display:inline;" class="form-require-reauth" data-action="deactivate"
                                                    data-user-id="{{ $admin->id }}" data-label="Deactivate {{ $admin->name }}">
                                                    @csrf
                                                    <button type="button" class="btn-sm btn-danger">Deactivate</button>
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
                                <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

        .btn-role {
            background: #eff6ff;
            color: var(--primary);
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Send OTP button with 5-minute resend countdown (shared)
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
            document.querySelectorAll('.send-otp-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var btnEl = this;
                    if (btnEl.disabled) return;
                    btnEl.disabled = true;
                    var token = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
                    fetch('{{ route("superadmin.send-otp") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token || '', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({})
                    }).then(function (r) {
                        return r.json().then(function (data) { return { ok: r.ok, data: data }; });
                    }).then(function (result) {
                        var block = btnEl.closest('.reauth-block');
                        if (result.ok) {
                            startOtpCooldown(btnEl);
                        } else {
                            var err = block.querySelector('.otp-error');
                            if (err) {
                                err.textContent = result.data.message || 'Failed to send OTP. Try again.';
                                err.style.display = 'block';
                            }
                            var sent = block.querySelector('.otp-sent');
                            if (sent) sent.style.display = 'none';
                            btnEl.disabled = false;
                            if (btnEl.textContent.indexOf('Resend in') !== 0) btnEl.textContent = 'Send OTP to my email';
                        }
                    }).catch(function () {
                        var block = btnEl.closest('.reauth-block');
                        var err = block && block.querySelector('.otp-error');
                        if (err) {
                            err.textContent = 'Failed to send OTP. Check your connection and try again.';
                            err.style.display = 'block';
                        }
                        btnEl.disabled = false;
                        if (btnEl.textContent.indexOf('Resend in') !== 0) btnEl.textContent = 'Send OTP to my email';
                    });
                });
            });

            // Forms that require re-auth (Deactivate, Reactivate)
            document.querySelectorAll('.form-require-reauth').forEach(function (form) {
                form.querySelector('button[type="button"]').addEventListener('click', function () {
                    openReauthModal(form);
                });
            });

            // Open Add User modal when returning with validation errors from Add User form
            @if($errors->has('email'))
            openAddUserModal();
            @endif
        });

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
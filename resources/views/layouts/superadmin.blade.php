@if(request()->header('X-Partial-Content') === '1')
@php
    $__partialTitle = trim((string) $__env->yieldContent('title', ''));
    if ($__partialTitle === '') { $__partialTitle = 'Super Admin'; }
    $__partialTitle = $__partialTitle . ' - Product Request System | DICT';
@endphp
<div data-page-title="{{ e($__partialTitle) }}">
    @stack('styles')
    @if(session('message'))
        <div class="alert-success">
            <span>{{ session('message') }}</span>
            <button type="button" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif
    @if(!auth()->check())
        <div class="guest-notice">
            <span class="material-icons">info</span>
            <div>
                <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
            </div>
        </div>
    @endif
    @yield('content')
    <footer class="footer">
        <p class="copyright">© {{ date('Y') }} Product Request System - DICT</p>
    </footer>
    @stack('scripts')
</div>
@else
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/super-admin.css', 'resources/js/super-admin.js'])
    @stack('styles')
</head>

<body class="panel-superadmin">

    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><span class="material-icons">shield</span><span class="sidebar-label">PRS Admin</span></h2>
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Collapse sidebar">
                    <span class="material-icons">chevron_left</span>
                </button>
            </div>
            @php $u = auth()->user(); @endphp
            <div class="profile-card">
                <div class="profile-avatar">{{ $u ? strtoupper(substr($u->name ?? 'A', 0, 1)) : 'G' }}</div>
                <div class="profile-info">
                    <div class="profile-name">{{ $u ? ($u->name ?? 'Admin') : 'Guest' }}</div>
                    <div class="profile-email">{{ $u ? ($u->email ?? '') : '' }}</div>
                </div>
            </div>
            <div class="menu-top">
                <a href="{{ auth()->check() ? route('superadmin.dashboard') : route('superadmin.guest') }}"
                    class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" title="Overview">
                    <span class="material-icons">dashboard</span><span class="sidebar-label">Overview</span>
                </a>
                <a href="{{ route('superadmin.admins') }}"
                    class="{{ request()->routeIs('superadmin.admins') ? 'active' : '' }}" title="Admin Management">
                    <span class="material-icons">groups</span><span class="sidebar-label">Admin Management</span>
                </a>
                <a href="{{ route('superadmin.approvers') }}"
                    class="{{ request()->routeIs('superadmin.approvers') ? 'active' : '' }}" title="Approvers">
                    <span class="material-icons">verified_user</span><span class="sidebar-label">Approvers</span>
                </a>
                <a href="{{ route('superadmin.requests') }}"
                    class="{{ request()->routeIs('superadmin.requests') ? 'active' : '' }}" title="All Requests">
                    <span class="material-icons">inventory_2</span><span class="sidebar-label">All Requests</span>
                </a>
                <a href="{{ route('superadmin.reports') }}"
                    class="{{ request()->routeIs('superadmin.reports') ? 'active' : '' }}" title="System Reports">
                    <span class="material-icons">analytics</span><span class="sidebar-label">System Reports</span>
                </a>
                <a href="{{ route('superadmin.settings') }}"
                    class="{{ request()->routeIs('superadmin.settings') ? 'active' : '' }}" title="System Settings">
                    <span class="material-icons">settings</span><span class="sidebar-label">System Settings</span>
                </a>
            </div>
            <div class="logout">
                @auth
                    <a href="{{ route('logout') }}" class="logout-link-get"
                        onclick="return confirm('Are you sure you want to logout?')"
                        style="text-decoration: none; width: 100%;" title="Logout">
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
            @if(session('message'))
                <div class="alert-success">
                    <span>{{ session('message') }}</span>
                    <button type="button" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif
            @if(!auth()->check())
                <div class="guest-notice">
                    <span class="material-icons">info</span>
                    <div>
                        <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a
                            href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your
                        account.
                    </div>
                </div>
            @endif
            @yield('content')
            <footer class="footer">
                <p class="copyright">© {{ date('Y') }} Product Request System - DICT</p>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
@endif
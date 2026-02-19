@if(request()->header('X-Partial-Content') === '1')
@php
    $__partialTitle = trim((string) $__env->yieldContent('title', ''));
    if ($__partialTitle === '') { $__partialTitle = 'User Panel'; }
    $__partialTitle = $__partialTitle . ' - Product Request System | DICT';
@endphp
<div data-page-title="{{ e($__partialTitle) }}">
    @if(!auth()->check())
    <div class="guest-notice">
        <span class="material-icons">info</span>
        <div>
            <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
        </div>
    </div>
    @endif
    @yield('main')
    @stack('scripts')
</div>
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>@yield('title', 'User Panel') - Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/user-panel.css', 'resources/js/user-panel.js'])
    @stack('styles')
</head>
<body>
<div class="container">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><span class="material-icons">person</span><span class="sidebar-label">PRS User</span></h2>
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Collapse sidebar">
                <span class="material-icons">chevron_left</span>
            </button>
        </div>
        @php $u = auth()->user(); @endphp
        <div class="profile-card">
            <div class="profile-avatar">{{ $u ? strtoupper(substr($u->name ?? 'U', 0, 1)) : 'G' }}</div>
            <div class="profile-info">
                <div class="profile-name">{{ $u ? ($u->name ?? 'User') : 'Guest' }}</div>
                <div class="profile-email">{{ $u ? ($u->email ?? '') : '' }}</div>
            </div>
        </div>
        <div class="menu-top">
            <a href="{{ auth()->check() ? route('user.dashboard') : route('user.guest') }}" class="{{ request()->routeIs('user.dashboard') || request()->routeIs('user.guest') ? 'active' : '' }}" title="Overview">
                <span class="material-icons">dashboard</span><span class="sidebar-label">Overview</span>
            </a>
            <a href="{{ route('user.requests.create') }}" class="{{ request()->routeIs('user.requests.create') ? 'active' : '' }}" title="Create Request">
                <span class="material-icons">add_circle</span><span class="sidebar-label">Create Request</span>
            </a>
            <a href="{{ route('user.requests.view') }}" class="{{ request()->routeIs('user.requests.view') ? 'active' : '' }}" title="View Request">
                <span class="material-icons">list_alt</span><span class="sidebar-label">View Request</span>
            </a>
            <a href="{{ route('user.reports') }}" class="{{ request()->routeIs('user.reports') ? 'active' : '' }}" title="View Reports">
                <span class="material-icons">analytics</span><span class="sidebar-label">View Reports</span>
            </a>
            <a href="{{ route('user.support') }}" class="{{ request()->routeIs('user.support') ? 'active' : '' }}" title="Support">
                <span class="material-icons">support</span><span class="sidebar-label">Support</span>
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
        @if(!auth()->check())
        <div class="guest-notice">
            <span class="material-icons">info</span>
            <div>
                <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
            </div>
        </div>
        @endif
        @yield('main')
    </div>
</div>
@stack('scripts')
</body>
</html>
@endif

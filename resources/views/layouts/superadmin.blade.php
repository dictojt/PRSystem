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
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>@yield('title', 'Super Admin') - Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/super-admin.css', 'resources/js/super-admin.js'])
    @stack('styles')
</head>

@php
    $autoCollapseSidebar = request()->routeIs('superadmin.dashboard') || request()->routeIs('superadmin.guest')
        || request()->routeIs('superadmin.admins') || request()->routeIs('superadmin.approvers')
        || request()->routeIs('superadmin.requests') || request()->routeIs('superadmin.settings');
@endphp
<body class="panel-superadmin {{ $autoCollapseSidebar ? 'sidebar-collapsed' : '' }}" data-auto-collapse-sidebar="{{ $autoCollapseSidebar ? '1' : '0' }}">
<script>document.body.classList.add('theme-' + (localStorage.getItem('prs-theme') || 'light'));</script>

    <div class="container">
        <div class="sidebar {{ $autoCollapseSidebar ? 'collapsed' : '' }}" id="sidebar" @if($autoCollapseSidebar) aria-expanded="false" @endif>
            <div class="sidebar-header">
                <h2><span class="material-icons">shield</span><span class="sidebar-label">PRS Admin</span></h2>
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="{{ $autoCollapseSidebar ? 'Expand sidebar' : 'Collapse sidebar' }}">
                    <span class="material-icons">{{ $autoCollapseSidebar ? 'chevron_right' : 'chevron_left' }}</span>
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
                    class="{{ request()->routeIs('superadmin.dashboard') || request()->routeIs('superadmin.guest') ? 'active' : '' }}" title="Overview">
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
                <div class="menu-item has-submenu" data-submenu="all-requests">
                    <a href="{{ route('superadmin.requests') }}"
                        class="{{ request()->routeIs('superadmin.requests') ? 'active' : '' }}" title="All Requests" aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">inventory_2</span><span class="sidebar-label">All Requests</span>
                        <span class="material-icons sidebar-submenu-chevron">expand_more</span>
                    </a>
                    <div class="sidebar-submenu" id="sidebar-submenu-all-requests" aria-hidden="true">
                        @php $reqStatus = request()->routeIs('superadmin.requests') ? request('status', 'all') : ''; @endphp
                        <a href="{{ route('superadmin.requests', ['status' => 'all']) }}"
                            class="{{ $reqStatus === 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ route('superadmin.requests', ['status' => 'pending']) }}"
                            class="{{ $reqStatus === 'pending' ? 'active' : '' }}">Pending</a>
                        <a href="{{ route('superadmin.requests', ['status' => 'approved']) }}"
                            class="{{ $reqStatus === 'approved' ? 'active' : '' }}">Approved</a>
                        <a href="{{ route('superadmin.requests', ['status' => 'rejected']) }}"
                            class="{{ $reqStatus === 'rejected' ? 'active' : '' }}">Rejected</a>
                    </div>
                </div>
                <a href="{{ route('superadmin.settings') }}"
                    class="{{ request()->routeIs('superadmin.settings') ? 'active' : '' }}" title="System Settings">
                    <span class="material-icons">settings</span><span class="sidebar-label">System Settings</span>
                </a>
            </div>
            <div class="logout">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="logout-form" style="width: 100%; margin: 0;">
                        @csrf
                        <button type="submit" class="logout-link-get" onclick="return confirm('Are you sure you want to logout?')" title="Logout">
                            <span class="material-icons">logout</span><span class="sidebar-label">Logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('home') }}" class="logout-link" title="Sign in">
                        <span class="material-icons">login</span><span class="sidebar-label">Sign in</span>
                    </a>
                @endauth
            </div>
        </div>
        <script>
            (function(){
                var s=document.getElementById('sidebar');
                var b=document.body;
                var t=document.getElementById('sidebarToggle');
                var i=t&&t.querySelector('.material-icons');
                if(b.dataset.autoCollapseSidebar==='1'){
                    if(s){s.classList.add('collapsed');}
                    b.classList.add('sidebar-collapsed');
                    if(i){i.textContent='chevron_right';}
                }else{
                    if(s){s.classList.remove('collapsed');}
                    b.classList.remove('sidebar-collapsed');
                    if(i){i.textContent='chevron_left';}
                }
            })();
        </script>
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>
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
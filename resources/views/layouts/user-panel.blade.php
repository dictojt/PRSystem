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
    @include('partials.footer')
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
@php
    $autoCollapseSidebar = request()->routeIs('user.dashboard') || request()->routeIs('user.guest')
        || request()->routeIs('user.requests.create') || request()->routeIs('user.requests.view')
        || request()->routeIs('user.reports') || request()->routeIs('user.support')
        || request()->routeIs('user.settings');
@endphp
<body class="panel-user {{ $autoCollapseSidebar ? 'sidebar-collapsed' : '' }}" data-auto-collapse-sidebar="{{ $autoCollapseSidebar ? '1' : '0' }}">
<script>document.body.classList.add('theme-' + (localStorage.getItem('prs-theme') || 'light'));</script>
<div class="container">
    @include('partials.user-sidebar', ['collapsed' => $autoCollapseSidebar])
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
        @if(!auth()->check())
        <div class="guest-notice">
            <span class="material-icons">info</span>
            <div>
                <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
            </div>
        </div>
        @endif
        @yield('main')
        @include('partials.footer')
    </div>
</div>
@stack('scripts')
</body>
</html>
@endif

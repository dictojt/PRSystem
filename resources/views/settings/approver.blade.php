@if(request()->header('X-Partial-Content') === '1')
<div data-page-title="Settings - Approver Panel | Product Request System | DICT">
    <div class="header-section">
        <h1>Settings</h1>
        <p>Manage your preferences.</p>
    </div>

    <div class="card settings-card-appearance" style="max-width: 560px;">
        <div class="card-title-bar">
            <h2>Appearance</h2>
        </div>
        <div class="settings-appearance">
            <div class="form-group">
                <label for="theme" class="form-label">Theme</label>
                <select id="theme" class="form-control" data-theme-toggle aria-label="Theme">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                </select>
            </div>
            <p class="settings-theme-note">Your choice is saved in this browser only.</p>
        </div>
    </div>
    @include('partials.footer')
</div>
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>Settings - Approver Panel | Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/approver.css', 'resources/js/approver.js'])
</head>
@php
    $autoCollapseSidebar = true; /* Settings is a content route */
@endphp
<body class="panel-approver {{ $autoCollapseSidebar ? 'sidebar-collapsed' : '' }}" data-auto-collapse-sidebar="{{ $autoCollapseSidebar ? '1' : '0' }}">
<script>document.body.classList.add('theme-' + (localStorage.getItem('prs-theme') || 'light'));</script>
<div class="container">
    <div class="sidebar {{ $autoCollapseSidebar ? 'collapsed' : '' }}" id="sidebar" @if($autoCollapseSidebar) aria-expanded="false" @endif>
        <div class="sidebar-header">
            <h2><span class="material-icons">verified_user</span><span class="sidebar-label">PRS Approver</span></h2>
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="{{ $autoCollapseSidebar ? 'Expand sidebar' : 'Collapse sidebar' }}">
                <span class="material-icons">{{ $autoCollapseSidebar ? 'chevron_right' : 'chevron_left' }}</span>
            </button>
        </div>
        @php $u = auth()->user(); $approverDashboard = route('approver.dashboard'); @endphp
        <div class="profile-card">
            <div class="profile-avatar">{{ $u ? strtoupper(substr($u->name ?? 'U', 0, 1)) : 'G' }}</div>
            <div class="profile-info">
                <div class="profile-name">{{ $u ? ($u->name ?? 'Approver') : 'Guest' }}</div>
                <div class="profile-email">{{ $u ? ($u->email ?? '') : '' }}</div>
            </div>
        </div>
        <div class="menu-top">
            <a href="{{ $approverDashboard }}" class="" title="Overview">
                <span class="material-icons">dashboard</span><span class="sidebar-label">Overview</span>
            </a>
            <a href="{{ $approverDashboard }}?tab=pending" class="" title="Pending Requests">
                <span class="material-icons">pending_actions</span><span class="sidebar-label">Pending Requests</span>
            </a>
            <a href="{{ $approverDashboard }}?tab=approved" class="" title="All request">
                <span class="material-icons">list_alt</span><span class="sidebar-label">All request</span>
            </a>
            @auth
            <a href="{{ route('approver.settings') }}" class="active" title="Settings">
                <span class="material-icons">settings</span><span class="sidebar-label">Settings</span>
            </a>
            @endauth
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
        <div class="header-section">
            <h1>Settings</h1>
            <p>Manage your preferences.</p>
        </div>

        <div class="card settings-card-appearance" style="max-width: 560px;">
            <div class="card-title-bar">
                <h2>Appearance</h2>
            </div>
            <div class="settings-appearance">
                <div class="form-group">
                    <label for="theme" class="form-label">Theme</label>
                    <select id="theme" class="form-control" data-theme-toggle aria-label="Theme">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>
                <p class="settings-theme-note">Your choice is saved in this browser only.</p>
            </div>
        </div>
        @include('partials.footer')
    </div>
</div>
</body>
</html>
@endif

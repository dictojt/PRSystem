@php $sidebarCollapsed = isset($collapsed) && $collapsed; @endphp
<div class="sidebar {{ $sidebarCollapsed ? 'collapsed' : '' }}" id="sidebar" @if($sidebarCollapsed) aria-expanded="false" @endif>
    <div class="sidebar-header">
        <h2><span class="material-icons">person</span><span class="sidebar-label">PRS User</span></h2>
        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="{{ $sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar' }}">
            <span class="material-icons">{{ $sidebarCollapsed ? 'chevron_right' : 'chevron_left' }}</span>
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
        <a href="{{ auth()->check() ? route('user.dashboard') : route('user.guest') }}" class="{{ request()->routeIs('user.dashboard') || request()->routeIs('user.guest') ? 'active' : '' }}" title="Overview" data-full-reload>
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
        @auth
        <a href="{{ route('user.settings') }}" class="{{ request()->routeIs('user.settings') ? 'active' : '' }}" title="Settings">
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

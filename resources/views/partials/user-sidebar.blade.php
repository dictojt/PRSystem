<div class="sidebar">
    <div class="menu-top">
        <h2><span class="material-icons">person</span> PRS User</h2>
        <a href="{{ auth()->check() ? route('user.dashboard') : route('user.guest') }}" class="{{ request()->routeIs('user.dashboard') || request()->routeIs('user.guest') ? 'active' : '' }}">
            <span class="material-icons">dashboard</span> Dashboard
        </a>
        <a href="{{ route('user.requests.create') }}" class="{{ request()->routeIs('user.requests.*') ? 'active' : '' }}"><span class="material-icons">add_circle</span> Create Request</a>
        <a href="{{ route('user.track') }}" class="{{ request()->routeIs('user.track') ? 'active' : '' }}"><span class="material-icons">search</span> Track Item</a>
        <a href="{{ route('user.reports') }}" class="{{ request()->routeIs('user.reports') ? 'active' : '' }}"><span class="material-icons">analytics</span> View Reports</a>
        <a href="{{ route('user.support') }}" class="{{ request()->routeIs('user.support') ? 'active' : '' }}"><span class="material-icons">support</span> Support</a>
    </div>
    <div class="logout">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="width: 100%;">
            @csrf
            <button type="button" onclick="logout()" style="width: 100%;">
                <span class="material-icons">logout</span> Logout
            </button>
        </form>
    </div>
</div>

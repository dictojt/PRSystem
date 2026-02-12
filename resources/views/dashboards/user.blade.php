<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel - Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --sidebar-bg: #f9fafb;
            --sidebar-border: #e5e7eb;
            --sidebar-text: #6b7280;
            --sidebar-text-dark: #111827;
            --bg-main: #f3f4f6;
            --card-white: #ffffff;
            --danger: #ef4444;
            --text-dark: #1e293b;
            --text-muted: #6b7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        html, body { height: 100%; overflow: hidden; background-color: var(--bg-main); color: var(--text-dark); }
        .container { display: flex; }

        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: 280px; min-width: 280px; max-width: 280px;
            background: var(--sidebar-bg); color: var(--sidebar-text-dark);
            padding: 16px 12px; display: flex; flex-direction: column;
            overflow-x: hidden; overflow-y: auto;
            transition: width 0.25s ease, min-width 0.25s ease, max-width 0.25s ease;
            z-index: 1000; border-right: 1px solid var(--sidebar-border);
        }
        .sidebar.collapsed { width: 72px; min-width: 72px; max-width: 72px; padding: 12px 8px; flex-shrink: 0; }
        .sidebar.collapsed .sidebar-label,
        .sidebar.collapsed .menu-top a .sidebar-label,
        .sidebar.collapsed .logout button .sidebar-label,
        .sidebar.collapsed .logout .logout-link .sidebar-label,
        .sidebar.collapsed .profile-card .profile-name,
        .sidebar.collapsed .profile-card .profile-email { display: none !important; }
        .sidebar.collapsed .sidebar-header { flex-direction: column-reverse; gap: 6px; justify-content: flex-start; margin-bottom: 10px; }
        .sidebar.collapsed .sidebar-header h2 { justify-content: center; margin-bottom: 0; }
        .sidebar.collapsed .profile-card { justify-content: center; padding: 10px; }
        .sidebar.collapsed .profile-avatar { margin-right: 0; }
        .sidebar.collapsed .menu-top a { justify-content: center; padding: 12px; }
        .sidebar.collapsed .logout button,
        .sidebar.collapsed .logout .logout-link { justify-content: center; padding: 12px; }
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 4px 12px;
            margin-bottom: 12px;
            min-height: 48px;
            flex-shrink: 0;
            gap: 12px;
        }
        .sidebar-header h2 { flex: 1; min-width: 0; font-size: 1rem; font-weight: 700; color: var(--sidebar-text-dark); margin: 0; padding: 0; display: flex; align-items: center; gap: 8px; }
        .sidebar-header h2 .material-icons { color: var(--primary); font-size: 22px; flex-shrink: 0; }
        .sidebar-toggle {
            width: 32px; height: 32px; min-width: 32px; min-height: 32px;
            border-radius: 50%; background: var(--primary); color: white;
            border: none; cursor: pointer; display: flex !important; align-items: center; justify-content: center;
            flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,.1); transition: transform 0.2s, background 0.2s;
            visibility: visible; opacity: 1;
        }
        .sidebar-toggle:hover { transform: scale(1.05); background: var(--primary-hover); }
        .sidebar-toggle .material-icons { font-size: 18px; }
        .sidebar.collapsed .sidebar-toggle .material-icons { transform: rotate(180deg); }

        .profile-card {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            margin-bottom: 16px;
            background: var(--card-white);
            border-radius: 12px;
            border: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .profile-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .profile-info { min-width: 0; }
        .profile-name { font-size: 14px; font-weight: 600; color: var(--sidebar-text-dark); margin-bottom: 2px; }
        .profile-email { font-size: 12px; color: var(--sidebar-text); }

        .menu-top { flex: 1; display: flex; flex-direction: column; gap: 4px; }
        .menu-top a {
            background: transparent;
            color: var(--sidebar-text);
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            width: 100%;
        }
        .menu-top a:hover { background: #e5e7eb; color: var(--sidebar-text-dark); }
        .menu-top a.active { background: #eff6ff; color: var(--primary); }
        .menu-top a.active .material-icons { color: var(--primary); }
        .menu-top a .material-icons { flex-shrink: 0; font-size: 20px; color: var(--sidebar-text); }
        .logout { padding-top: 16px; border-top: 1px solid var(--sidebar-border); }
        .logout button, .logout .logout-link-get {
            width: 100%;
            background: transparent;
            color: var(--danger);
            border: none;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-start;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
        }
        .logout button:hover, .logout .logout-link-get:hover { background: #fef2f2; color: var(--danger); }
        .logout button .material-icons, .logout .logout-link-get .material-icons { flex-shrink: 0; font-size: 20px; }
        .logout-link {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-start;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
            text-decoration: none;
        }
        .logout-link:hover { background: var(--primary-hover); color: white; }
        .logout-link .material-icons { flex-shrink: 0; font-size: 20px; }

        .main { position: relative; z-index: 1; margin-left: 280px; width: calc(100% - 280px); height: 100vh; padding: 24px 32px 32px; overflow-y: auto; transition: margin-left 0.25s ease, width 0.25s ease; background: var(--bg-main); }
        body.sidebar-collapsed .main { margin-left: 72px; width: calc(100% - 72px); }
        .breadcrumb { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }
        .breadcrumb a { color: var(--primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .guest-notice { display: flex; align-items: flex-start; gap: 12px; padding: 14px 18px; margin-bottom: 20px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; font-size: 14px; color: #1e40af; }
        .guest-notice .material-icons { flex-shrink: 0; font-size: 22px; margin-top: 1px; }
        .guest-notice a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .guest-notice a:hover { text-decoration: underline; }
        .header-section { margin-bottom: 24px; }
        .header-section h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; color: var(--text-dark); }
        .header-section p { font-size: 14px; color: var(--text-muted); }

        .stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        @media (max-width: 900px) { .stat-cards { grid-template-columns: repeat(2, 1fr); } }
        .stat-card { background: var(--card-white); padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.05); display: flex; align-items: flex-start; gap: 12px; }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-card .stat-label { font-size: 13px; color: var(--text-muted); margin-bottom: 4px; }
        .stat-card .stat-value { font-size: 20px; font-weight: 700; color: var(--text-dark); }

        .home-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .card { background: var(--card-white); padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .card-title-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
        .card-title-bar h2 { font-size: 14px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .card-title-bar .material-icons { background: #eff6ff; color: var(--primary); padding: 8px; border-radius: 8px; font-size: 20px; }
        .card-list { list-style: none; }
        .card-list li { padding: 12px 0; font-size: 14px; color: var(--text-dark); border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
        .card-list li:last-child { border-bottom: none; }
        .card-list .meta { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .badge-info { background: #dbeafe; color: #1d4ed8; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-success { background: #d1fae5; color: #047857; }

        .quick-actions {
            background: var(--card-white);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .quick-actions h3 { font-size: 14px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; }
        .quick-actions .btn-wrap { display: flex; flex-wrap: wrap; gap: 12px; }
        .quick-actions a {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500;
            text-decoration: none; transition: 0.2s;
        }
        .quick-actions a.btn-primary { background: var(--primary); color: white; border: none; }
        .quick-actions a.btn-primary:hover { background: var(--primary-hover); color: white; }
        .quick-actions a.btn-outline { background: white; color: var(--primary); border: 1px solid var(--primary); }
        .quick-actions a.btn-outline:hover { background: #eff6ff; }
        .quick-actions a.btn-secondary { background: #f3f4f6; color: #6b7280; border: none; }
        .quick-actions a.btn-secondary:hover { background: #e5e7eb; color: var(--text-dark); }
    </style>
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
        <div class="profile-card">
            @php $u = auth()->user(); @endphp
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
        <nav class="breadcrumb">Home &gt; <a href="{{ auth()->check() ? route('user.dashboard') : route('user.guest') }}">My account</a> &gt; Overview</nav>

        @if(!auth()->check())
        <div class="guest-notice">
            <span class="material-icons">info</span>
            <div>
                <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
            </div>
        </div>
        @endif

        <div class="header-section">
            <h1>Overview</h1>
            <p>Manage active requests, pending actions, and completed items.</p>
        </div>

        @if(session('success'))
        <div class="alert-success" style="background:#d1fae5;color:#047857;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
            {{ session('success') }}
        </div>
        @endif

        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon"><span class="material-icons">inventory_2</span></div>
                <div>
                    <div class="stat-label">Total Requests</div>
                    <div class="stat-value">{{ count($activeRequests ?? []) + count($completed ?? []) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-icons">pending_actions</span></div>
                <div>
                    <div class="stat-label">Active</div>
                    <div class="stat-value">{{ count($activeRequests ?? []) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-icons">schedule</span></div>
                <div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">{{ count($pendingActions ?? []) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><span class="material-icons">check_circle</span></div>
                <div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">{{ count($completed ?? []) }}</div>
                </div>
            </div>
        </div>

        <div class="home-cards">
            <div class="card">
                <div class="card-title-bar">
                    <h2>Active Requests</h2>
                    <span class="material-icons">pending_actions</span>
                </div>
                <ul class="card-list">
                    @forelse($activeRequests as $req)
                    <li>
                        <div>
                            <div>{{ $req['item'] }}{{ isset($req['quantity']) && $req['quantity'] > 1 ? ' (×' . $req['quantity'] . ')' : '' }}</div>
                            <div class="meta">ID: {{ $req['id'] }} · {{ $req['date'] }}</div>
                        </div>
                        <span class="badge badge-info">{{ $req['status'] }}</span>
                    </li>
                    @empty
                    <li>No active requests.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-title-bar">
                    <h2>Pending Actions</h2>
                    <span class="material-icons">schedule</span>
                </div>
                <ul class="card-list">
                    @forelse($pendingActions as $act)
                    <li>
                        <div>
                            <div>{{ $act['action'] }}</div>
                            <div class="meta">Due: {{ $act['due'] }}</div>
                        </div>
                    </li>
                    @empty
                    <li>No pending actions.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-title-bar">
                    <h2>Completed</h2>
                    <span class="material-icons">check_circle</span>
                </div>
                <ul class="card-list">
                    @forelse($completed as $c)
                    <li>
                        <div>
                            <div>{{ $c['item'] }}{{ isset($c['quantity']) && $c['quantity'] > 1 ? ' (×' . $c['quantity'] . ')' : '' }}</div>
                            <div class="meta">ID: {{ $c['id'] }} · {{ $c['date'] }}</div>
                        </div>
                    </li>
                    @empty
                    <li>No completed requests.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="btn-wrap">
                <a href="{{ route('user.requests.create') }}" class="btn-primary"><span class="material-icons" style="font-size:18px;">add</span> Create Request</a>
                <a href="{{ route('user.requests.view') }}" class="btn-outline"><span class="material-icons" style="font-size:18px;">list_alt</span> View Request</a>
                <a href="{{ route('user.reports') }}" class="btn-outline"><span class="material-icons" style="font-size:18px;">analytics</span> View Reports</a>
                <a href="{{ route('user.support') }}" class="btn-secondary"><span class="material-icons" style="font-size:18px;">support</span> Support</a>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var KEY = 'prs_user_sidebar_collapsed';
    var sidebar = document.getElementById('sidebar');
    var toggle = document.getElementById('sidebarToggle');
    var icon = toggle ? toggle.querySelector('.material-icons') : null;
    function setCollapsed(collapsed) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (icon) icon.textContent = 'chevron_right';
            try { localStorage.setItem(KEY, '1'); } catch (e) {}
        } else {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
            if (icon) icon.textContent = 'chevron_left';
            try { localStorage.removeItem(KEY); } catch (e) {}
        }
    }
    if (toggle) toggle.addEventListener('click', function() { setCollapsed(!sidebar.classList.contains('collapsed')); });
    try { if (localStorage.getItem(KEY) === '1') setCollapsed(true); } catch (e) {}
})();
</script>
</body>
</html>

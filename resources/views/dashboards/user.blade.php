<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>User Panel - Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/js/app.js'])

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
        .quick-actions .btn-wrap { display: flex; flex-wrap: wrap; gap: 8px; }
        .quick-actions a {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
            text-decoration: none; transition: 0.2s;
        }
        .quick-actions a.btn-primary { background: var(--primary); color: white; border: none; }
        .quick-actions a.btn-primary:hover { background: var(--primary-hover); color: white; }
        .quick-actions a.btn-outline { background: white; color: var(--primary); border: 1px solid var(--primary); }
        .quick-actions a.btn-outline:hover { background: #eff6ff; }
        .quick-actions a.btn-secondary { background: #f3f4f6; color: #6b7280; border: none; }
        .quick-actions a.btn-secondary:hover { background: #e5e7eb; color: var(--text-dark); }

        :root {
            --surface-strong: #ffffff;
            --surface-muted: #f8fafc;
            --business-navy: #0f172a;
            --soft-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            --badge-approved: #10b981;
            --badge-pending: #f59e0b;
            --badge-rejected: #ef4444;
        }

        .overview-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }
        .kpi-card {
            background: var(--surface-strong);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: var(--soft-shadow);
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }
        .kpi-icon.total { background: linear-gradient(135deg, #1d4ed8, #3b82f6); }
        .kpi-icon.pending { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .kpi-icon.approved { background: linear-gradient(135deg, #059669, #10b981); }
        .kpi-icon.rejected { background: linear-gradient(135deg, #dc2626, #ef4444); }
        .kpi-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 5px;
        }
        .kpi-value {
            font-size: 24px;
            line-height: 1;
            color: var(--business-navy);
            font-weight: 700;
        }

        .overview-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }
        .overview-stack {
            display: grid;
            gap: 18px;
        }
        .overview-panel {
            background: var(--surface-strong);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: var(--soft-shadow);
            padding: 18px;
        }
        .overview-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .overview-panel-head h2 {
            font-size: 15px;
            font-weight: 700;
            color: var(--business-navy);
        }
        .panel-subtitle {
            font-size: 12px;
            color: #64748b;
        }
        .table-wrap {
            overflow-x: auto;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
        }
        .overview-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
            background: #fff;
        }
        .overview-table th,
        .overview-table td {
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
            vertical-align: middle;
        }
        .overview-table th {
            background: var(--surface-muted);
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 11px;
            font-weight: 700;
        }
        .overview-table tr:last-child td { border-bottom: none; }
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
        }
        .status-approved { background: rgba(16, 185, 129, 0.14); color: #047857; }
        .status-pending { background: rgba(245, 158, 11, 0.16); color: #b45309; }
        .status-reject { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }

        .quote-chart {
            display: grid;
            justify-items: center;
            gap: 16px;
        }
        .donut-ring {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            display: grid;
            place-items: center;
        }
        .donut-center {
            width: 122px;
            height: 122px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        }
        .donut-center strong {
            font-size: 30px;
            color: var(--business-navy);
            line-height: 1;
        }
        .donut-center span {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .quote-legend {
            width: 100%;
            display: grid;
            gap: 8px;
        }
        .legend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #334155;
            padding: 8px 10px;
            border-radius: 10px;
            background: #f8fafc;
        }
        .legend-item .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .dot-approved { background: var(--badge-approved); }
        .dot-pending { background: var(--badge-pending); }
        .dot-rejected { background: var(--badge-rejected); }

        .approved-alerts {
            display: grid;
            gap: 10px;
        }
        .alert-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0;
        }
        .alert-item-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 12px;
            color: inherit;
            transition: background 0.2s ease;
        }
        .alert-item-link:hover {
            background: #eef2ff;
        }
        .alert-item .alert-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #0f172a;
        }
        .alert-item .material-icons {
            font-size: 18px;
        }
        .alert-icon-approved { color: #059669; }
        .alert-icon-pending { color: #d97706; }
        .alert-icon-rejected { color: #dc2626; }
        .alert-meta {
            font-size: 12px;
            color: #64748b;
            white-space: nowrap;
        }
        .empty-state {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            padding: 16px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
        }

        @media (max-width: 1200px) {
            .overview-kpis { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
            .overview-layout { grid-template-columns: 1fr; }
            .overview-table { min-width: 540px; }
        }
        @media (max-width: 768px) {
            .main {
                padding: 18px 14px 24px;
            }
            .overview-kpis { grid-template-columns: 1fr; }
            .kpi-card { border-radius: 14px; }
            .overview-panel { padding: 14px; border-radius: 14px; }
            .overview-table th,
            .overview-table td { padding: 10px 10px; font-size: 12px; }
            .table-wrap { border-radius: 10px; }
            .donut-ring { width: 170px; height: 170px; }
            .donut-center { width: 112px; height: 112px; }
            .quick-actions .btn-wrap { flex-direction: row; justify-content: flex-start; }
            .quick-actions .btn-wrap a { justify-content: center; }
        }
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
        @if(!auth()->check())
        <div class="guest-notice">
            <span class="material-icons">info</span>
            <div>
                <strong>You are viewing as Guest.</strong> There is no separate sign-up form. Go to the <a href="{{ route('home') }}">Home page</a> and click &quot;Sign in with Google&quot; to use your account.
            </div>
        </div>
        @endif

        @php
            $requestAlerts = $requestAlerts ?? ($completed ?? []);
            $approvedFromAlerts = collect($requestAlerts)
                ->filter(fn ($request) => str_contains(strtolower((string) ($request['status'] ?? '')), 'approv'))
                ->count();
            $statusSummary = $statusSummary ?? [
                'approved' => $approvedFromAlerts,
                'pending' => count($activeRequests ?? []),
                'rejected' => 0,
            ];

            $approvedCount = (int) ($statusSummary['approved'] ?? 0);
            $pendingCount = (int) ($statusSummary['pending'] ?? 0);
            $rejectedCount = (int) ($statusSummary['rejected'] ?? 0);
            $totalRequests = $approvedCount + $pendingCount + $rejectedCount;

            $recentRequests = $recentRequests ?? [];
            if (count($recentRequests) === 0) {
                $recentRequests = collect(array_merge($activeRequests ?? [], $requestAlerts ?? []))
                    ->sortByDesc(fn ($request) => $request['date'] ?? '')
                    ->map(function ($request) {
                        if (! isset($request['status'])) {
                            $request['status'] = 'Approved';
                        }
                        return $request;
                    })
                    ->take(5)
                    ->values()
                    ->toArray();
            }

            $badgeClass = function ($status) {
                $normalized = strtolower((string) $status);
                if (str_contains($normalized, 'approv')) {
                    return 'status-approved';
                }
                if (str_contains($normalized, 'reject')) {
                    return 'status-reject';
                }
                return 'status-pending';
            };
            $statusIcon = function ($status) {
                $normalized = strtolower((string) $status);
                if (str_contains($normalized, 'approv')) {
                    return ['name' => 'task_alt', 'class' => 'alert-icon-approved'];
                }
                if (str_contains($normalized, 'reject')) {
                    return ['name' => 'cancel', 'class' => 'alert-icon-rejected'];
                }
                return ['name' => 'schedule', 'class' => 'alert-icon-pending'];
            };
        @endphp

        <div class="header-section">
            <h1>Procurement Dashboard Overview</h1>
            <p>Monitor request flow, quote outcomes, and approvals in one business-ready view.</p>
        </div>

        @if(session('success'))
        <div class="alert-success" style="background:#d1fae5;color:#047857;padding:12px 16px;border-radius:10px;margin-bottom:18px;">
            {{ session('success') }}
        </div>
        @endif

        <div class="overview-kpis">
            <div class="kpi-card">
                <div class="kpi-icon total"><span class="material-icons">inventory_2</span></div>
                <div>
                    <div class="kpi-label">Total Requests</div>
                    <div class="kpi-value">{{ $totalRequests }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon pending"><span class="material-icons">pending_actions</span></div>
                <div>
                    <div class="kpi-label">Pending</div>
                    <div class="kpi-value">{{ $pendingCount }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon approved"><span class="material-icons">check_circle</span></div>
                <div>
                    <div class="kpi-label">Approved</div>
                    <div class="kpi-value">{{ $approvedCount }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon rejected"><span class="material-icons">cancel</span></div>
                <div>
                    <div class="kpi-label">Rejected</div>
                    <div class="kpi-value">{{ $rejectedCount }}</div>
                </div>
            </div>
        </div>

        <div class="overview-layout">
            <div class="overview-panel">
                <div class="overview-panel-head">
                    <h2>Recent Requests</h2>
                    <span class="panel-subtitle">Latest 5 records</span>
                </div>
                <div class="table-wrap">
                    <table class="overview-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $req)
                            <tr>
                                <td>{{ $req['id'] ?? '-' }}</td>
                                <td>{{ $req['item'] ?? '-' }}</td>
                                <td>{{ $req['quantity'] ?? 1 }}</td>
                                <td>{{ $req['date'] ?? '-' }}</td>
                                <td>
                                    <span class="status-badge {{ $badgeClass($req['status'] ?? 'Pending') }}">
                                        {{ $req['status'] ?? 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">No request history available.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overview-stack">
                <div class="overview-panel">
                    <div class="overview-panel-head">
                        <h2>Request Activity Feed</h2>
                        <span class="panel-subtitle">Most recent approved, rejected, and pending updates</span>
                    </div>
                    <div class="approved-alerts">
                        @forelse(($requestAlerts ?? []) as $alert)
                        @php $icon = $statusIcon($alert['status'] ?? 'Pending'); @endphp
                        <div class="alert-item">
                            <a href="{{ $alert['url'] ?? route('user.requests.view') }}" class="alert-item-link">
                                <div class="alert-left">
                                    <span class="material-icons {{ $icon['class'] }}">{{ $icon['name'] }}</span>
                                    <span>{{ $alert['id'] }} · {{ $alert['item'] }}</span>
                                </div>
                                <span class="alert-meta">{{ $alert['status'] ?? 'Pending' }} · {{ $alert['date'] }}</span>
                            </a>
                        </div>
                        @empty
                        <div class="empty-state">No request alerts available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="overview-panel" style="margin-bottom: 20px;">
            <div class="overview-panel-head">
                <h2>Active Requests Pending</h2>
                <span class="panel-subtitle">Requests currently in progress</span>
            </div>
            <div class="table-wrap">
                <table class="overview-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeRequests as $req)
                        <tr>
                            <td>{{ $req['id'] ?? '-' }}</td>
                            <td>{{ $req['item'] ?? '-' }}</td>
                            <td>{{ $req['quantity'] ?? 1 }}</td>
                            <td>{{ $req['date'] ?? '-' }}</td>
                            <td>
                                <span class="status-badge {{ $badgeClass($req['status'] ?? 'Pending') }}">
                                    {{ $req['status'] ?? 'Pending' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">No active pending requests.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="btn-wrap">
                <a href="{{ route('user.requests.create') }}" class="btn-primary"><span class="material-icons" style="font-size:16px;">add</span> Create Request</a>
                <a href="{{ route('user.requests.view') }}" class="btn-outline"><span class="material-icons" style="font-size:16px;">list_alt</span> View Request</a>
                <a href="{{ route('user.reports') }}" class="btn-outline"><span class="material-icons" style="font-size:16px;">analytics</span> View Reports</a>
                <a href="{{ route('user.support') }}" class="btn-secondary"><span class="material-icons" style="font-size:16px;">support</span> Support</a>
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

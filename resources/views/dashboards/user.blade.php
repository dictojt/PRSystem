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
    @vite(['resources/js/app.js', 'resources/css/theme-dark-standalone.css'])
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
        html, body { height: 100%; overflow: hidden; overflow-x: hidden; background-color: var(--bg-main); color: var(--text-dark); }
        .container { display: flex; min-width: 0; overflow-x: hidden; }

        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: 280px; min-width: 280px; max-width: 280px;
            background: #1a365d;
            color: #fff;
            padding: 16px 12px; display: flex; flex-direction: column;
            overflow-x: hidden; overflow-y: auto;
            transition: width 0.25s ease, min-width 0.25s ease, max-width 0.25s ease;
            z-index: 1000; border-right: 1px solid rgba(255,255,255,0.08);
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
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
        .sidebar.collapsed .menu-top a { justify-content: center; padding: 12px; position: relative; }
        .sidebar.collapsed .logout button,
        .sidebar.collapsed .logout .logout-link,
        .sidebar.collapsed .logout .logout-link-get { justify-content: center; padding: 12px; position: relative; }
        .sidebar.collapsed .menu-top a:hover .sidebar-label,
        .sidebar.collapsed .logout .logout-link-get:hover .sidebar-label,
        .sidebar.collapsed .logout .logout-link:hover .sidebar-label {
            display: block !important;
            position: absolute; left: 100%; top: 50%; transform: translateY(-50%);
            margin-left: 12px; padding: 6px 12px;
            background: rgba(30,30,50,0.95); color: #fff; font-size: 13px; font-weight: 500;
            white-space: nowrap; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 10000; pointer-events: none;
        }
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
        .sidebar-header h2 { flex: 1; min-width: 0; font-size: 1rem; font-weight: 700; color: #fff; margin: 0; padding: 0; display: flex; align-items: center; gap: 8px; }
        .sidebar-header h2 .material-icons { color: #fff; font-size: 22px; flex-shrink: 0; }
        .sidebar-toggle {
            width: 32px; height: 32px; min-width: 32px; min-height: 32px;
            border-radius: 50%; background: rgba(255,255,255,0.1); color: #fff;
            border: none; cursor: pointer; display: flex !important; align-items: center; justify-content: center;
            flex-shrink: 0; transition: transform 0.2s, background 0.2s, box-shadow 0.2s;
            visibility: visible; opacity: 1;
        }
        .sidebar-toggle:hover { transform: scale(1.08); background: rgba(255,255,255,0.18); box-shadow: 0 2px 12px rgba(0,0,0,0.25); }
        .sidebar-toggle:active { transform: scale(0.98); }
        .sidebar-toggle .material-icons { font-size: 18px; }

        .profile-card {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            margin-bottom: 16px;
            background: rgba(255,255,255,0.06);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .profile-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.14);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .profile-info { min-width: 0; }
        .profile-name { font-size: 14px; font-weight: 600; color: #fff; margin-bottom: 2px; }
        .profile-email { font-size: 12px; color: rgba(255,255,255,0.75); }

        .menu-top { flex: 1; display: flex; flex-direction: column; gap: 4px; }
        .menu-top a {
            background: transparent;
            color: rgba(255,255,255,0.9);
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
        .menu-top a:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .menu-top a.active { background: rgba(255,255,255,0.15); color: #fff; }
        .menu-top a.active .material-icons { color: #fff; }
        .menu-top a .material-icons { flex-shrink: 0; font-size: 20px; color: rgba(255,255,255,0.9); }
        .logout { padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.12); }
        .logout button, .logout .logout-link-get {
            width: 100%;
            background: #1e40af;
            color: #fff;
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
        .logout button:hover, .logout .logout-link-get:hover { background: #1e3a8a; color: #fff; }
        .logout button .material-icons, .logout .logout-link-get .material-icons { flex-shrink: 0; font-size: 20px; color: #fff; }
        .logout-link {
            width: 100%;
            background: #1e40af;
            color: #fff;
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
        .logout-link:hover { background: #1e3a8a; color: #fff; }
        .logout-link .material-icons { flex-shrink: 0; font-size: 20px; color: #fff; }

        .main { position: relative; z-index: 1; margin-left: 280px; width: calc(100% - 280px); min-width: 0; max-width: 100%; height: 100vh; padding: 24px 32px 32px; overflow-y: auto; overflow-x: hidden; transition: margin-left 0.25s ease, width 0.25s ease; background: var(--bg-main); }
        body.sidebar-collapsed .main { margin-left: 72px; width: calc(100% - 72px); }
        .breadcrumb { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }
        .breadcrumb a { color: var(--primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .guest-notice { display: flex; align-items: flex-start; gap: 12px; padding: 14px 18px; margin-bottom: 20px; background: transparent; border: 1px solid #bfdbfe; border-radius: 10px; font-size: 14px; color: #1e40af; }
        .guest-notice .material-icons { flex-shrink: 0; font-size: 22px; margin-top: 1px; }
        .guest-notice a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .guest-notice a:hover { text-decoration: underline; }
        .header-section { margin-bottom: 24px; }
        .header-section h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; color: var(--text-dark); }
        .header-section p { font-size: 14px; color: var(--text-muted); }

        .stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        @media (max-width: 900px) { .stat-cards { grid-template-columns: repeat(2, 1fr); } }
        .stat-card { background: var(--card-white); padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.05); display: flex; align-items: flex-start; gap: 12px; }
        .stat-card .stat-icon { width: 44px; height: 44px; border-radius: 10px; background: transparent; color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-card .stat-label { font-size: 13px; color: var(--text-muted); margin-bottom: 4px; }
        .stat-card .stat-value { font-size: 20px; font-weight: 700; color: var(--text-dark); }

        .home-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .card { background: var(--card-white); padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .card-title-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
        .card-title-bar h2 { font-size: 14px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .card-title-bar .material-icons { background: transparent; color: var(--primary); padding: 8px; border-radius: 8px; font-size: 20px; }
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
        .quick-actions a.btn-outline:hover { background: transparent; }
        .quick-actions a.btn-secondary { background: #f3f4f6; color: #6b7280; border: none; }
        .quick-actions a.btn-secondary:hover { background: #e5e7eb; color: var(--text-dark); }

        .footer { background: transparent; padding: 20px 24px; margin-top: 40px; text-align: center; width: 100%; box-sizing: border-box; border-top: 1px solid rgba(255,255,255,0.08); }
        .footer .copyright { font-size: 12px; color: #94a3b8; margin: 0; }

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
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            border-left: none;
            border-right: none;
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
        .user-overview-row-clickable { cursor: pointer; }
        .user-overview-row-clickable:hover { background: #f8fafc !important; }
        #userViewRequestModal.is-open { visibility: visible !important; opacity: 1 !important; pointer-events: auto !important; }
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
            .overview-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .overview-layout { grid-template-columns: 1fr; }
            .overview-table { min-width: 540px; }
        }
        @media (max-width: 992px) {
            .overview-kpis { grid-template-columns: 1fr; }
            .stat-cards { grid-template-columns: repeat(2, 1fr); }
            /* Collapsed: icon-only. Expanded: full overlay + backdrop (click outside to close) */
            .sidebar.collapsed { width: 72px !important; min-width: 72px !important; max-width: 72px !important; }
            body.sidebar-collapsed .main { margin-left: 72px !important; width: calc(100% - 72px) !important; }
            .sidebar:not(.collapsed) { width: 280px !important; min-width: 280px !important; max-width: 280px !important; box-shadow: 4px 0 24px rgba(0,0,0,0.25); }
            body:not(.sidebar-collapsed) .main { margin-left: 0 !important; width: 100% !important; }
            .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 999; cursor: pointer; }
            body:not(.sidebar-collapsed) .sidebar-backdrop { display: block; }
        }
        @media (min-width: 993px) {
            .sidebar-backdrop { display: none !important; }
        }
        @media (max-width: 768px) {
            .main { padding: 18px 14px 24px; overflow-x: hidden; max-width: 100%; }
            .header-section h1 { font-size: 20px; }
            .header-section p { font-size: 13px; }
            .overview-kpis { grid-template-columns: 1fr; gap: 12px; max-width: 100%; min-width: 0; }
            .kpi-card { border-radius: 14px; padding: 14px; min-width: 0; }
            .kpi-value { font-size: 18px; }
            .overview-layout { gap: 14px; max-width: 100%; min-width: 0; }
            .overview-panel { padding: 14px; border-radius: 14px; max-width: 100%; min-width: 0; overflow-x: hidden; box-sizing: border-box; }
            .overview-panel-head { flex-wrap: wrap; gap: 8px; }
            .overview-panel-head h2 { font-size: 14px; }
            .panel-subtitle { font-size: 11px; }
            /* Card-style table: fits in viewport, no horizontal scroll */
            .table-wrap {
                overflow-x: hidden;
                border: none;
                margin: 0;
                max-width: 100%;
                min-width: 0;
                box-sizing: border-box;
            }
            .overview-table {
                width: 100%;
                max-width: 100%;
                min-width: 0;
                box-sizing: border-box;
            }
            .overview-table thead { display: none; }
            .overview-table tbody tr {
                display: block;
                margin-bottom: 12px;
                padding: 14px;
                border-top: 1px solid #e2e8f0;
                border-bottom: 1px solid #e2e8f0;
                border-left: none;
                border-right: none;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,.06);
                max-width: 100%;
                box-sizing: border-box;
            }
            .overview-table tbody tr:last-child { margin-bottom: 0; }
            .overview-table tbody td {
                display: block;
                padding: 6px 0 8px;
                border: none;
                border-bottom: 1px solid #f1f5f9;
                font-size: 13px;
                text-align: left;
                max-width: 100%;
                min-width: 0;
                box-sizing: border-box;
                word-break: break-word;
                overflow-wrap: break-word;
                overflow: hidden;
            }
            .overview-table tbody td:last-child { border-bottom: none; padding-bottom: 0; }
            .overview-table tbody td::before {
                content: attr(data-label);
                display: block;
                font-size: 11px;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: .04em;
                margin-bottom: 2px;
            }
            .overview-table tbody tr:has(td[colspan]) { padding: 14px; }
            .overview-table tbody tr:has(td[colspan]) td { display: block; padding: 0; border: none; }
            .overview-table tbody tr:has(td[colspan]) td::before { display: none; }
            .status-badge { min-width: 0 !important; padding: 2px 6px !important; font-size: 10px !important; border-radius: 4px !important; line-height: 1.2 !important; max-width: 100%; display: inline-block; }
            /* Role contents size only (compact) */
            .overview-table tbody td[data-label="Role"] .badge {
                padding: 2px 6px !important;
                font-size: 10px !important;
                border-radius: 4px !important;
                line-height: 1.2 !important;
                min-width: 0 !important;
            }
            .alert-item-link { flex-wrap: wrap; gap: 6px; padding: 10px 12px; }
            .alert-item .alert-left { flex: 1; min-width: 0; font-size: 12px; }
            .alert-item .alert-left span:last-child { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .alert-meta { font-size: 11px; white-space: normal; }
            .quick-actions { padding: 16px; max-width: 100%; min-width: 0; }
            .quick-actions .btn-wrap { display: flex; flex-wrap: wrap; gap: 8px; }
            .quick-actions .btn-wrap a { padding: 10px 14px; font-size: 13px; min-height: 44px; align-items: center; justify-content: center; }
            .donut-ring { width: 170px; height: 170px; }
            .donut-center { width: 112px; height: 112px; }
        }
        @media (max-width: 576px) {
            .main { padding: 14px 12px 20px; }
            .header-section h1 { font-size: 18px; }
            .header-section p { font-size: 12px; }
            .stat-cards { grid-template-columns: 1fr; gap: 12px; }
            .stat-card { padding: 16px; }
            .overview-panel { padding: 12px; }
            .overview-table tbody tr { padding: 12px; margin-bottom: 10px; border-radius: 10px; }
            .overview-table tbody td { font-size: 12px; }
            .quick-actions .btn-wrap a { width: 100%; justify-content: center; }
            .donut-ring { width: 150px; height: 150px; }
            .donut-center { width: 96px; height: 96px; }
            .donut-center strong { font-size: 24px; }
        }
        @media (max-width: 480px) {
            .main { padding: 12px 10px 16px; }
            .header-section h1 { font-size: 17px; }
            .overview-panel { padding: 12px; }
            .kpi-card { padding: 12px; }
            .kpi-value { font-size: 16px; }
            .kpi-label { font-size: 11px; }
            .overview-table tbody tr { padding: 12px; margin-bottom: 8px; }
            .overview-table tbody td { font-size: 12px; }
            .alert-item-link { padding: 10px 12px; min-height: 44px; }
        }
        @media (max-width: 400px) {
            .main { padding: 11px 10px 14px; }
            .header-section h1 { font-size: 16px; }
            .header-section p { font-size: 11px; }
            .overview-panel { padding: 10px; }
            .kpi-card { padding: 10px; }
            .kpi-value { font-size: 15px; }
            .overview-table tbody tr { padding: 10px; margin-bottom: 8px; border-radius: 10px; }
            .overview-table tbody td { font-size: 11px; }
            .quick-actions .btn-wrap a { font-size: 12px; padding: 9px 12px; }
        }
        @media (max-width: 360px) {
            .main { padding: 10px 8px 14px; }
            .header-section h1 { font-size: 16px; }
            .kpi-card { padding: 10px; }
            .overview-panel { padding: 10px; }
            .quick-actions { padding: 12px; }
        }
        @media (max-width: 320px) {
            .main { padding: 8px 8px 12px; }
            .header-section h1 { font-size: 15px; }
        }
    </style>
</head>
<body class="panel-user">
<script>document.body.classList.add('theme-' + (localStorage.getItem('prs-theme') || 'light'));</script>
<div class="container">
    @include('partials.user-sidebar')
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
<!-- header section -->
        <div class="header-section">
            <h1>Procurement Dashboard Overview</h1>
            <p>Monitor request flow, quote outcomes, and approvals in one business-ready view.</p>
        </div>

        @if(session('success'))
        <div class="alert-success" style="background:#d1fae5;color:#047857;padding:12px 16px;border-radius:10px;margin-bottom:18px;">
            {{ session('success') }}
        </div>
        @endif
<!-- stat- cards -->
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
                            <tr class="user-overview-row-clickable" role="button" tabindex="0" title="Click to view details">
                                <td data-label="Request ID">
                                    <button type="button" class="user-btn-view-request" style="display:none;position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;"
                                        data-request-id="{{ e($req['id'] ?? '') }}"
                                        data-item="{{ e($req['item'] ?? '') }}"
                                        data-quantity="{{ $req['quantity'] ?? 1 }}"
                                        data-date="{{ e($req['date'] ?? '') }}"
                                        data-status="{{ e($req['status'] ?? '') }}">View</button>
                                    {{ $req['id'] ?? '-' }}
                                </td>
                                <td data-label="Item">{{ $req['item'] ?? '-' }}</td>
                                <td data-label="Qty">{{ $req['quantity'] ?? 1 }}</td>
                                <td data-label="Date">{{ $req['date'] ?? '-' }}</td>
                                <td data-label="Status">
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

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="btn-wrap">
                <a href="{{ route('user.requests.create') }}" class="btn-primary"><span class="material-icons" style="font-size:16px;">add</span> Create Request</a>
                <a href="{{ route('user.requests.view') }}" class="btn-outline"><span class="material-icons" style="font-size:16px;">list_alt</span> View Request</a>
                <a href="{{ route('user.reports') }}" class="btn-outline"><span class="material-icons" style="font-size:16px;">analytics</span> View Reports</a>
                <a href="{{ route('user.support') }}" class="btn-secondary"><span class="material-icons" style="font-size:16px;">support</span> Support</a>
            </div>
        </div>

        @include('partials.footer')
    </div>
</div>

{{-- View request modal (dashboard Recent Requests – single-click row) --}}
<div id="userViewRequestModal" class="user-view-request-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="userViewRequestModalTitle" aria-hidden="true" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:1100;visibility:hidden;opacity:0;pointer-events:none;display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box;">
    <div class="user-view-request-modal-backdrop" id="userViewRequestModalBackdrop" style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:1;"></div>
    <div class="user-view-request-modal-content" id="userViewRequestModalContent" style="position:relative;z-index:2;background:var(--card-white,#fff);border-radius:12px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);max-width:440px;width:100%;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;">
        <div class="user-view-request-modal-header" style="padding:20px 24px;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
            <h2 id="userViewRequestModalTitle" class="user-view-request-modal-title" style="margin:0;font-size:18px;font-weight:600;color:#0f172a;">Request details</h2>
            <button type="button" class="user-view-request-modal-close" id="userViewRequestModalClose" aria-label="Close" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:24px;cursor:pointer;color:#64748b;">&times;</button>
        </div>
        <div class="user-view-request-modal-body" style="padding:24px;overflow-y:auto;flex:1;">
            <section class="user-view-request-section" style="margin-bottom:16px;">
                <h3 style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin:0 0 8px 0;">Request Details</h3>
                <div class="request-detail-card">
                    <dl class="view-request-dl">
                        <dt>Request ID</dt><dd id="user-view-req-id">—</dd>
                        <dt>Item</dt><dd id="user-view-req-item">—</dd>
                        <dt>Quantity</dt><dd id="user-view-req-quantity">—</dd>
                        <dt>Date</dt><dd id="user-view-req-date">—</dd>
                        <dt>Status</dt><dd id="user-view-req-status">—</dd>
                    </dl>
                </div>
            </section>
        </div>
        <div class="user-view-request-modal-footer" style="padding:16px 24px;border-top:1px solid #e2e8f0;flex-shrink:0;">
            <a href="{{ route('user.requests.view') }}" class="user-view-request-btn-all" style="display:inline-block;padding:10px 18px;background:#1d4ed8;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">View all my requests</a>
            <button type="button" class="user-view-request-btn-close" id="userViewRequestModalBtnClose" style="margin-left:8px;padding:10px 18px;background:#f1f5f9;color:#334155;border:none;border-radius:8px;cursor:pointer;font-weight:500;">Close</button>
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
    var backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) backdrop.addEventListener('click', function() {
        if (window.innerWidth <= 992 && !sidebar.classList.contains('collapsed')) setCollapsed(true);
    });
    try { if (localStorage.getItem(KEY) === '1') setCollapsed(true); } catch (e) {}
})();

(function() {
    var modal = document.getElementById('userViewRequestModal');
    var backdrop = document.getElementById('userViewRequestModalBackdrop');
    var closeBtn = document.getElementById('userViewRequestModalClose');
    var footerClose = document.getElementById('userViewRequestModalBtnClose');
    function openUserViewModal(btn) {
        if (!btn) return;
        var status = btn.getAttribute('data-status') || '—';
        document.getElementById('user-view-req-id').textContent = btn.getAttribute('data-request-id') || '—';
        document.getElementById('user-view-req-item').textContent = btn.getAttribute('data-item') || '—';
        document.getElementById('user-view-req-quantity').textContent = btn.getAttribute('data-quantity') || '1';
        document.getElementById('user-view-req-date').textContent = btn.getAttribute('data-date') || '—';
        var statusEl = document.getElementById('user-view-req-status');
        if (status === 'Rejected') statusEl.innerHTML = '<span class="badge-rejected">Rejected</span>';
        else if (status === 'Approved' || status === 'Completed') statusEl.innerHTML = '<span class="badge-approved">' + status + '</span>';
        else if (status === 'Pending') statusEl.innerHTML = '<span class="badge-pending">Pending</span>';
        else statusEl.textContent = status;
        if (modal) { modal.classList.add('is-open'); modal.style.visibility = 'visible'; modal.style.opacity = '1'; modal.style.pointerEvents = 'auto'; modal.setAttribute('aria-hidden', 'false'); }
        document.body && document.body.classList.add('modal-open');
    }
    function closeUserViewModal() {
        if (modal) { modal.classList.remove('is-open'); modal.style.visibility = 'hidden'; modal.style.opacity = '0'; modal.style.pointerEvents = 'none'; modal.setAttribute('aria-hidden', 'true'); }
        document.body && document.body.classList.remove('modal-open');
    }
    document.querySelectorAll('.user-btn-view-request').forEach(function(btn) {
        btn.addEventListener('click', function(e) { e.stopPropagation(); openUserViewModal(btn); });
    });
    var userTable = document.querySelector('.overview-table');
    if (userTable) {
        userTable.addEventListener('click', function(e) {
            var row = e.target.closest('tr.user-overview-row-clickable');
            if (!row) return;
            var btn = row.querySelector('.user-btn-view-request');
            if (btn) btn.click();
        });
        userTable.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var row = e.target.closest('tr.user-overview-row-clickable');
            if (!row) return;
            e.preventDefault();
            var btn = row.querySelector('.user-btn-view-request');
            if (btn) btn.click();
        });
    }
    if (backdrop) backdrop.addEventListener('click', closeUserViewModal);
    if (closeBtn) closeBtn.addEventListener('click', closeUserViewModal);
    if (footerClose) footerClose.addEventListener('click', closeUserViewModal);
    if (modal) modal.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeUserViewModal(); });
})();
</script>
</body>
</html>

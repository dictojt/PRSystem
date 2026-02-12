@extends('layouts.superadmin')
@section('title', 'Dashboard')
@section('breadcrumb')
Home &gt; <a href="{{ route('superadmin.dashboard') }}">Admin</a> &gt; Overview
@endsection
@section('content')
<div class="header-section">
    <h1>Overview</h1>
    <p>Welcome back! Here's what's happening in the system today.</p>
</div>
<div class="home-cards">
    <div class="card">
        <div class="card-title-bar">
            <h2>Total Admins</h2>
            <span class="material-icons">admin_panel_settings</span>
        </div>
        <div class="stat-value">{{ $totalAdmins ?? 0 }}</div>
        <div class="stat-label">Active system administrators</div>
    </div>
    <div class="card">
        <div class="card-title-bar">
            <h2>Pending Approvals</h2>
            <span class="material-icons">pending_actions</span>
        </div>
        <div class="stat-value">{{ $pendingApprovals ?? 0 }}</div>
        <div class="stat-label">Requests waiting for action</div>
    </div>
    <div class="card">
        <div class="card-title-bar">
            <h2>Approved Requests</h2>
            <span class="material-icons">assignment_turned_in</span>
        </div>
        <div class="stat-value">{{ $approvedRequests ?? 0 }}</div>
        <div class="stat-label">Completed this month</div>
    </div>
</div>
@endsection

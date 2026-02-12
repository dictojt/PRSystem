@extends('layouts.superadmin')
@section('title', 'System Reports')
@section('content')
<div class="header-section">
    <h1>System Reports</h1>
    <p>Overview of request statistics.</p>
</div>
<div class="home-cards" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-title-bar">
            <h2>Pending</h2>
            <span class="material-icons">pending_actions</span>
        </div>
        <div class="stat-value">{{ $pendingCount ?? 0 }}</div>
        <div class="stat-label">Awaiting approval</div>
    </div>
    <div class="card">
        <div class="card-title-bar">
            <h2>Approved this month</h2>
            <span class="material-icons">assignment_turned_in</span>
        </div>
        <div class="stat-value">{{ $approvedThisMonth ?? 0 }}</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="card">
        <div class="card-title-bar">
            <h2>Rejected this month</h2>
            <span class="material-icons">cancel</span>
        </div>
        <div class="stat-value">{{ $rejectedThisMonth ?? 0 }}</div>
        <div class="stat-label">Rejected</div>
    </div>
</div>
<div class="table-card">
    <div class="card-title-bar">Count by status</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byStatus ?? [] as $status => $total)
            <tr>
                <td><span class="badge @if($status === 'Pending') badge-pending @elseif($status === 'Approved') badge-approved @else badge-rejected @endif">{{ $status }}</span></td>
                <td>{{ $total }}</td>
            </tr>
            @endforeach
            @if(empty($byStatus) || $byStatus->isEmpty())
            <tr><td colspan="2" style="text-align: center; padding: 40px; color: #94a3b8;">No data yet.</td></tr>
            @endif
        </tbody>
    </table>
</div>
@endsection

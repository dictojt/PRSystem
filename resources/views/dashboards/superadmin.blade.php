@extends('layouts.superadmin')
@section('title', 'Dashboard')
@section('content')
<div class="header-section">
    <h1>Overview</h1>
    <p>Welcome back! Here's a summary of the system at a glance.</p>
</div>

<div class="dashboard-summary">
    <div class="home-cards">
        <div class="card card-stat">
            <div class="card-title-bar">
                <h2>Total Admins</h2>
                <span class="material-icons">admin_panel_settings</span>
            </div>
            <div class="stat-value">{{ $totalAdmins ?? 0 }}</div>
            <div class="stat-label">Active system administrators</div>
        </div>
        <div class="card card-stat">
            <div class="card-title-bar">
                <h2>Pending Approvals</h2>
                <span class="material-icons">pending_actions</span>
            </div>
            <div class="stat-value">{{ $pendingApprovals ?? 0 }}</div>
            <div class="stat-label">Requests waiting for action</div>
        </div>
        <div class="card card-stat">
            <div class="card-title-bar">
                <h2>Approved This Month</h2>
                <span class="material-icons">assignment_turned_in</span>
            </div>
            <div class="stat-value">{{ $approvedRequests ?? 0 }}</div>
            <div class="stat-label">Completed</div>
        </div>
    </div>

    <div class="dashboard-quick-links">
        <a href="{{ route('superadmin.requests') }}" class="quick-link">
            <span class="material-icons">inventory_2</span>
            <span>View all requests</span>
        </a>
        <a href="{{ route('superadmin.admins') }}" class="quick-link">
            <span class="material-icons">groups</span>
            <span>Manage users</span>
        </a>
    </div>
</div>

<div class="dashboard-charts">
    <div class="chart-card">
        <div class="card-title-bar">
            <h2>Requests by status</h2>
        </div>
        <div class="chart-wrap chart-doughnut">
            <canvas id="chartByStatus" height="220"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="card-title-bar">
            <h2>Requests in the last 7 days</h2>
        </div>
        <div class="chart-wrap chart-bar">
            <canvas id="chartLast7Days" height="220"></canvas>
        </div>
    </div>
</div>

<div class="table-card dashboard-table">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    var byStatus = @json($byStatus ?? new \Illuminate\Support\Collection());
    var last7Days = @json($requestsLast7Days ?? []);

    // Doughnut: requests by status
    var statusLabels = [];
    var statusData = [];
    var statusColors = { 'Pending': '#f59e0b', 'Approved': '#10b981', 'Rejected': '#ef4444', 'Processing': '#3b82f6', 'In Review': '#8b5cf6' };
    Object.keys(byStatus).forEach(function (s) {
        statusLabels.push(s);
        statusData.push(byStatus[s]);
    });
    if (statusLabels.length) {
        var ctx = document.getElementById('chartByStatus');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusLabels.map(function (l) { return statusColors[l] || '#94a3b8'; }),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    // Bar: last 7 days
    if (last7Days.length) {
        var ctx2 = document.getElementById('chartLast7Days');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: last7Days.map(function (d) { return d.label; }),
                    datasets: [{
                        label: 'Requests',
                        data: last7Days.map(function (d) { return d.count; }),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }
})();
</script>
@endpush
@endsection

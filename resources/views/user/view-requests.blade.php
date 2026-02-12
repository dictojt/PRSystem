@extends('layouts.user-panel')

@section('title', 'View Request')

@push('styles')
<style>
/* Status filter - critical layout so dropdown works even if main CSS is delayed */
.view-requests-page .status-filter-wrap { display: flex; align-items: center; gap: 12px; }
.view-requests-page .status-filter-dropdown { position: relative; }
.view-requests-page .status-filter-trigger {
    display: inline-flex; align-items: center; justify-content: space-between; gap: 12px;
    min-width: 150px; padding: 10px 14px; font-size: 14px; font-weight: 500; color: #1e293b;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.view-requests-page .status-filter-value { flex: 1; text-align: left; }
.view-requests-page .status-filter-chevron {
    width: 16px; height: 16px; flex-shrink: 0;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat center;
}
.view-requests-page .status-filter-menu {
    position: absolute; top: calc(100% + 6px); right: 0; min-width: 180px; padding: 6px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,.1); z-index: 50;
    display: none;
}
.view-requests-page .status-filter-dropdown.is-open .status-filter-menu { display: block; }
.view-requests-page .status-filter-option {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px;
    font-size: 14px; font-weight: 500; color: #334155; border-radius: 8px; cursor: pointer;
}
.view-requests-page .status-filter-option:hover { background: #f1f5f9; }
.view-requests-page .status-filter-option[aria-selected="true"] { background: #eff6ff; color: #1d4ed8; }
.view-requests-page .status-filter-option-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.view-requests-page .status-dot-all { background: #94a3b8; }
.view-requests-page .status-dot-pending { background: #f59e0b; }
.view-requests-page .status-dot-completed { background: #10b981; }
.view-requests-page .status-filter-label {
    font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em;
}
</style>
@endpush

@section('main')
<div class="view-requests-page">
<div class="header-section">
    <h1>View Request</h1>
    <p>View all your requested items and their status.</p>
</div>

@if(!auth()->check())
<div class="card" style="max-width: 560px;">
    <p style="color: #6b7280; margin: 0;">Sign in to view your requested items.</p>
    <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 16px;">Sign in with Google</a>
</div>
@else
<div class="card table-card">
    <div class="card-title-bar">
        <h2>My requested items</h2>
        <div class="status-filter-wrap">
            <span class="status-filter-label">Filter by status</span>
            <div class="status-filter-dropdown" id="status-filter-dropdown">
                <button type="button" class="status-filter-trigger" id="status-filter-trigger" aria-haspopup="listbox" aria-expanded="false" aria-label="Filter by status">
                    <span class="status-filter-value" id="status-filter-value">{{ ($filter ?? 'all') === 'all' ? 'All' : (($filter ?? 'all') === 'pending' ? 'Pending' : 'Completed') }}</span>
                    <span class="status-filter-chevron" aria-hidden="true"></span>
                </button>
                <div class="status-filter-menu" id="status-filter-menu" role="listbox" aria-hidden="true">
                    <div class="status-filter-option" role="option" data-value="all" tabindex="0" {{ ($filter ?? 'all') === 'all' ? 'aria-selected="true"' : '' }}>
                        <span class="status-filter-option-dot status-dot-all"></span>
                        <span>All</span>
                    </div>
                    <div class="status-filter-option" role="option" data-value="pending" tabindex="0" {{ ($filter ?? 'all') === 'pending' ? 'aria-selected="true"' : '' }}>
                        <span class="status-filter-option-dot status-dot-pending"></span>
                        <span>Pending</span>
                    </div>
                    <div class="status-filter-option" role="option" data-value="completed" tabindex="0" {{ ($filter ?? 'all') === 'completed' ? 'aria-selected="true"' : '' }}>
                        <span class="status-filter-option-dot status-dot-completed"></span>
                        <span>Completed</span>
                    </div>
                </div>
            </div>
            <input type="hidden" id="status-filter" value="{{ $filter ?? 'all' }}">
        </div>
    </div>
    @if(count($requests) > 0)
    <div class="table-responsive">
        <table class="data-table" id="requests-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Rejection reason</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                @php
                    $filterGroup = in_array($req['status'], ['Pending', 'Processing', 'In Review']) ? 'pending' : 'completed';
                @endphp
                <tr data-filter="{{ $filterGroup }}">
                    <td><strong>{{ $req['request_id'] }}</strong></td>
                    <td>{{ $req['item_name'] }}</td>
                    <td class="description-cell">
                        @if(!empty($req['description'] ?? null))
                            @php
                                $desc = $req['description'];
                                $descLen = strlen($desc);
                                $truncateAt = 60;
                                $isLong = $descLen > $truncateAt;
                            @endphp
                            @if($isLong)
                                <span class="item-description-short">{{ Str::limit($desc, $truncateAt) }}</span>
                                <span class="item-description-full" style="display: none;">{{ $desc }}</span>
                                <button type="button" class="description-toggle-link" aria-expanded="false" data-toggle="description">View full</button>
                            @else
                                <span class="item-description-text">{{ $desc }}</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $req['quantity'] }}</td>
                    <td>
                        @if($req['status'] === 'Pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($req['status'] === 'Approved')
                            <span class="badge badge-approved">Approved</span>
                        @elseif($req['status'] === 'Rejected')
                            <span class="badge badge-rejected">Rejected</span>
                        @else
                            <span class="badge badge-info">{{ $req['status'] }}</span>
                        @endif
                    </td>
                    <td>
                        @if(($req['status'] ?? '') === 'Rejected' && !empty($req['rejection_reason'] ?? null))
                            <span class="rejection-reason-text" title="{{ e($req['rejection_reason']) }}">{{ Str::limit($req['rejection_reason'], 50) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $req['created_at'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p id="filter-no-results" class="filter-no-results" style="display: none; color: #6b7280; padding: 24px; margin: 0;">No <span id="filter-no-results-label">pending</span> requests.</p>
    @else
    <p style="color: #6b7280; padding: 24px; margin: 0;">
        @if(($filter ?? 'all') !== 'all')
            No {{ $filter === 'pending' ? 'pending' : 'completed' }} requests.
        @else
            You have not submitted any requests yet. <a href="{{ route('user.requests.create') }}">Create a request</a> to get started.
        @endif
    </p>
    @endif
</div>
@endif
</div>

@if(auth()->check() && count($requests ?? []) > 0)
@push('scripts')
<script>
(function() {
    var hiddenInput = document.getElementById('status-filter');
    var trigger = document.getElementById('status-filter-trigger');
    var valueDisplay = document.getElementById('status-filter-value');
    var menu = document.getElementById('status-filter-menu');
    var dropdown = document.getElementById('status-filter-dropdown');
    var table = document.getElementById('requests-table');
    var noResults = document.getElementById('filter-no-results');
    var noResultsLabel = document.getElementById('filter-no-results-label');
    if (!hiddenInput || !table) return;
    var rows = table.querySelectorAll('tbody tr[data-filter]');
    var labels = { all: 'All', pending: 'Pending', completed: 'Completed' };

    function filterRows() {
        var value = hiddenInput.value;
        var visible = 0;
        rows.forEach(function(row) {
            var show = value === 'all' || row.getAttribute('data-filter') === value;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (noResults) {
            noResults.style.display = (value !== 'all' && visible === 0) ? 'block' : 'none';
            if (noResultsLabel) noResultsLabel.textContent = value === 'pending' ? 'pending' : 'completed';
        }
    }

    function setValue(value) {
        hiddenInput.value = value;
        if (valueDisplay) valueDisplay.textContent = labels[value] || value;
        filterRows();
        menu.setAttribute('aria-hidden', 'true');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        dropdown.classList.remove('is-open');
        var opts = menu.querySelectorAll('.status-filter-option');
        opts.forEach(function(o) { o.setAttribute('aria-selected', o.getAttribute('data-value') === value ? 'true' : 'false'); });
    }

    if (trigger && menu) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = dropdown.classList.toggle('is-open');
            menu.setAttribute('aria-hidden', !open);
            trigger.setAttribute('aria-expanded', open);
        });
        menu.querySelectorAll('.status-filter-option').forEach(function(opt) {
            opt.addEventListener('click', function() { setValue(opt.getAttribute('data-value')); });
            opt.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setValue(opt.getAttribute('data-value')); }
            });
        });
    }
    document.addEventListener('click', function() {
        if (dropdown && dropdown.classList.contains('is-open')) {
            dropdown.classList.remove('is-open');
            menu.setAttribute('aria-hidden', 'true');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
    });
    filterRows();

    // Long description: "View full" / "Show less" toggle
    table.querySelectorAll('.description-toggle-link').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var cell = btn.closest('.description-cell');
            if (!cell) return;
            var short = cell.querySelector('.item-description-short');
            var full = cell.querySelector('.item-description-full');
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            if (expanded) {
                if (short) short.style.display = '';
                if (full) full.style.display = 'none';
                btn.textContent = 'View full';
                btn.setAttribute('aria-expanded', 'false');
            } else {
                if (short) short.style.display = 'none';
                if (full) full.style.display = '';
                btn.textContent = 'Show less';
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });
})();
</script>
@endpush
@endif
@endsection

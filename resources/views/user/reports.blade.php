@extends('layouts.user-panel')

@section('title', 'View Reports')

@section('main')
<div class="header-section">
    <h1>View Reports</h1>
    <p>Summary of your requests and activity.</p>
</div>

<div class="card">
    <h3 style="font-size:16px;margin-bottom:16px;color:#64748b;">Reports Summary</h3>
    <ul class="report-list">
        @foreach($reports as $r)
        <li>
            <div>
                <strong>{{ $r['name'] }}</strong>
                <span style="font-size:13px;color:#94a3b8;">{{ $r['period'] }}</span>
            </div>
            <span style="font-weight:600;">{{ $r['value'] }}</span>
        </li>
        @endforeach
    </ul>
</div>
@endsection

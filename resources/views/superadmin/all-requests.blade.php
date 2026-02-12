@extends('layouts.superadmin')
@section('title', 'All Requests')
@section('content')
<div class="header-section">
    <h1>All Requests</h1>
    <p>View and monitor all product requests.</p>
</div>
<div class="table-card">
    <div class="card-title-bar">Requests</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Requestor</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
            <tr>
                <td>{{ $req->request_id }}</td>
                <td>{{ $req->user?->name ?? '—' }}</td>
                <td>{{ $req->item_name }}</td>
                <td>{{ $req->quantity ?? 1 }}</td>
                <td>
                    @if($req->status === 'Pending')
                        <span class="badge badge-pending">Pending</span>
                    @elseif($req->status === 'Approved')
                        <span class="badge badge-approved">Approved</span>
                    @else
                        <span class="badge badge-rejected">Rejected</span>
                    @endif
                </td>
                <td>{{ $req->created_at?->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">No requests yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if(method_exists($requests, 'links'))
    <div style="padding: 16px 24px;">{{ $requests->links() }}</div>
    @endif
</div>
@endsection

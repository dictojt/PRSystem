@extends('layouts.superadmin')
@section('title', 'Approvers')
@section('content')
<div class="header-section">
    <h1>Approvers</h1>
    <p>Users who can approve or reject requests.</p>
</div>
<div class="table-card">
    <div class="card-title-bar">Approver List</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Added</th>
            </tr>
        </thead>
        <tbody>
            @forelse($approvers ?? [] as $approver)
            <tr>
                <td>{{ $approver->name }}</td>
                <td>{{ $approver->email }}</td>
                <td>{{ $approver->created_at?->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align: center; padding: 40px; color: #94a3b8;">No approvers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

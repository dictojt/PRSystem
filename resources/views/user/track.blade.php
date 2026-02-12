@extends('layouts.user-panel')

@section('title', 'Track Item')

@section('main')
<div class="header-section">
    <h1>Track Item</h1>
    <p>Enter your Request ID to check status.</p>
</div>

<div class="card" style="max-width: 480px;">
    <form action="{{ route('user.track') }}" method="GET">
        <div class="form-group">
            <label for="request_id">Request ID</label>
            <input type="text" id="request_id" name="request_id" value="{{ $requestId ?? '' }}" placeholder="e.g. REQ-1001" style="text-transform: uppercase;">
        </div>
        <button type="submit" class="btn btn-primary"><span class="material-icons" style="font-size:18px;">search</span> Track</button>
    </form>

    @if(request()->has('request_id'))
        @if($result)
            <div class="track-result">
                <strong>Request ID:</strong> {{ strtoupper($requestId) }}<br>
                <strong>Item:</strong> {{ $result['item'] }}<br>
                <strong>Quantity:</strong> {{ $result['quantity'] ?? 1 }}<br>
                <strong>Date:</strong> {{ $result['date'] }}<br>
                <strong>Status:</strong> {{ $result['status'] }}
            </div>
        @else
            <div class="track-result not-found">
                No request found with ID "{{ $requestId }}". Try REQ-2025-02-00001, REQ-2025-02-00002, or REQ-2025-02-00003 (after seeding).
            </div>
        @endif
    @endif
</div>
@endsection

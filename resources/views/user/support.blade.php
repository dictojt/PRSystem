@extends('layouts.user-panel')

@section('title', 'Support')

@section('main')
<div class="header-section">
    <h1>Support</h1>
    <p>Get help or contact the system administrator.</p>
</div>

<div class="card" style="max-width: 560px;">
    <div class="support-card">
        <strong>Contact Support</strong>
        <p style="margin:8px 0 0;font-size:14px;color:#64748b;">For technical issues or questions about the Product Request System.</p>
        <a href="mailto:support@example.com" class="btn btn-primary" style="margin-top:12px;">support@example.com</a>
    </div>
</div>
@endsection

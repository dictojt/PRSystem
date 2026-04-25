@extends('layouts.user-panel')

@section('title', 'Settings')

@section('main')
<div class="header-section">
    <h1>Settings</h1>
    <p>Manage your preferences.</p>
</div>

<div class="card settings-card-appearance" style="max-width: 560px;">
    <div class="card-title-bar">
        <h2>Appearance</h2>
    </div>
    <div class="settings-appearance">
        <div class="form-group">
            <label for="theme" class="form-label">Theme</label>
            <select id="theme" class="form-control" data-theme-toggle aria-label="Theme">
                <option value="light">Light</option>
                <option value="dark">Dark</option>
            </select>
        </div>
        <p class="settings-theme-note">Your choice is saved in this browser only.</p>
    </div>
</div>
@endsection

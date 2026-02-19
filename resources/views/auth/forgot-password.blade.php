<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>Forgot Password | Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/home.css', 'resources/js/app.js'])
</head>
<body>

    <div class="gov-bar">
        <span class="ph-icon" aria-hidden="true"></span>
        <span>GOVPH</span>
        <span class="sep"></span>
        <span>REPUBLIC OF THE PHILIPPINES</span>
    </div>

    <div class="branding">
        <div class="dict-logo">DICT Logo</div>
        <div class="system-name">
            <h1>Product Request System</h1>
            <p>Internal Administrative Portal</p>
        </div>
    </div>
    <div class="branding-lines">
        <div class="band-red"></div>
        <div class="band-blue"></div>
    </div>

    <div class="main-wrap">
        <div class="signin-card">
            <div class="card-top-band">
                <span class="band-red"></span>
                <span class="band-blue"></span>
            </div>
            <div class="card-body">
                <div class="building-icon">
                    <span class="material-icons">lock_reset</span>
                </div>
                <h2>Forgot Password</h2>
                <p class="welcome-text">
                    Enter your account email. Your request will be sent to an administrator. Once approved, a new default password will be sent to this email.
                </p>

                @if(session('error'))
                    <div class="alert-error">{{ session('error') }}</div>
                @endif
                @if(session('message'))
                    <div class="alert-success" style="background: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 16px;">{{ session('message') }}</div>
                @endif

                <form method="post" action="{{ route('forgot-password.submit') }}" style="margin-bottom: 20px;">
                    @csrf
                    <input type="email" name="email" placeholder="Your email" required
                           style="width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; margin-bottom: 12px;"
                           value="{{ old('email') }}">
                    @error('email')
                        <div class="alert-error" style="margin-bottom: 12px;">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn-gmail" style="width: 100%; justify-content: center; cursor: pointer; border: none;">
                        Request password reset
                    </button>
                </form>

                <p class="terms">
                    <a href="{{ route('home') }}">← Back to Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p class="copyright">© {{ date('Y') }} Product Request System - Department of Information and Communications Technology</p>
    </footer>

</body>
</html>

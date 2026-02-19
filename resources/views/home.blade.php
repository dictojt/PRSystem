<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>Product Request System | DICT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/home.css', 'resources/js/app.js'])
</head>
<body>

    <!-- GOVPH Bar (left-aligned like reference) -->
    <div class="gov-bar">
        <span class="ph-icon" aria-hidden="true"></span>
        <span>GOVPH</span>
        <span class="sep"></span>
        <span>REPUBLIC OF THE PHILIPPINES</span>
    </div>

    <!-- Branding -->
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

    <!-- Main -->
    <div class="main-wrap">
        <div class="signin-card">
            <div class="card-top-band">
                <span class="band-red"></span>
                <span class="band-blue"></span>
            </div>
            <div class="card-body">
                <div class="building-icon">
                    <span class="material-icons">account_balance</span>
                </div>
                <h2>Sign In</h2>
                <p class="welcome-text">
                    Welcome to the <strong>Product Request System</strong>. There is no separate sign-up form — use the button below to sign in with your Google account. Your account is created the first time you sign in. Your role (user, approver, or super admin) is set in the system.
                </p>

                @if(session('error'))
                    <div class="alert-error">{{ session('error') }}</div>
                @endif

                <a href="{{ route('auth.google') }}" class="btn-gmail">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sign in with Google
                </a>
                <p class="terms">
                    <a href="{{ route('forgot-password.form') }}">Forgot password?</a> Request a reset and an administrator will send you a new default password.
                </p>
                <p class="terms">
                    By signing in, you agree to the <a href="#">Data Privacy Terms &amp; Conditions</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="copyright">© {{ date('Y') }} Product Request System - Department of Information and Communications Technology</p>
    </footer>

</body>
</html>

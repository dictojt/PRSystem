<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        $clientId = trim(env('GOOGLE_CLIENT_ID') ?: config('services.google.client_id') ?? '');
        $clientSecret = trim(env('GOOGLE_CLIENT_SECRET') ?: (config('services.google.client_secret') ?? ''));

        if ($clientId === '' || $clientSecret === '') {
            return redirect()->route('home')->with('error', 'Google sign-in is not configured. In .env add your GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET (from Google Cloud Console), save the file, then run in terminal: php artisan config:clear');
        }

        // Use current request URL for callback so when accessed via ngrok/tunnel, Google redirects back to the same host (not localhost)
        $callbackUrl = route('auth.google.callback');
        return Socialite::driver('google')->redirectUrl($callbackUrl)->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        $callbackUrl = route('auth.google.callback');
        $googleUser = null;
        try {
            // Use same callback URL as the redirect (current request origin) so token exchange matches when using ngrok
            $googleUser = Socialite::driver('google')->redirectUrl($callbackUrl)->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Google OAuth InvalidStateException', [
                'message' => $e->getMessage(),
                'host' => request()->getHost(),
                'is_secure' => request()->secure(),
                'user_agent' => request()->userAgent(),
            ]);

            // Browser privacy protections (common on Brave localhost) can drop callback state cookies.
            // In local/dev only, retry once with stateless mode to keep sign-in working.
            $canFallbackStateless = app()->environment('local') || in_array(request()->getHost(), ['localhost', '127.0.0.1'], true);
            if (! $canFallbackStateless) {
                return redirect()->route('home')->with('error', 'Session expired or invalid. Please try signing in again.');
            }

            try {
                $googleUser = Socialite::driver('google')
                    ->redirectUrl($callbackUrl)
                    ->stateless()
                    ->user();
                Log::info('Google OAuth stateless fallback succeeded', [
                    'host' => request()->getHost(),
                ]);
            } catch (\Exception $fallbackError) {
                Log::error('Google OAuth stateless fallback failed', [
                    'message' => $fallbackError->getMessage(),
                    'trace' => $fallbackError->getTraceAsString(),
                    'callback_url' => $callbackUrl,
                ]);
                return redirect()->route('home')->with('error', 'Session expired or invalid. Please try signing in again.');
            }
        } catch (\Exception $e) {
            Log::error('Google OAuth error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'callback_url' => $callbackUrl,
            ]);
            return redirect()->route('home')->with('error', 'Unable to sign in with Google. Please try again.');
        }

        $email = $googleUser->getEmail();
        if (empty($email)) {
            return redirect()->route('home')->with('error', 'Your Google account did not provide an email. Please allow email access and try again.');
        }

        // Optional: restrict login to certain email domains (e.g. in .env: ALLOWED_EMAIL_DOMAINS=dict.gov.ph,gmail.com)
        $allowedDomains = array_filter(array_map('trim', explode(',', env('ALLOWED_EMAIL_DOMAINS', ''))));
        if (! empty($allowedDomains)) {
            $domain = strtolower(substr($email, strpos($email, '@') + 1));
            if (! in_array($domain, $allowedDomains, true)) {
                return redirect()->route('home')->with('error', 'Sign-in is only allowed for authorized email domains. Your domain is not in the allowed list.');
            }
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            $user->update([
                'avatar' => $googleUser->getAvatar(),
                'name' => $googleUser->getName() ?? $user->name,
                'email' => $email,
            ]);
        } else {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'name' => $googleUser->getName() ?? $user->name,
                ]);
            } else {
                $user = User::create([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName() ?? 'User',
                    'email' => $email,
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(str()->random(32)),
                    'role' => 'user',
                ]);
            }
        }

        if (isset($user->is_active) && $user->is_active === false) {
            return redirect()->route('home')->with('error', 'Your account has been deactivated. Please contact an administrator.');
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard.redirect'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    }

    /**
     * Show forgot password request form (user requests reset; superadmin approves later).
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Submit forgot password request (creates pending request for superadmin to approve).
     */
    public function submitForgotPasswordRequest(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return redirect()->route('forgot-password.form')
                ->with('error', 'No account found with that email.');
        }

        // Avoid duplicate pending request
        $exists = PasswordResetRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
        if ($exists) {
            return redirect()->route('forgot-password.form')
                ->with('message', 'You already have a pending request. An administrator will process it soon.');
        }

        PasswordResetRequest::create(['user_id' => $user->id]);
        return redirect()->route('forgot-password.form')
            ->with('message', 'Request submitted. An administrator will review it and send a new password to your email.');
    }
}

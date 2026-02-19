<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->fixUrlsForTunnelOrProxy();
    }

    /**
     * When the app is accessed via a forwarded port (e.g. ngrok, dev tunnel), use the
     * request's scheme and host for URLs so redirects and links point to the reachable URL.
     */
    protected function fixUrlsForTunnelOrProxy(): void
    {
        if ($this->app->runningInConsole() || ! request()->hasHeader('Host')) {
            return;
        }

        $host = request()->getHost();
        $isLocalHost = in_array($host, ['localhost', '127.0.0.1'], true);
        $isHttps = request()->secure();

        // Always use request URL when accessed via a different host (e.g. ngrok) so
        // post-login redirects and links work instead of sending users to unreachable localhost.
        if (! $isLocalHost) {
            $rootUrl = request()->getScheme() . '://' . $host;
            if (request()->getPort() && ! in_array((int) request()->getPort(), [80, 443], true)) {
                $rootUrl .= ':' . request()->getPort();
            }
            URL::forceRootUrl(rtrim($rootUrl, '/'));
        }

        // Session cookie policy for OAuth:
        // - force secure cookies on HTTPS hosts (e.g. ngrok)
        // - force non-secure on localhost/http to avoid InvalidStateException
        if ($isHttps) {
            config(['session.secure' => true]);
        } elseif ($isLocalHost) {
            config([
                'session.secure' => false,
                'session.same_site' => 'lax',
            ]);
        }

        if (! $isLocalHost && $this->app->environment('local') && is_file(public_path('hot'))) {
            $this->useTunnelUrlForViteHot();
        }
    }

    /**
     * Point Vite at a "hot" file that uses the request host so dev assets load over the tunnel.
     * Requires the Vite dev server to be reachable (e.g. host: true in vite.config and port forwarded).
     */
    protected function useTunnelUrlForViteHot(): void
    {
        $originalHot = @file_get_contents(public_path('hot'));
        if ($originalHot === false) {
            return;
        }
        $parsed = parse_url(trim($originalHot));
        $port = $parsed['port'] ?? 5173;
        $viteDevUrl = request()->getScheme() . '://' . request()->getHost() . ':' . $port;

        $hotPath = storage_path('app/vite-hot-' . md5(request()->getHost()) . '.txt');
        if (file_put_contents($hotPath, $viteDevUrl) !== false) {
            Vite::useHotFile($hotPath);
        }
    }
}

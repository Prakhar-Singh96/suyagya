<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache; // ✅ ये जरूरी है
use App\Models\Redirect;              // ✅ ये भी जरूरी है
use Illuminate\Support\Facades\Schema;

class CheckForRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ FIX 1: Migration रूट को छोड़ दें
        if ($request->is('run-migration') || $request->is('run-migration*')) {
            return $next($request);
        }

        // ✅ FIX 2: अगर 'redirects' टेबल अभी तक डेटाबेस में नहीं बनी है, तो चेक न करें
        if (!\Schema::hasTable('redirects')) {
            return $next($request);
        }

        $path = '/' . trim($request->path(), '/');

        $redirect = Cache::remember("redirect_{$path}", 3600, function () use ($path) {
            return Redirect::where('old_url', $path)->first();
        });

        if ($redirect) {
            return redirect($redirect->new_url, $redirect->status_code);
        }

        return $next($request);
    }
}

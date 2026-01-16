<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache; // ✅ ये जरूरी है
use App\Models\Redirect;              // ✅ ये भी जरूरी है

class CheckForRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // URL को साफ करें (slash / को handle करें)
        $path = '/' . trim($request->path(), '/');

        // Cache चेक करें
        $redirect = Cache::remember("redirect_{$path}", 3600, function () use ($path) {
            return Redirect::where('old_url', $path)->first();
        });

        if ($redirect) {
            return redirect($redirect->new_url, $redirect->status_code);
        }

        return $next($request);
    }
}

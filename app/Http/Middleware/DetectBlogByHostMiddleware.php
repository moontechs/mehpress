<?php

namespace App\Http\Middleware;

use App\Models\Blog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectBlogByHostMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->header('X-Forwarded-Host', $request->header('Host'));

        $blog = Blog::where('host', '=', 'https://'.$host)
            ->orWhere('host', '=', 'http://'.$host)
            ->firstOrFail();

        $request->session()->put('blog', $blog);

        if (! $request->session()->has('language')) {
            $request->session()->put('language', $blog->default_language);
        }

        return $next($request);
    }
}

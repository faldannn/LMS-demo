<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);

    $headers = config('security.headers', []);

    if ($value = $headers['x_frame_options'] ?? null) {
      $response->headers->set('X-Frame-Options', $value);
    }
    if ($value = $headers['x_content_type_options'] ?? null) {
      $response->headers->set('X-Content-Type-Options', $value);
    }
    if ($value = $headers['referrer_policy'] ?? null) {
      $response->headers->set('Referrer-Policy', $value);
    }
    if ($value = $headers['permissions_policy'] ?? null) {
      $response->headers->set('Permissions-Policy', $value);
    }
    
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
    $response->headers->set('Content-Security-Policy', "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data: blob: ui-avatars.com; font-src 'self' https: data:;");

    if ($request->secure() || app()->environment('production')) {
      $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    return $response;
  }
}

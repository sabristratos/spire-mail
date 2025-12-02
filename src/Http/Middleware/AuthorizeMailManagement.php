<?php

namespace SpireMail\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeMailManagement
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationEnabled = config('spire-mail.authorization.enabled', true);

        if (! $authorizationEnabled) {
            return $next($request);
        }

        $gateName = config('spire-mail.authorization.gate', 'manage-mail-templates');

        if (! Gate::has($gateName)) {
            Gate::define($gateName, fn ($user) => true);
        }

        if (! Gate::allows($gateName)) {
            abort(403, __('spire-mail::messages.unauthorized'));
        }

        return $next($request);
    }
}

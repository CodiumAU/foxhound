<?php

namespace Foxhound\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Access\Gate;

class Authorize
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected Gate $gate
    ) {
    }

    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $this->gate->authorize('viewFoxhound');

        return $next($request);
    }
}

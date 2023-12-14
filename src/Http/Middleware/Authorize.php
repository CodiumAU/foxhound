<?php

namespace Foxhound\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
        if ($this->gate->denies('viewFoxhound')) {
            return Response::view('foxhound::unauthorized', [], 403);
        }

        return $next($request);
    }
}

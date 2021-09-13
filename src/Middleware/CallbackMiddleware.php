<?php

namespace Psychai\FittCommunicator\Middleware;

use Closure;
use Illuminate\Http\Request;

class CallbackMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (!$request->has('pid')) {
            return abort(401);
        }
        if (!$request->has('c')) {
            return abort(401);
        }
        $hash = hash('sha256',
            $request->get('pid') .
            $request->get('action') .
            $request->get('action_param') .
            $request->get('pass1') .
            $request->get('pass2') .
            $request->get('pass3') .
            $request->get('pass4') .
            $request->get('pass5') .
            config('fitt-communicator.client_secret')
        );

        if ($request->get('c') !== $hash) {
            return abort(401);
        }

        return $next($request);
    }
}

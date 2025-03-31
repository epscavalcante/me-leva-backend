<?php

namespace App\Http\Middleware;

use App\Account;
use App\Services\TokenGenerator\TokenGenerator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
    public function __construct(private readonly TokenGenerator $tokenGenerator) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (! $token) {
            abort(HttpResponse::HTTP_FORBIDDEN, 'Bearer token not provided');
        }

        $data = $this->tokenGenerator->decode($token);
        $user = Account::find($data['account_id']);
        if (! $user) {
            abort(HttpResponse::HTTP_FORBIDDEN, 'Bearer token not provided');
        }

        Auth::setUser(
            user: $user
        );

        return $next($request);

        /*
        try {
            $data = $this->tokenGenerator->decode($token);
            $user = Account::find($data['account_id']);

        } catch (\Throwable $th) {
            throw $th;
        }
        */
    }
}

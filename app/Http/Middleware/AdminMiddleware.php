<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Contracts\Auth\Authenticatable;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $userController = new UserController();
            $user = Auth::user();
            if ($userController->isAdmin($user)) {
                return $next($request);
            } else {
                Log::error('User is not an admin');
                return response()->json([
                    'message' => 'Unauthorized User',
                ], 401);
            }
        } catch (\Throwable $th) {
            Log::error('Exception caught in AdminMiddleware: ' . $th->getMessage());
            return response()->json([
                'message' => 'Unauthorized User',
            ], 401);
        }
    }
}

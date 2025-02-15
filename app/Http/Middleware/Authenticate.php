<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // Redirect berdasarkan prefix URL
            if ($request->is('admin*')) {
                return route('admin.login'); // Redirect ke admin login
            }
            return route('user.login'); // Redirect ke user login
        }
    }

    /**
     * Handle unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu.',
            ], 401);
        }

        // Redirect ke halaman login yang sesuai
        return redirect($this->redirectTo($request));
    }
}

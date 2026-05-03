<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AutoLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $user = User::first();
            
            if (!$user) {
                $user = User::factory()->create([
                    'name' => 'Auto User',
                    'email' => 'auto@example.com',
                ]);
            }
            
            Auth::login($user);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pemesanan;
class CheckOrderOwner
{
    public function handle($request, Closure $next)
    {
        $orderId = $request->route('orderId');
        $order = Pemesanan::where('order_id', $orderId)->first();

        if (!$order || $order->user_id !== auth()->id()) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}

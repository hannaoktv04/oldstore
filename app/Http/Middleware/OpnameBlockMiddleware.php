<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\OpnameSession;

class OpnameBlockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $activeSession = OpnameSession::where('status', 'aktif')->where('block_transaction', true)->first();

        if ($activeSession) {
            return redirect()->back()->with('error', 'Transaksi tidak dapat dilakukan selama stock opname berlangsung.');
        }

        return $next($request);
    }
}

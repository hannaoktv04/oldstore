<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\OpnameSession;
use Illuminate\Support\Facades\View;

class OpnameBlockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $activeSession = OpnameSession::where('status', 'aktif')->where('block_transaction', true)->first();

        View::share('opnameAktif', $activeSession ? true : false);
        View::share('opnameDimulai', $activeSession ? $activeSession->tanggal_mulai : null);

        return $next($request);
    }

}

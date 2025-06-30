<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\OpnameSession;

class CheckOpnameActive
{
	public function handle(Request $request, Closure $next)
	{
		$activeSession = OpnameSession::where('status', 'aktif')->first();
		if ($activeSession) {
			return redirect()->route('admin.stock_opname.index')->with('error', 'Transaksi dinonaktifkan saat stok opname!');
		}
		return $next($request);
	}
}

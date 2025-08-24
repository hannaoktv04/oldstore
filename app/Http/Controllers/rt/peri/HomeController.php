<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\Item;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $items = Item::with(['category', 'photo'])
            ->withSum('stocks', 'qty')
            ->withSum('details', 'qty_requested')
            ->whereHas('state', fn($q) => $q->where('is_archived', '0'))
            ->orderByRaw('(COALESCE(stocks_sum_qty, 0) = 0) ASC')
            ->orderByDesc('details_sum_qty_requested')
            ->take(24)
            ->get();

        return view('peri::layouts.home', compact('items'));
    }
}
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
            ->withSum('details', 'qty_requested')
            ->orderByRaw('stok_minimum = 0')
            ->orderByDesc('details_sum_qty_requested')
            ->take(24)
            ->get();

        return view('peri::layouts.home', compact('items'));
    }
}
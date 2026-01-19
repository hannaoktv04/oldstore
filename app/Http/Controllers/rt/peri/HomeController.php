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
            ->withSum('details as details_sum_qty_requested', 'qty_requested')
            ->orderBy('stok', 'asc')            
            ->orderByDesc('details_sum_qty_requested')
            ->take(24)
            ->get();

        return view('peri::layouts.home', compact('items'));
    }
}
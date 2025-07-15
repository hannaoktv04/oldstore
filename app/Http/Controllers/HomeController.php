<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        $items = Item::with(['category', 'photo'])
            ->withSum('details', 'qty_requested')
            ->orderByRaw('stok_minimum = 0')
            ->orderByDesc('details_sum_qty_requested')
            ->take(24)
            ->get();

        return view('layouts.home', compact('items'));
    }

}
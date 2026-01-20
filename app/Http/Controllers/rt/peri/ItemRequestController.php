<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ItemRequest;
use App\Models\Order; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ItemRequestController extends Controller
{
    public function history()
    {
        $userId = Auth::id(); 

        $requests = ItemRequest::with([
            'details.item.category',
            'details.item.photo',
            'itemDelivery'
        ])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        
        $orders = Order::with(['items.item'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('peri::user.history', compact('requests', 'orders'));
    }
    
}
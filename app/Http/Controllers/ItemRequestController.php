<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function history()
    {
        $requests = \App\Models\ItemRequest::with(['details.item'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('item_requests.history', compact('requests'));
    }

}

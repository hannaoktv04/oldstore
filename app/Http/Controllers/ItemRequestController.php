<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ItemRequest;

class ItemRequestController extends Controller
{
    public function history()
    {
        $requests = \App\Models\ItemRequest::with(['details.item'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.history', compact('requests'));
    }

}

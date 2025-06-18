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

    public function updateTanggalPengambilan(Request $request, $id)
    {
        dd($request->all());
         $validator = Validator::make($request->all(), [
            'tanggal_pengambilan' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemRequest = ItemRequest::findOrFail($id);
        $itemRequest->tanggal_pengambilan = $request->tanggal_pengambilan;
        $itemRequest->save();

        return response()->json(['message' => 'Tanggal pengambilan diperbarui']);
    }

}

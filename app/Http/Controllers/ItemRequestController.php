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
        $requests = ItemRequest::with([
            'details.item.category',
            'details.item.photo',
            'itemDelivery'
        ])
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

        return view('user.history', compact('requests'));
    }

    public function updateTanggalPengambilan(Request $request, $id)
    {
        dd($request->all());
         $validator = Validator::make($request->all(), [
            'tanggal_pengiriman' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemRequest = ItemRequest::findOrFail($id);
        $itemRequest->tanggal_pengiriman = $request->tanggal_pengiriman;
        $itemRequest->save();

        return response()->json(['message' => 'Tanggal Pengiriman diperbarui']);
    }

    public function konfirmasiUser($id)
    {
        $request = ItemRequest::findOrFail($id);

        if ($request->status !== 'received') {
            return back()->with('error', 'Pesanan belum bisa dikonfirmasi.');
        }

        $request->user_confirmed = true;
        $request->save();

        return back()->with('success', 'Pesanan telah dikonfirmasi.');
    }

    public function showENota($id)
    {
        $request = ItemRequest::with(['user', 'details.item.category', 'itemDelivery.staff'])->findOrFail($id);
        
        if ($request->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('user.enota', compact('request'));
    }

}

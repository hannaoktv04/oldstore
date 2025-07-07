<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemDelivery;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Storage;

class StaffPengirimanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff_pengiriman']);
    }

    public function index()
    {
        $user = Auth::user();
        $pengiriman = ItemDelivery::with(['request.user'])->latest()->get();

        return view('staff-pengiriman.dashboard', compact('user', 'pengiriman'));
    }

    public function show($kodeResi)
    {
        $id = (int) str_replace('KP', '', $kodeResi);
        $pengajuan = ItemRequest::with(['user', 'details.item'])->findOrFail($id);

        if ($pengajuan->status === 'received') {
            return redirect()->route('staff-pengiriman.dashboard')
                ->with('info', 'Barang sudah dikonfirmasi dan diterima.');
        }

        $delivery = ItemDelivery::firstOrNew(['item_request_id' => $pengajuan->id]);

        if (!$delivery->staff_pengiriman) {
            $delivery->staff_pengiriman = auth()->user()->nama;
            $delivery->operator_id = auth()->id();
            $delivery->tanggal_kirim = now();
            $delivery->status = 'in_progress';
            $delivery->save();
        }

        return view('staff-pengiriman.konfirmasi', compact('pengajuan', 'kodeResi'));
    }

    public function submit(Request $request, $kodeResi)
    {
        $id = (int) str_replace('KP', '', $kodeResi);
        $pengajuan = ItemRequest::findOrFail($id);

        $request->validate([
            'bukti_foto' => 'required|image|max:2048',
            'catatan' => 'nullable|string|max:255',
        ]);

        $path = $request->file('bukti_foto')->store('bukti_pengiriman', 'public');

        $pengajuan->update([
            'status' => 'received',
        ]);

        ItemDelivery::updateOrCreate(
            ['item_request_id' => $pengajuan->id],
            [
                'operator_id' => auth()->id(),
                'tanggal_kirim' => now(),
                'bukti_foto' => $path,
                'status' => 'completed',
                'catatan' => $request->catatan,
                'staff_pengiriman' => auth()->user()->nama,
            ]
        );

        return redirect()->route('staff-pengiriman.dashboard')->with('success', 'Pesanan telah dikonfirmasi.');
    }
}

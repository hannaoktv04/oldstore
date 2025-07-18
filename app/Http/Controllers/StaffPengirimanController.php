<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemDelivery;
use App\Models\ItemRequest;

class StaffPengirimanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff_pengiriman']);
    }

    public function index()
    {
        $user = Auth::user();
        $pengiriman = ItemDelivery::with(['request.user', 'request.details.item'])
            ->where('status', 'in_progress')
            ->where('staff_pengiriman', $user->nama)
            ->latest()
            ->get();

        return view('staff-pengiriman.dashboard', compact('user', 'pengiriman'));
    }

    public function show($kodeResi)
    {
        $id = (int) str_replace('KP', '', $kodeResi);
        $pengajuan = ItemRequest::with(['user', 'details.item', 'itemDelivery'])->findOrFail($id);

        $delivery = $pengajuan->itemDelivery;

        if (!$delivery) {
            // Belum ada pengiriman sama sekali, kurir bisa assign sendiri
            $delivery = ItemDelivery::create([
                'item_request_id' => $pengajuan->id,
                'operator_id' => auth()->id(),
                'tanggal_kirim' => now(),
                'staff_pengiriman' => auth()->user()->nama,
                'status' => 'in_progress',
            ]);
        } else {
            if ($delivery->staff_pengiriman) {
                if ($delivery->staff_pengiriman !== auth()->user()->nama) {
                    return redirect()->route('staff-pengiriman.dashboard')
                        ->with('error', 'Pengiriman sudah diassign ke staff lain: ' . $delivery->staff_pengiriman);
                }
                // Kalau sudah diassign ke staff ini, biarkan saja, tidak usah overwrite
            } else {
                // Kalau belum diassign, assign sekarang
                $delivery->update([
                    'staff_pengiriman' => auth()->user()->nama,
                    'operator_id' => auth()->id(),
                    'tanggal_kirim' => now(),
                    'status' => 'in_progress',
                ]);
            }
        }

        return view('staff-pengiriman.konfirmasi', compact('pengajuan', 'kodeResi'));
    }

    public function submit(Request $request, $kodeResi)
    {
        $id = (int) str_replace('KP', '', $kodeResi);
        $pengajuan = ItemRequest::with('itemDelivery')->findOrFail($id);

        $request->validate([
            'bukti_foto' => 'required|image|max:2048',
            'catatan' => 'nullable|string|max:255',
        ]);

        $path = $request->file('bukti_foto')->store('bukti_pengiriman', 'public');

        $pengajuan->update([
            'status' => 'received',
        ]);

        $pengajuan->itemDelivery->update([
            'operator_id' => auth()->id(),
            'bukti_foto' => $path,
            'status' => 'completed',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('staff-pengiriman.dashboard')->with('success', 'Pesanan telah dikonfirmasi.');
    }

    public function onProgress()
    {
        $user = Auth::user();

        $pengiriman = ItemDelivery::with(['request.user', 'request.details.item'])
            ->where('status', 'in_progress')
            ->where('staff_pengiriman', $user->nama)
            ->latest()
            ->get();

        return view('staff-pengiriman.onprogress', compact('pengiriman'));
    }

    public function selesai(Request $request)
    {
        $user = Auth::user();

        $query = ItemDelivery::with(['request.user', 'request.details.item'])
            ->where('status', 'completed')
            ->where('staff_pengiriman', $user->nama);

        if ($request->tanggal) {
            $query->whereDate('tanggal_kirim', $request->tanggal);
        }

        $pengiriman = $query->latest()->get();

        return view('staff-pengiriman.selesai', compact('pengiriman'));
    }
}

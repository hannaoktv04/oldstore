<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
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
            ->where('staff_pengiriman', $user->id)
            ->latest()
            ->get();

        return view('peri::staff-pengiriman.dashboard', compact('user', 'pengiriman'));
    }

    public function show($kodeResi)
    {
        $id = (int) str_replace('KP', '', $kodeResi);
        $pengajuan = ItemRequest::with(['user', 'details.item', 'itemDelivery'])->findOrFail($id);

        $delivery = $pengajuan->itemDelivery;

        if (!$delivery) {
            $delivery = ItemDelivery::create([
                'item_request_id' => $pengajuan->id,
                'operator_id' => auth()->id(),
                'tanggal_kirim' => now(),
                'staff_pengiriman' => auth()->user()->id,
                'status' => 'in_progress',
            ]);
        } else {
            if ($delivery->staff_pengiriman) {
                if ($delivery->staff_pengiriman !== auth()->user()->id) {
                    return redirect()->route('staff-pengiriman.dashboard')
                        ->with('error', 'Pengiriman sudah diassign ke staff lain: ' . $delivery->staff_pengiriman);
                }
            } else {
                $delivery->update([
                    'staff_pengiriman' => auth()->user()->id,
                    'operator_id' => auth()->id(),
                    'tanggal_kirim' => now(),
                    'status' => 'in_progress',
                ]);
            }
        }

        return view('peri::staff-pengiriman.konfirmasi', compact('pengajuan', 'kodeResi'));
    }

    public function submit(Request $request, $kodeResi)
    {
        $id = (int) str_replace('KP', '', $kodeResi);
        $pengajuan = ItemRequest::with('itemDelivery')->findOrFail($id);

        $request->validate([
            'bukti_foto' => 'required|image|max:5120',
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
            ->where('staff_pengiriman', $user->id)
            ->whereNotNull('tanggal_kirim')
            ->latest()
            ->get();

        return view('peri::staff-pengiriman.onprogress', compact('pengiriman'));
    }

    public function waiting()
    {
        $user = Auth::user();

        $pengiriman = ItemRequest::with(['user', 'details.item', 'itemDelivery'])
            ->where('status', 'approved')
            ->whereDoesntHave('itemDelivery', function ($q) {
                $q->whereNotNull('staff_pengiriman');
            })
            ->latest()
            ->get();

        return view('peri::staff-pengiriman.waiting', compact('pengiriman'));
    }

    public function assignToMe($id)
    {
        $user = Auth::user();

        $pengajuan = ItemRequest::with('itemDelivery')->findOrFail($id);

        if ($pengajuan->itemDelivery && $pengajuan->itemDelivery->staff_pengiriman) {
            return back()->with('error', 'Pengiriman sudah diassign ke staff lain.');
        }

        $delivery = ItemDelivery::updateOrCreate(
            ['item_request_id' => $pengajuan->id],
            [
                'staff_pengiriman' => $user->id,
                'operator_id' => $user->id,
                'status' => 'in_progress',
                'tanggal_kirim' => now(),
            ]
        );

        $pengajuan->update([
            'status' => 'delivered',
            'tanggal_pengiriman' => now(),
        ]);

        return redirect()->route('staff-pengiriman.onprogress')->with('success', 'Pengiriman berhasil diambil oleh Anda.');
    }


    public function selesai(Request $request)
    {
        $user = Auth::user();

        $query = ItemDelivery::with(['request.user', 'request.details.item'])
            ->where('status', 'completed')
            ->where('staff_pengiriman', $user->id);

        if ($request->tanggal) {
            $query->whereDate('tanggal_kirim', $request->tanggal);
        }

        $pengiriman = $query->latest()->get();

        return view('peri::staff-pengiriman.selesai', compact('pengiriman'));
    }
}

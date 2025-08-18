<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StockNotification;
use Carbon\Carbon;

class NotifikasiController extends Controller
{
    public function getNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $notifikasi = StockNotification::where('seen', false)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($notif) {
                    return [
                        'judul' => $notif->judul ?? 'Notifikasi Stok',
                        'pesan' => $notif->pesan ?? 'Ada update pada stok barang.',
                        'waktu' => Carbon::parse($notif->created_at)->diffForHumans(),
                        'read'  => $notif->seen,
                        'url'   => $notif->url ?? null,
                    ];
                });

            return response()->json([
                'jumlah' => $notifikasi->count(),
                'data' => $notifikasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead($id = null)
    {
        try {
            if ($id) {
                StockNotification::where('id', $id)->update(['seen' => true]);
            } else {
                StockNotification::where('seen', false)->update(['seen' => true]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

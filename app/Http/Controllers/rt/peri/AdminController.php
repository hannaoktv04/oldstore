<?php
namespace App\Http\Controllers\rt\peri;
use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\User;
use App\Models\ItemLog;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\ItemDelivery;
use App\Models\ItemWishlist;
use App\Models\StockNotification;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $pengajuanBaru = ItemRequest::where('status', 'submitted')->count();
        $perluDikirim = ItemRequest::where('status', 'approved')->count();
        $sedangDikirim = ItemRequest::where('status', 'delivered')->count(); 
        $pengajuanSelesai = ItemRequest::where('status', 'received')->count();
        $pembatalan = ItemRequest::where('status', 'rejected')->count();

        $totalBarang = Item::count();        
        $stokKritis = Item::whereHas('stocks', function ($query) {
            $query->where('qty', '>', 0)
                ->whereColumn('item_stocks.qty', '<=', 'items.stok_minimum');
        })->count();

        $stokHabis = Item::whereHas('stocks', function ($query) {
            $query->where('qty', '=', 0);
        })->count();

        $stokKritisItems = Item::whereHas('stocks', function ($query) {
            $query->where('qty', '>', 0)
                ->whereColumn('item_stocks.qty', '<=', 'items.stok_minimum');
        })->get();

        foreach ($stokKritisItems as $item) {
            $exists = StockNotification::where('item_id', $item->id)
                ->where('seen', false)
                ->where('judul', 'Stok Menipis')
                ->exists();

            if (!$exists) {
                StockNotification::create([
                    'item_id' => $item->id,
                    'judul' => 'Stok Menipis',
                    'pesan' => "Stok \"{$item->nama_barang}\" menipis.",
                    'url' => route('admin.items.show', $item->id),
                ]);
            }
        }

        $stokHabisItems = Item::whereHas('stocks', fn ($q) => $q->where('qty', '=', 0))->get();

        foreach ($stokHabisItems as $item) {
            $exists = StockNotification::where('item_id', $item->id)
                ->where('seen', false)
                ->where('judul', 'Stok Habis')
                ->exists();

            if (!$exists) {
                StockNotification::create([
                    'item_id' => $item->id,
                    'judul' => 'Stok Habis',
                    'pesan' => "Stok \"{$item->nama_barang}\" telah habis.",
                    'url' => route('admin.items.show', $item->id),
                ]);
            }
        }

        if ($pengajuanBaru > 0) {
            $exists = StockNotification::where('judul', 'Pengajuan Baru')
                ->where('seen', false)
                ->exists();

            if (!$exists) {
                StockNotification::create([
                    'item_id' => null,
                    'judul' => 'Pengajuan Baru',
                    'pesan' => "Ada {$pengajuanBaru} pengajuan barang baru yang perlu ditinjau.",
                    'url' => route('admin.pengajuan.status', ['status' => 'submitted']),
                ]);
            }
        }

        $inventarisFilter = $request->input('inventaris', 'all');

        $produkQuery = Item::with(['category', 'photo', 'stocks']);
        switch ($inventarisFilter) {
            case 'critical':
                $produkQuery->whereHas('stocks', function ($query) {
                    $query->where('qty', '>', 0)
                        ->whereColumn('item_stocks.qty', '<=', 'items.stok_minimum');
                });
                break;
            case 'out_of_stock':
                $produkQuery->whereHas('stocks', function ($query) {
                    $query->where('qty', '=', 0);
                });
                break;
            default:
                $produkQuery->orderBy('created_at', 'desc');
                break;
        }

        $produkToShow = $produkQuery->take(5)->get();

        $produkStokHabis = Item::with(['category', 'photo'])
            ->whereHas('stocks', fn ($q) => $q->where('qty', '=', 0))
            ->orderBy('nama_barang')->take(5)->get();

        $produkStokKritis = Item::with(['category', 'photo'])
            ->whereHas('stocks', fn ($q) => $q->where('qty', '>', 0)
                ->whereColumn('item_stocks.qty', '<=', 'items.stok_minimum'))
            ->orderBy('nama_barang')->take(5)->get();

        $produkTerbaru = Item::with(['category', 'photo'])
            ->orderBy('created_at', 'desc')->take(5)->get();


        $tahunDipilih = $request->input('tahun', date('Y'));
        $bulanDipilih = $request->input('bulan', 'all');

        $bulanList = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
        ];

        $barangKeluarPerBulan = ItemLog::where('tipe', 'out')
            ->whereYear('created_at', $tahunDipilih)
            ->selectRaw("EXTRACT(MONTH FROM created_at)::int AS bulan, SUM(qty) AS total")
            ->groupByRaw("EXTRACT(MONTH FROM created_at)::int")
            ->orderBy('bulan')
            ->pluck('total', 'bulan')   
            ->toArray();

        $grafikBulanan = [];
        foreach (range(1, 12) as $i) {
            $grafikBulanan[] = $barangKeluarPerBulan[$i] ?? 0;
        }


        $topProduk = ItemRequestDetail::whereYear('created_at', $tahunDipilih)
            ->when($bulanDipilih != 'all', fn ($q) => $q->whereMonth('created_at', $bulanDipilih))
            ->selectRaw('item_id, SUM(qty_requested) as total')
            ->groupBy('item_id')->orderByDesc('total')
            ->with('item.category', 'item.photo')->take(5)->get();

        $topWishlist = ItemWishlist::whereYear('created_at', $tahunDipilih)
            ->when($bulanDipilih != 'all', fn ($q) => $q->whereMonth('created_at', $bulanDipilih))
            ->selectRaw('nama_barang, category_id, COUNT(*) as total')
            ->groupBy('nama_barang', 'category_id')->orderByDesc('total')
            ->with('category')->take(5)->get();

        $topStaffPengiriman = ItemDelivery::select('staff_pengiriman', DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $tahunDipilih)
            ->when($bulanDipilih != 'all', fn ($q) => $q->whereMonth('created_at', $bulanDipilih))
            ->whereNotNull('staff_pengiriman')->groupBy('staff_pengiriman')
            ->orderByDesc('total')->with('staff')->take(5)->get();

        $topUserRequest = ItemRequest::whereYear('created_at', $tahunDipilih)
            ->when($bulanDipilih != 'all', fn ($q) => $q->whereMonth('created_at', $bulanDipilih))
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')->orderByDesc('total')
            ->with('user')->take(5)->get();

        $notifikasi = [];
        if ($stokKritis > 0) $notifikasi[] = "Ada {$stokKritis} barang dengan stok menipis.";
        if ($perluDikirim > 0) $notifikasi[] = "{$perluDikirim} pengajuan perlu dikirim.";
        if ($stokHabis > 0) $notifikasi[] = "Ada {$stokHabis} barang yang stoknya habis.";

        return view('peri::admin.dashboard.index', compact(
            'pengajuanBaru',
            'perluDikirim',
            'pengajuanSelesai',
            'sedangDikirim',
            'pembatalan',
            'totalBarang',
            'stokKritis',
            'stokHabis',
            'produkStokHabis',
            'produkStokKritis',
            'produkTerbaru',
            'produkToShow',
            'inventarisFilter',
            'tahunDipilih',
            'bulanDipilih',
            'grafikBulanan',
            'bulanList',
            'topProduk',
            'topWishlist',
            'topStaffPengiriman',
            'topUserRequest',
            'notifikasi'
        ));
    }

    public function pengajuanByStatus($status)
    {
        $pengajuans = ItemRequest::with([
            'user',
            'details.item.photo',
            'details.item.category',
            'itemDelivery.staff'
        ])->where('status', $status);

        if ($status === 'submitted') {
            $pengajuans->orderBy('created_at', 'asc');
        } elseif ($status === 'approved') {
            $pengajuans->orderBy('tanggal_pengiriman', 'asc')->orderBy('id', 'asc');
        } else {
            $pengajuans->orderBy('created_at', 'desc');
        }

        if ($status === 'approved') {
            $pengajuans->where(function ($q) {
                $q->doesntHave('itemDelivery')
                    ->orWhereHas('itemDelivery', function ($q) {
                        $q->whereNull('staff_pengiriman');
                    });
            });
        } elseif ($status === 'delivered') {
            $pengajuans->where('status', 'delivered')
                ->whereHas('itemDelivery', fn ($q) =>
                    $q->whereNotNull('staff_pengiriman')->where('status', 'in_progress'));
        }

        $pengajuans = $pengajuans->get();

        $staff_pengiriman = User::whereHas('roles', fn ($q) =>
            $q->where('nama_role', 'staff_pengiriman'))->get();

        return view('peri::admin.pengajuan.index', compact('pengajuans', 'status', 'staff_pengiriman'));
    }
}

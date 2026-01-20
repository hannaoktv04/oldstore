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
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $pengajuanBaru = ItemRequest::where('status', 'submitted')->count() 
                   + Order::where('payment_status', 'pending')->count();

        $perluDikirim = ItemRequest::where('status', 'approved')->count() 
                    + Order::where('payment_status', 'success')->count();

        $sedangDikirim = ItemRequest::where('status', 'delivered')->count(); 

        $pengajuanSelesai = ItemRequest::where('status', 'received')->count();

        $pembatalan = ItemRequest::where('status', 'rejected')->count();
        $totalBarang = Item::count();
        $stokKritis = Item::where('stok', '<=', 5)->count();
        $pesananMasuk = Order::where('payment_status', 'success')->count(); // Total pesanan lunas

        // Total Pendapatan Bulan Ini dari Midtrans
        $totalPendapatan = Order::where('payment_status', 'success')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total_amount');

        $totalBarang = Item::count();

        // stok kritis & habis (stok kritis <= 5)
        $stokKritis = Item::where('stok', '<=', 5)->count();
        $stokHabis = Item::where('stok', '=', 0)->count();

        $stokKritisItems = Item::where('stok', '<=', 5)->get();

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

        $stokHabisItems = Item::where('stok', '=', 0)->get();

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

        $produkQuery = Item::with(['category', 'photo']);
        switch ($inventarisFilter) {
            case 'critical':
                $produkQuery->where('stok', '<=', 5);
                break;
            case 'out_of_stock':
                $produkQuery->where('stok', '=', 0);
                break;
            default:
                $produkQuery->orderBy('created_at', 'desc');
                break;
        }

        $produkToShow = $produkQuery->take(5)->get();

        $produkStokHabis = Item::with(['category', 'photo'])
            ->where('stok', '=', 0)
            ->orderBy('nama_barang')->take(5)->get();

        $produkStokKritis = Item::with(['category', 'photo'])
            ->where('stok', '<=', 5)
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

        $topProduk = DB::table('order_items as oi')
            ->select(
                'oi.item_id',
                DB::raw('SUM(oi.quantity) as total')
            )
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('o.payment_status', 'success') // hanya transaksi berhasil
            ->whereYear('o.created_at', $tahunDipilih)
            ->when($bulanDipilih != 'all', fn ($q) =>
                $q->whereMonth('o.created_at', $bulanDipilih)
            )
            ->groupBy('oi.item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

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
            'totalPendapatan',
            'pesananMasuk',
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

    private function getStats()
    {
        return [
            'pengajuanBaru' => ItemRequest::where('status', 'submitted')->count() + Order::where('payment_status', 'pending')->count(),
            'perluDikirim' => ItemRequest::where('status', 'approved')->count() + Order::where('payment_status', 'success')->count(),
            'sedangDikirim' => ItemRequest::where('status', 'delivered')->count(), 
            'pengajuanSelesai' => ItemRequest::where('status', 'received')->count(),
            'pesananMasuk' => Order::where('payment_status', 'success')->count(),
            'totalPendapatan' => Order::where('payment_status', 'success')->whereMonth('created_at', date('m'))->sum('total_amount'),
            'stokKritis' => Item::where('stok', '<=', 5)->count(),
        ];
    }

    public function pengajuanByStatus($status)
    {
        $stats = $this->getStats();
        $pengajuans = collect(); // Default koleksi kosong

        if ($status === 'submitted') {
            // Gabungkan Pengajuan Manual 'submitted' & Order Midtrans 'pending'
            $manual = ItemRequest::with(['user', 'details.item'])->where('status', 'submitted')->get();
            $orders = Order::with(['user', 'items.item'])->where('payment_status', 'pending')->get();
            $pengajuans = $manual->concat($orders);
        } 
        elseif ($status === 'approved') {
            // Gabungkan Pengajuan Manual 'approved' & Order Midtrans 'success'
            $manual = ItemRequest::with(['user', 'details.item'])->where('status', 'approved')->get();
            $orders = Order::with(['user', 'items.item'])->where('payment_status', 'success')->get();
            $pengajuans = $manual->concat($orders);
        } 
        elseif ($status === 'delivered') {
            // Data yang sedang dalam perjalanan (sudah ada resi)
            $pengajuans = ItemRequest::with(['user', 'details.item', 'itemDelivery.staff'])->where('status', 'delivered')->get();
        } 
        elseif ($status === 'received') {
            // Data yang sudah dikonfirmasi sampai oleh user
            $pengajuans = ItemRequest::with(['user', 'details.item', 'itemDelivery.staff'])->where('status', 'received')->get();
        }

        $staff_pengiriman = User::all(); // Mengambil semua user untuk staff

        return view('peri::admin.pengajuan.index', array_merge($stats, [
            'pengajuans' => $pengajuans,
            'status' => $status,
            'staff_pengiriman' => $staff_pengiriman
        ]));
    }
}

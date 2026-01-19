<?php

namespace App\Http\Controllers\rt\peri;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Carbon\Carbon;
// Import SDK Midtrans
use Midtrans\Config;
use Midtrans\Snap;

class CartController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        $carts = Cart::with(['item.images', 'item.photo', 'item.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->sortBy(fn($cart) => $cart->item->stok_minimum <= 0 ? 1 : 0);

        $jumlahKeranjang = $carts->count();
        $totalHarga = $carts->sum(fn($cart) => $cart->qty * ($cart->item->harga ?? 0));

        return view('peri::cart.index', compact('carts', 'jumlahKeranjang', 'totalHarga'));
    }

    public function store(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty' => "required|numeric|min:1|max:{$item->stok}",
        ]);

        $cart = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
        ]);

        $cart->qty += $request->qty;
        $cart->save();

        return redirect()->route('cart.index')->with('success', 'Barang berhasil ditambahkan ke keranjang.');
    }

    public function checkoutPage()
    {
        // Bersihkan order expired sebelum halaman dimuat
        $this->autoCancelExpiredOrders();

        $carts = Cart::with('item')
            ->where('user_id', Auth::id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $subtotal = $carts->sum(fn ($c) => $c->qty * ($c->item->harga ?? 0));

        return view('peri::cart.checkout', compact('carts', 'subtotal'));
    }

    public function checkout(Request $request)
    {
        // Validasi data pengiriman dari form
        $request->validate([
            'cart_ids'      => 'required',
            'ongkir'        => 'required|numeric',
            'province_code' => 'required',
            'city_code'     => 'required',
            'district_code' => 'required',
            'village_code'  => 'required',
            'full_address'  => 'required|string',
            'postal_code'   => 'required',
            'courier'       => 'required',
            'weight'        => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $ids = explode(',', $request->cart_ids);
            $carts = Cart::with('item')->whereIn('id', $ids)->get();

            // 1. Cek ketersediaan stok sebelum eksekusi
            foreach ($carts as $cart) {
                if ($cart->item->stok < $cart->qty) {
                    throw new \Exception("Stok barang '{$cart->item->nama_barang}' tidak mencukupi.");
                }
            }

            $subtotal = $carts->sum(fn($c) => $c->qty * ($c->item->harga ?? 0));
            $totalAmount = $subtotal + $request->ongkir;

            // 2. Buat Data Order
            $order = Order::create([
                'order_number'   => 'ORD-' . strtoupper(Str::random(10)),
                'user_id'        => Auth::id(),
                'payment_status' => 'pending',
                'province_code'  => $request->province_code,
                'city_code'      => $request->city_code,
                'district_code'  => $request->district_code,
                'village_code'   => $request->village_code,
                'full_address'   => $request->full_address,
                'postal_code'    => $request->postal_code,
                'courier'        => $request->courier,
                'weight'         => $request->weight,
                'shipping_cost'  => $request->ongkir,
                'subtotal'       => $subtotal,
                'total_amount'   => $totalAmount,
            ]);

            // 3. Simpan Detail & Potong Stok
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id'  => $cart->item_id,
                    'quantity' => $cart->qty,
                    'price'    => $cart->item->harga,
                ]);

                // Kurangi stok barang
                $cart->item->decrement('stok', $cart->qty);
                // Hapus dari keranjang
                $cart->delete();
            }

            // 4. Integrasi Midtrans
            $params = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email'      => Auth::user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            DB::commit();
            return redirect()->route('cart.invoice', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function autoCancelExpiredOrders()
    {
        // Cari order pending yang sudah lebih dari 24 jam
        $expiredOrders = Order::where('payment_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDay())
            ->with('items')
            ->get();

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // Kembalikan stok untuk setiap item di dalam order
                foreach ($order->items as $detail) {
                    Item::where('id', $detail->item_id)->increment('stok', $detail->quantity);
                }
                // Ubah status menjadi expired
                $order->update(['payment_status' => 'expired']);
            });
        }
    }

    public function invoice($id)
    {
        $this->autoCancelExpiredOrders();
        $order = Order::with(['items.item', 'user'])->where('user_id', Auth::id())->findOrFail($id);
        return view('peri::cart.invoice', compact('order'));
    }

    // --- API REGIONAL & ONGKIR ---
    
    public function calcOngkir(Request $request)
    {
        $request->validate([
            'destination_village_code' => 'required|size:10',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required', 
        ]);

        try {
            $response = Http::withHeaders(['x-api-co-id' => env('API_CO_ID_KEY')])
                ->get('https://use.api.co.id/expedition/shipping-cost', [
                    'origin_village_code'      => '3172051003',
                    'destination_village_code' => $request->destination_village_code,
                    'weight'                   => ceil($request->weight / 1000), 
                ]);

            if (!$response->successful()) return response()->json(['is_success' => false, 'message' => 'Gagal akses API'], 500);

            $api = $response->json();
            $selectedCourier = strtolower($request->courier);
            $price = 0; $name = '';

            if (isset($api['data']['couriers'])) {
                foreach ($api['data']['couriers'] as $service) {
                    if (str_contains(strtolower($service['courier_code']), $selectedCourier)) {
                        $price = (int) $service['price'];
                        $name = $service['courier_name'];
                        break; 
                    }
                }
            }

            return response()->json([
                'is_success' => $price > 0,
                'data' => ['price' => $price, 'courier' => $name],
                'message' => $price > 0 ? '' : 'Kurir tidak tersedia'
            ]);
        } catch (\Exception $e) {
            return response()->json(['is_success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    protected function apiClient()
    {
        return new Client(['headers' => ['accept' => 'application/json', 'x-api-co-id' => env('API_CO_ID_KEY')]]);
    }

    public function getProvinces() {
        $res = $this->apiClient()->get('https://use.api.co.id/regional/indonesia/provinces');
        return response()->json(json_decode($res->getBody(), true));
    }

    public function getCities($province_code) {
        $res = $this->apiClient()->get('https://use.api.co.id/regional/indonesia/regencies', ['query' => ['province_code' => $province_code]]);
        return response()->json(json_decode($res->getBody(), true));
    }

    public function getDistricts($city_code) {
        $res = $this->apiClient()->get('https://use.api.co.id/regional/indonesia/districts', ['query' => ['regency_code' => $city_code]]);
        return response()->json(json_decode($res->getBody(), true));
    }

    public function getVillages($district_code) {
        $res = $this->apiClient()->get('https://use.api.co.id/regional/indonesia/villages', ['query' => ['district_code' => $district_code]]);
        return response()->json(json_decode($res->getBody(), true));
    }

    public function destroy($id)
    {
        Cart::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Item dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = explode(',', $request->cart_ids);
        Cart::whereIn('id', $ids)->where('user_id', Auth::id())->delete();
        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus.');
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        // 1. Verifikasi Signature Key untuk keamanan
        $validSignature = hash("sha512", 
            $notification->order_id . 
            $notification->status_code . 
            $notification->gross_amount . 
            env('MIDTRANS_SERVER_KEY')
        );

        if ($notification->signature_key !== $validSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari data order berdasarkan order_number
        $order = Order::where('order_number', $notification->order_id)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;

        // 3. Update status berdasarkan respon Midtrans
        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $order->update(['payment_status' => 'challenge']);
                } else {
                    $order->update(['payment_status' => 'success']);
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update(['payment_status' => 'success']);
        } elseif (in_array($transactionStatus, ['pending'])) {
            $order->update(['payment_status' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            
            // Kembalikan stok jika pembayaran gagal/expired
            if ($order->payment_status !== 'expired') {
                DB::transaction(function () use ($order) {
                    foreach ($order->items as $item) {
                        Item::where('id', $item->item_id)->increment('stok', $item->quantity);
                    }
                    $order->update(['payment_status' => 'expired']);
                });
            }
        }

        return response()->json(['message' => 'Notification handled']);
    }
}
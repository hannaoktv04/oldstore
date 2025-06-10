<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;


class CartController extends Controller
{
    public function index()
    {
        // Ambil data cart user yang sedang login, beserta relasi item
        $carts = Cart::with('item')->where('user_id', Auth::id())->get();
        return view('cart.index', compact('carts'));
    }

    public function store(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'qty' => "required|numeric|min:1|max:{$item->stok_minimum}",
        ]);

        // Cari cart existing
        $cart = Cart::where('user_id', Auth::id())
                    ->where('item_id', $request->item_id)
                    ->first();

        if ($cart) {
            // Kalau sudah ada, update qty dengan menambah jumlah baru
            $cart->qty += $request->qty;
            $cart->save();
        } else {
            // Kalau belum ada, buat baru dengan qty sesuai request
            Cart::create([
                'user_id' => Auth::id(),
                'item_id' => $request->item_id,
                'qty' => $request->qty,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Barang berhasil ditambahkan ke keranjang.');
    }

    public function destroy($id)
    {
        // Hapus item cart sesuai id dan user yang login
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cart->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);

        if ($request->action === 'increase') {
            $cart->qty += 1;
        } elseif ($request->action === 'decrease') {
            if ($cart->qty > 1) {
                $cart->qty -= 1;
            }
        }

        $cart->save();

        return redirect()->route('cart.index')->with('success', 'Jumlah diperbarui');
    }

    public function checkout()
    {
        $user = auth()->user();
        $carts = $user->carts()->with('item')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong.');
        }

        DB::beginTransaction();

        try {
            // Buat header permintaan
            $itemRequest = ItemRequest::create([
                'user_id' => $user->id,
                'status' => 'submitted', // status awal langsung diajukan
                'tanggal_permintaan' => now(),
                'keterangan' => null, // Bisa diubah jika kamu tambahkan input di form
            ]);

            // Simpan semua item dari cart ke item_request_details
            foreach ($carts as $cart) {
                ItemRequestDetail::create([
                    'item_request_id' => $itemRequest->id,
                    'item_id' => $cart->item_id,
                    'qty_requested' => $cart->qty,
                    'qty_approved' => null,
                ]);
            }

            // Hapus isi keranjang
            $user->carts()->delete();

            DB::commit();
            return redirect()->route('cart.index')->with('success', 'Permintaan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('error', 'Gagal mengajukan permintaan.');
        }
    }

}

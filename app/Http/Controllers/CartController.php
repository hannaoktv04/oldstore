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

        $cart = Cart::where('user_id', Auth::id())
                    ->where('item_id', $request->item_id)
                    ->first();

        if ($cart) {
            $cart->qty += $request->qty;
            $cart->save();
        } else {
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
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cart->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function update(Request $request, $id)
    {
    $cart = Cart::with('item')->findOrFail($id);

        $stokTersedia = $cart->item->stok_minimum;

        if ($request->action === 'increase') {
            if ($cart->qty < $stokTersedia) {
                $cart->qty += 1;
            } else {
                return redirect()->route('cart.index');
            }
        } elseif ($request->action === 'decrease') {
            if ($cart->qty > 1) {
                $cart->qty -= 1;
            }
        } elseif ($request->filled('qty')) {
            $qtyBaru = intval($request->qty);
            if ($qtyBaru < 1) {
                $qtyBaru = 1;
            } elseif ($qtyBaru > $stokTersedia) {
                return redirect()->route('cart.index')->with('error', 'Jumlah melebihi stok tersedia.');
            }
            $cart->qty = $qtyBaru;
        }

        $cart->save();

        return redirect()->route('cart.index')->with('success', 'Jumlah diperbarui.');

    if (!$cart->item) {
        return redirect()->route('cart.index')->with('error', 'Item tidak ditemukan.');
    }

    $stokTersedia = $cart->item->stok_minimum;

    if ($request->action === 'increase') {
        if ($cart->qty < $stokTersedia) {
            $cart->qty += 1;
        } else {
            return redirect()->route('cart.index')->with('error', 'Jumlah melebihi stok tersedia.');
        }

    } elseif ($request->action === 'decrease') {
        if ($cart->qty > 1) {
            $cart->qty -= 1;
        }

    } elseif ($request->filled('qty')) {
        $qtyBaru = intval($request->qty);

        if ($qtyBaru < 1) {
            $qtyBaru = 1;
        } elseif ($qtyBaru > $stokTersedia) {
            return redirect()->route('cart.index')->with('error', 'Jumlah melebihi stok tersedia.');
        }

        $cart->qty = $qtyBaru;
    }

    $cart->save();

    return redirect()->route('cart.index')->with('success', 'Jumlah berhasil diperbarui.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'tanggal_pengambilan' => 'required|date|after_or_equal:' . now()->toDateString(),
            'cart_ids' => 'required|array|min:1',
            'cart_ids.*' => 'exists:carts,id',
        ]);

        $user = auth()->user();
        $cartIds = $request->cart_ids;

        $carts = Cart::with('item')
            ->where('user_id', $user->id)
            ->whereIn('id', $cartIds)
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada item yang dipilih.');
        }

        DB::beginTransaction();

        try {
            $itemRequest = ItemRequest::create([
                'user_id' => $user->id,
                'status' => 'submitted',
                'status' => 'submitted',
                'tanggal_permintaan' => now(),
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => null,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => null,
            ]);

            foreach ($carts as $cart) {
                ItemRequestDetail::create([
                    'item_request_id' => $itemRequest->id,
                    'item_id' => $cart->item_id,
                    'qty_requested' => $cart->qty,
                    'qty_approved' => null,
                ]);
            }

            Cart::whereIn('id', $cartIds)->delete();

            DB::commit();
            return redirect()->route('user.history')->with('success', 'Permintaan berhasil diajukan dan menunggu konfirmasi admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.history')->with('error', 'Gagal mengajukan permintaan.');
        }
    }



    public function pesanLangsung(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'qty' => "required|numeric|min:1|max:{$item->stok_minimum}",
            'tanggal_pengambilan' => 'required|date|after_or_equal:' . now()->toDateString(),
        ]);

        DB::beginTransaction();

        try {
            $itemRequest = ItemRequest::create([
                'user_id' => Auth::id(),
                'status' => 'submitted',
                'tanggal_permintaan' => now(),
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'keterangan' => null,
            ]);

            ItemRequestDetail::create([
                'item_request_id' => $itemRequest->id,
                'item_id' => $item->id,
                'qty_requested' => $request->qty,
                'qty_approved' => null,
            ]);

            DB::commit();
            return redirect()->route('user.history')->with('success', 'Permintaan langsung berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan permintaan langsung.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = explode(',', $request->cart_ids);

        Cart::whereIn('id', $ids)->where('user_id', Auth::id())->delete();

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus.');
    }


}
<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\ItemStock;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('details.item', 'creator')->latest()->get();
        return view('admin.purchaseOrder.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $items = Item::select('id', 'kode_barang', 'nama_barang', 'satuan')->get();

        $prefix = 'PO-' . now()->format('Ym');
        $lastPO = PurchaseOrder::where('nomor_po', 'like', "$prefix%")->latest()->first();
        $counter = $lastPO ? (int) Str::afterLast($lastPO->nomor_po, '-') + 1 : 1;
        $nomor_po = $prefix . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);

        return view('admin.purchaseOrder.createPO', compact('items', 'nomor_po'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_po' => 'required|date',
            'item_id' => 'required|array',
            'qty' => 'required|array',
            'qty.*' => 'numeric|min:0.01',
        ]);
        $prefix = 'PO-' . now()->format('Ym');
        $lastPO = PurchaseOrder::where('nomor_po', 'like', "$prefix%")->latest()->first();
        $counter = $lastPO ? (int) Str::afterLast($lastPO->nomor_po, '-') + 1 : 1;
        $nomor_po = $prefix . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);

        $po = PurchaseOrder::create([
            'tanggal_po' => $request->tanggal_po,
            'status' => 'draft',
            'created_by' => Auth::id(),
            'nomor_po' => $nomor_po,
        ]);

        foreach ($request->item_id as $index => $itemId) {
            PurchaseOrderDetail::create([
                'purchase_order_id' => $po->id,
                'item_id' => $itemId,
                'qty' => $request->qty[$index],
            ]);
        }

        return redirect()->route('admin.purchase_orders.index')->with('success', 'Purchase Order created successfully.');
    }
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('details.item');
        return view('admin.purchaseOrder.showPO', compact('purchaseOrder'));
    }
    public function submit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft orders can be submitted.');
        }
        $purchaseOrder->update(['status' => 'submitted']);

        return redirect()->back()->with('success', 'Purchase Order has been successfully submitted.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->details()->delete();
        $purchaseOrder->delete();
        return redirect()->route('admin.purchase_orders.index')->with('success', 'Purchase Order dan semua detailnya berhasil dihapus.');
    }
    public function receiveForm(PurchaseOrder $purchaseOrder)
    {
        $details = $purchaseOrder->details()->with('item')->get();
        return view('admin.purchaseOrder.receive', compact('purchaseOrder', 'details'));
    }
    public function processReceive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $data = $request->input('details');

        foreach ($data as $detailId => $newQty) {
            $detail = PurchaseOrderDetail::findOrFail($detailId);

            if ($detail->qty != $newQty) {
                $detail->qty = $newQty;
                $detail->save();
            }

            ItemLog::create([
                'item_id'   => $detail->item_id,
                'tipe'      => 'in',
                'qty'       => $newQty,
                'sumber'    => 'po',
                'sumber_id' => $purchaseOrder->id,
                'deskripsi' => 'Barang diterima dari PO #' . $purchaseOrder->nomor_po,
            ]);

            $itemStock = ItemStock::firstOrNew(['item_id' => $detail->item_id]);
            $itemStock->qty = ($itemStock->qty ?? 0) + $newQty;
            $itemStock->save();

            $item = Item::find($detail->item_id);
            if ($item) {
                $item->stok_minimum += $newQty;
                $item->save();
            }
        }

        $purchaseOrder->status = 'received';
        $purchaseOrder->save();

        return redirect()->route('admin.purchase_orders.index')->with('success', 'Barang berhasil diterima dan stok diperbarui.');
    }


    public function edit(PurchaseOrder $purchaseOrder)
    {
        $items = Item::select('id', 'kode_barang', 'nama_barang', 'satuan')->get();
        $purchaseOrder->load('details.item');
        $existingItems = $purchaseOrder->details;

        return view('admin.purchaseOrder.editPO', compact('purchaseOrder', 'items', 'existingItems'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'tanggal_po' => 'required|date',
            'item_id' => 'required|array',
            'qty' => 'required|array',
            'qty.*' => 'numeric|min:0.01',
        ]);

        $purchaseOrder->update([
            'tanggal_po' => $request->tanggal_po,
        ]);
        $purchaseOrder->details()->delete();

        foreach ($request->item_id as $index => $itemId) {
            PurchaseOrderDetail::create([
                'purchase_order_id' => $purchaseOrder->id,
                'item_id' => $itemId,
                'qty' => $request->qty[$index],
            ]);
        }

        return redirect()->route('admin.purchase_orders.index')
            ->with('success', 'Purchase Order updated successfully');
    }
}
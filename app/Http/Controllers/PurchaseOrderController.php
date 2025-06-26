<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('details.item', 'creator')->latest()->get();
        return view('admin.purchaseOrder.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $items = Item::all();

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
}

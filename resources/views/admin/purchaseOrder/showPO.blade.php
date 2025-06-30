@extends('layouts.admin')

@section('title', 'Detail Purchase Order')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Purchase Order Details - {{ $purchaseOrder->nomor_po }}</h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="fw-semibold">Kode PO</label>
                    <div>{{ $purchaseOrder->nomor_po }}</div>
                </div>
                <div class="col-md-6">
                    <label class="fw-semibold">Tanggal PO</label>
                    <div>{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</div>
                </div>
            </div>

            <h5 class="mt-3">Orders</h5>
            <table class="table table-striped table-bordered">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="col-md-2">Kode Item</th>
                        <th class="col-md-5 text-center">Item</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach ($purchaseOrder->details as $detail)
                    @php $totalQty += $detail->qty; @endphp
                    <tr>
                        <td class="text-center">{{ $detail->item->kode_barang ?? '-' }}</td>
                        <td>{{ $detail->item->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $detail->item->satuan ?? '-' }}</td>
                        <td class="text-center">{{ number_format($detail->qty) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-end" colspan="3">Total Qty</th>
                        <th class="text-center">{{ number_format($totalQty) }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3">
                <label>Status</label> :
                <strong>
                    @switch($purchaseOrder->status)
                    @case('draft')
                    Draft
                    @break

                    @case('submitted')
                    Submitted
                    @break

                    @case('received')
                    Received
                    @break

                    @defaulti
                    N/A
                    @endswitch
                </strong>
            </div>
            <div class="text-start mt-4 mb-2  ">
                <button class="btn btn-flat btn-success" id="print">Print</button>
                <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-flat btn-dark">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Detail Purchase Order')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Purchase Order Details - {{ $purchaseOrder->nomor_po }}</h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="text-info">P.O. Code</label>
                    <div>{{ $purchaseOrder->nomor_po }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-info">Tanggal</label>
                    <div>{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</div>
                </div>
            </div>

            <h5 class="text-info">Orders</h5>
            <table class="table table-striped table-bordered">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Item</th>
                        <th class="text-center">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach ($purchaseOrder->details as $detail)
                        @php $totalQty += $detail->qty; @endphp
                        <tr>
                            <td class="text-center">{{ number_format($detail->qty, 2) }}</td>
                            <td>
                                {{ $detail->item->nama_barang }}
                            </td>
                            <td class="text-center">
                                {{ $detail->item->satuan }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-end" colspan="2">Total Qty</th>
                        <th class="text-center">{{ number_format($totalQty, 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-3">
                <label class="text-info">Status</label>:
                <strong>
                    @switch($purchaseOrder->status)
                        @case('draft') Draft @break
                        @case('submitted') Submitted @break
                        @case('received') Received @break
                        @default N/A
                    @endswitch
                </strong>
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        <button class="btn btn-flat btn-success" id="print">Print</button>
        <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-flat btn-dark">Back to List</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('print').addEventListener('click', function () {
        const printArea = document.getElementById('print_out').innerHTML;
        const newWin = window.open('', '', 'width=900,height=800');
        newWin.document.write(`
            <html>
                <head>
                    <title>Purchase Order</title>
                    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
                </head>
                <body>
                    <h3 class="text-center">Purchase Order</h3>
                    ${printArea}
                </body>
            </html>
        `);
        newWin.document.close();
        setTimeout(() => {
            newWin.print();
            newWin.close();
        }, 300);
    });
</script>
@endpush

@extends('peri::layouts.admin')

@section('title', 'Receive PO')

@section('content')
<div class="card px-2 py-3">
    <div class="card-header">
        <h4 class="card-title">Input Stok Fisik - {{ $purchaseOrder->nomor_po }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase_orders.processReceive', $purchaseOrder->id) }}" method="POST">
            @csrf

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Satuan</th>
                        <th>Qty PO</th>
                        <th>Qty Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                    <tr>
                        <td>{{ $detail->item->nama_barang }}</td>
                        <td>{{ $detail->item->satuan->nama_satuan }}</td>
                        <td>{{ $detail->qty }}</td>
                        <td>
                            <input type="number" name="details[{{ $detail->id }}]" class="form-control"
                                value="{{ $detail->qty }}" min="0" step="1" required>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/peri/crud-po.js') }}"></script>
@endpush

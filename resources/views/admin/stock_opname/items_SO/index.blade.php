@extends('layouts.admin')

@section('title', 'Input Stock Opname')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Input Stock Opname - Periode {{ $session->periode_bulan }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.stock_opname.input', $session->id) }}" method="POST">
                @csrf
                <div class="table-responsive-lg mt-3">
                    <table class="table table-bordered w-100" id="stockOpnameTable">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Stok Sistem</th>
                                <th>Stok Fisik</th>
                                <th>Selisih</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->kode_barang }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td class="text-end">{{ number_format($item->stocks->qty ?? 0) }}</td>
                                    <td>
                                        <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                        <input type="number" name="qty_fisik[]" step="0.01" min="0"
                                            class="form-control qty-fisik" data-sistem="{{ $item->stocks->qty ?? 0 }}">
                                    </td>
                                    <td class="selisih text-end text-muted fw-bold">
                                        0.00
                                        <input type="hidden" name="selisih[]" class="input-selisih" value="0.00">
                                    </td>
                                    <td>
                                        <input type="text" name="catatan[]" class="form-control"
                                            placeholder="Opsional...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan Opname</button>
                    <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#stockOpnameTable').DataTable({
                responsive: true,
            });

            $(document).on('input', '.qty-fisik', function() {
                const fisik = parseFloat($(this).val()) || 0;
                const sistem = parseFloat($(this).data('sistem')) || 0;
                const selisih = (fisik - sistem);

                const tr = $(this).closest('tr');
                const selisihCell = tr.find('.selisih');
                const inputSelisih = selisihCell.find('.input-selisih');

                selisihCell.contents().first()[0].nodeValue = selisih;


                selisihCell.removeClass('text-danger text-success text-muted');
                if (selisih < 0) {
                    selisihCell.addClass('text-danger');
                } else if (selisih > 0) {
                    selisihCell.addClass('text-success');
                } else {
                    selisihCell.addClass('text-muted');
                }

                inputSelisih.val(selisih);
            });
        });
    </script>
@endpush

@extends('peri::layouts.admin')

@section('content')
    <div class="container">
        <div class="py-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.stock_opname.index') }}">Stock Opname</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $session->periode_bulan }}</li>
                </ol>
            </nav>

        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Stock Opname - Periode {{ $session->periode_bulan }}</h4>
                </div>

            </div>
            <div class="card-body">

                <form id="opnameForm" action="{{ route('admin.stock_opname.update', $session->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-4 border-bottom pb-4">
                        <div class="col-md-3 mb-3">
                            <label class="form-label small text-muted">Periode Bulan</label>
                            <input type="text" name="periode_bulan" class="form-control form-control-sm"
                                value="{{ $session->periode_bulan }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label small text-muted">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                value="{{ $session->tanggal_mulai->format('Y-m-d') }}"
                                min="{{ now()->subYears(1)->format('Y-m-d') }}"
                                max="{{ now()->addYear()->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label small text-muted">Admin</label>
                            <div class="form-control-plaintext">
                                {{ $session->opener->nama ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label small text-muted">Status</label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-{{ $session->status === 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label small text-muted">Catatan Sesi</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="2"
                                placeholder="Tambahkan catatan jika diperlukan">{{ $session->catatan }}</textarea>
                        </div>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" id="stockOpnameTable">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Fisik</th>
                                    <th>Selisih</th>
                                    <th>Catatan Item</th>
                                </tr>
                            </thead>
                            <tbody">
                                @foreach ($items as $item)
                                    @php
                                        $stockOpname = $item->stockOpnames->firstWhere('session_id', $session->id);
                                        $qtySistem = $item->stocks->qty ?? 0;
                                        $qtyFisik = $stockOpname->qty_fisik ?? null;
                                        $selisih = is_null($qtyFisik) ? null : $qtyFisik - $qtySistem;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->kode_barang }}</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td class="text-center">{{ $item->satuan->nama_satuan ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($qtySistem) }}</td>
                                        <td>
                                            <input type="hidden" name="items[{{ $item->id }}][item_id]"
                                                value="{{ $item->id }}">
                                            <input type="number" name="items[{{ $item->id }}][qty_fisik]"
                                                step="0.01" min="0"
                                                class="form-control form-control-sm qty-fisik text-end"
                                                value="{{ !is_null($qtyFisik) ? $qtyFisik : '' }}"
                                                data-sistem="{{ $qtySistem }}" placeholder="0">
                                        </td>
                                        <td
                                            class="selisih text-center fw-bold {{ !is_null($selisih) ? ($selisih < 0 ? 'text-danger' : ($selisih > 0 ? 'text-success' : 'text-muted')) : '' }}">
                                            {{ !is_null($selisih) ? number_format($selisih) : '' }}
                                            <input type="hidden" name="items[{{ $item->id }}][selisih]"
                                                class="input-selisih" value="{{ !is_null($selisih) ? $selisih : '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $item->id }}][catatan]"
                                                class="form-control form-control-sm"
                                                value="{{ $stockOpname->catatan ?? '' }}" placeholder="Catatan">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                        </table>
                    </div>

                    <div>
                        <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>

            </div>

        </div>


    </div>

    <form id="endSessionForm" action="{{ route('admin.stock_opname.endSession', $session->id) }}" method="POST"
        class="d-none">
        @csrf
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#stockOpnameTable').DataTable({
                paging: false,
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ ",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Data tidak ditemukan"
                },
                columnDefs: [{
                        orderable: false,
                        targets: [6]
                    },
                    {
                        className: "dt-head-center",
                        targets: "_all"
                    }
                ]
            });

            $(document).on('input', '.qty-fisik', function() {
                const fisik = parseFloat($(this).val()) || 0;
                const sistem = parseFloat($(this).data('sistem')) || 0;
                const selisih = (fisik - sistem).toFixed(2);

                const tr = $(this).closest('tr');
                const selisihCell = tr.find('.selisih');
                const inputSelisih = tr.find('.input-selisih');

                selisihCell.text(selisih);
                inputSelisih.val(selisih);
                selisihCell.removeClass('text-danger text-success text-muted');

                if (selisih < 0) {
                    selisihCell.addClass('text-danger');
                } else if (selisih > 0) {
                    selisihCell.addClass('text-success');
                } else {
                    selisihCell.addClass('text-muted');
                }
                if ($(this).val() === '') {
                    selisihCell.text('-');
                    inputSelisih.val('');
                    selisihCell.removeClass('text-danger text-success text-muted');
                }
            });
        });
    </script>
@endpush

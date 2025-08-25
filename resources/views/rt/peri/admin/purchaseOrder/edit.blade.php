@extends('peri::layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Edit Pengajuan Barang</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.purchase_orders.update', $purchaseOrder->id) }}" method="POST" id="poEditForm">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="fw-semibold">Nomor PO</label>
                        <input type="text" class="form-control" value="{{ $purchaseOrder->nomor_po ?? '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_po" class="fw-semibold">Tanggal PO <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal_po" name="tanggal_po"
                            class="form-control @error('tanggal_po') is-invalid @enderror"
                            value="{{ old('tanggal_po', $purchaseOrder->tanggal_po?->format('Y-m-d')) }}" required>
                        @error('tanggal_po')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="fw-semibold">Pilih Item</label>
                            <select id="item-select" class="form-select">
                                <option value="">pilih </option>
                                @foreach ($items as $it)
                                    <option value="{{ $it->id }}" data-kode="{{ $it->kode_barang }}"
                                        data-nama="{{ $it->nama_barang }}"
                                        data-satuan="{{ $it->satuan->nama_satuan ?? '-' }}"
                                        data-stok="{{ $it->total_stok ?? 0 }}">
                                        {{ $it->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="fw-semibold">Satuan</label>
                            <input id="unit" type="text" class="form-control" placeholder="-" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="fw-semibold">stok akhir</label>
                            <input type="number" class="form-control" id="stok-akhir" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="fw-semibold">Qty</label>
                            <input id="qty" type="number" class="form-control" step="1" min="0"
                                placeholder="0">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button id="add-item" type="button" class="btn btn-primary">
                                <i class="ri-add-line"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="poTable" class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 15%">Kode</th>
                                <th>Nama</th>
                                <th style="width: 12%">Satuan</th>
                                <th style="width: 18%">Qty</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchaseOrder->details as $d)
                                <tr>
                                    <td>
                                        <input type="hidden" name="item_code[]" value="{{ $d->item->kode_barang }}">
                                        {{ $d->item->kode_barang }}
                                    </td>
                                    <td>
                                        <input type="hidden" name="item_id[]" value="{{ $d->item_id }}">
                                        <input type="hidden" name="item_name[]" value="{{ $d->item->nama_barang }}">
                                        {{ $d->item->nama_barang }}
                                    </td>
                                    <td>
                                        @php $satuanNama = $d->item->satuan->nama_satuan ?? '-' @endphp
                                        <input type="hidden" name="unit[]" value="{{ $satuanNama }}">
                                        {{ $satuanNama }}
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control"
                                            value="{{ old('qty.' . $loop->index, $d->qty) }}" step="1" min="0"
                                            required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-row">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/peri/purchase-order.js') }}"></script>
@endpush

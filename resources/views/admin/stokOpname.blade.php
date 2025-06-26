@extends('layouts.admin')
@section('title','Daftar Barang')

@push('styles')
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Daftar Barang</h4>
        <a href="{{ route('admin.addItem') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Tambah Item
        </a>
    </div>

    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-striped align-middle" id="itemTable">
            <thead class="table-success">
                <tr>
                    <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                    <th>Produk</th>
                    <th class="text-center">Total Barang</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td><input type="checkbox" value="{{ $item->id }}" class="form-check-input item-checkbox"></td>

                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/'.$item->photo_url) }}" class="thumb"
                                alt="{{ $item->nama_barang }}">
                            <div>
                                <div class="fw-semibold">{{ $item->nama_barang }}</div>
                                <small class="text-muted">{{ $item->category->categori_name ?? 'Kategori Tidak
                                    Diketahui' }}</small>
                            </div>
                        </div>
                    </td>

                    <td class="text-center">{{ number_format($item->total_stok ?? $item->stok_minimum) }}</td>

                    <td class="text-center">
                        <form action="{{ route('admin.items.toggle',$item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <div class="form-check form-switch justify-content-center">
                                <input type="checkbox" class="form-check-input" onchange="this.form.submit()" {{
                                    isset($item->state)&&!$item->state->is_archived ? 'checked' : '' }}>
                            </div>
                        </form>
                    </td>

                    <td class="text-center">
                        <a href="{{ route('admin.items.edit',$item->id) }}" class="btn btn-outline-primary btn-sm me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm btnHapusItem"
                            data-nama="{{ $item->nama_barang }}"
                            data-action="{{ route('admin.items.destroy',$item->id) }}" data-bs-toggle="modal"
                            data-bs-target="#hapusModal">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <form id="bulkActionForm" method="POST" action="{{ route('admin.items.bulkAction') }}">
        @csrf
        <div id="actionBar" class="d-none">
            <div class="d-flex justify-content-between align-items-center px-3 py-3">
                <div>
                    <input type="checkbox" id="floatingSelectAll" class="form-check-input me-2">
                    <span id="selectedCount">0 produk dipilih</span>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-danger me-2" data-bs-toggle="modal"
                        data-bs-target="#modalHapusBulk">Hapus</button>
                    <button type="submit" name="action" value="arsipkan"
                        class="btn btn-secondary me-2">Sembunyikan</button>
                    <button type="submit" name="action" value="tampilkan" class="btn btn-success">Tampilkan</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

<div class="modal fade" id="hapusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusItem" method="POST">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Hapus Produk?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-secondary">
                    Produk <span id="namaItemDihapus" class="fw-semibold text-dark"></span> akan dihapus dari daftar.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modalHapusBulk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.items.bulkAction') }}">
            @csrf <input type="hidden" name="action" value="hapus">
            <div id="bulkItemIds"></div>

            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-secondary">
                    Apakah yakin ingin menghapus <span id="jumlahItemDihapus" class="fw-bold text-dark">0</span> item
                    terpilih?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>


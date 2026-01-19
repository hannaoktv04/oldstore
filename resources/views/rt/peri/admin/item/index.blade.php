@extends('peri::layouts.admin')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex flex-wrap justify-content-start gap-3">
                    <div class="product_category">
                        <select id="filter-kategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->categori_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>Tambah Barang
                    </a>
                </div>
            </div>

            <hr>

            <div class="table-responsive px-3 mb-2">
                <table class="table table-hover" id="itemTable" data-source="{{ route('admin.items.data') }}">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Harga</th>
                            <th class="text-center">Size</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Detail Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/peri/daftar-item.js') }}" defer></script>
@endpush

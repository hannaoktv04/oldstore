@extends('layouts.admin')
@section('title','Daftar Kategori')

@push('styles')
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Daftar Kategori</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
        </button>
    </div>

    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-striped align-middle" id="categoryTable">
            <thead class="table-success">
                <tr>
                    <th><input type="checkbox" id="selectAllCategory" class="form-check-input"></th>
                    <th>Nama Kategori</th>
                    <th class="text-center">Jumlah Item</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td><input type="checkbox" value="{{ $category->id }}" class="form-check-input category-checkbox"></td>

                    <td>{{ $category->categori_name }}</td>

                    <td class="text-center">{{ $category->items_count }}</td>

                    <td class="text-center">
                        <button class="btn btn-outline-primary btn-sm me-1 btnEditKategori"
                            data-id="{{ $category->id }}"
                            data-name="{{ $category->categori_name }}"
                            data-action="{{ route('admin.categories.update', $category->id) }}"
                            data-bs-toggle="modal" data-bs-target="#modalEditKategori">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm btnHapusKategori"
                            data-nama="{{ $category->categori_name }}"
                            data-action="{{ route('admin.categories.destroy', $category->id) }}"
                            data-bs-toggle="modal" data-bs-target="#modalHapusKategori">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form id="bulkActionFormCategory" method="POST" action="{{ route('admin.categories.bulkDelete') }}">
        @csrf
        <div id="actionBarCategory" class="d-none">
            <div class="d-flex justify-content-between align-items-center px-3 py-3">
                <div>
                    <input type="checkbox" id="floatingSelectAllCategory" class="form-check-input me-2">
                    <span id="selectedCountCategory">0 kategori dipilih</span>
                </div>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#modalHapusBulkKategori">Hapus</button>
            </div>
        </div>
    </form>
</div>
@endsection

<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newCategory" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="categori_name" id="newCategory" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditKategori" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="editCategoryName" name="categori_name" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalHapusKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapusKategori" method="POST">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Hapus Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-secondary">
                    Kategori <span id="namaKategoriDihapus" class="fw-semibold text-dark"></span> akan dihapus.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalHapusBulkKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.categories.bulkDelete') }}">
            @csrf
            <div id="bulkCategoryIds"></div>
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-secondary">
                    Yakin ingin menghapus <span id="jumlahKategoriDihapus" class="fw-bold text-dark">0</span> kategori?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/category-table.js') }}"></script>
@endpush

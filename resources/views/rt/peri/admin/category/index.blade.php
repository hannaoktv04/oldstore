@extends('peri::layouts.admin')

@section('content')
<div class="container py-4 position-relative">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Daftar Kategori</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                <i class="ri-add-line me-1"></i> Tambah Kategori
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="categoryTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center sorting_disabled" style="width: 5%;"><input type="checkbox"
                                    id="selectAllCategory" class="form-check-input"></th>
                            <th>Nama Kategori</th>
                            <th class="text-center" style="width: 15%;">Jumlah Item</th>
                            <th class="text-center sorting_disabled" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                        <tr>
                            <td class="text-center"><input type="checkbox" value="{{ $category->id }}"
                                    class="form-check-input category-checkbox"></td>
                            <td>{{ $category->categori_name }}</td>
                            <td class="text-center">{{ $category->items_count }}</td>
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect btnEditKategori"
                                    data-name="{{ $category->categori_name }}"
                                    data-action="{{ route('admin.categories.update', $category->id) }}"
                                    data-bs-toggle="modal" data-bs-target="#modalEditKategori" title="Edit">
                                    <i class="ri-pencil-line ri-20px text-primary"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect btnHapusKategori"
                                    data-nama="{{ $category->categori_name }}"
                                    data-action="{{ route('admin.categories.destroy', $category->id) }}" title="Hapus">
                                    <i class="ri-delete-bin-7-line ri-20px"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <input type="hidden" id="bulkDeleteRoute" value="{{ route('admin.categories.bulkDelete') }}">
        </div>
    </div>

    <div class="offcanvas offcanvas-bottom" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="categoryBulkActionOffcanvas">
        <div class="offcanvas-body">
            <div class="container d-flex flex-wrap justify-content-between align-items-center">
                <div class="text-dark">
                    <span id="selectedCountCategory" class="fw-semibold">0</span> kategori dipilih.
                </div>
                <div>
                    <button type="button" class="btn btn-danger" id="bulkDeleteBtn">Hapus Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newCategory" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" name="categori_name" id="newCategory" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
        <form id="formEditKategori" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="editCategoryName" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="editCategoryName" name="categori_name" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/peri/crud-category.js') }}"></script>
@endpush
@extends('peri::layouts.admin')

@section('content')
<div class="container">

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3">Foto Item <span class="text-danger">*</span></h5>
            <div id="imageUploadWrapper" class="d-flex flex-wrap gap-2 upload-scroller">
                <div class="upload-box">
                    <label class="upload-trigger">
                        <input type="file" name="photo_Item[]" accept="image/*" class="d-none" onchange="handleImageUpload(this)" multiple>
                        <div class="upload-placeholder">
                            <i class="ri-image-add-line ri-30px"></i>
                        </div>
                    </label>
                </div>
            </div>
            <small class="text-muted d-block mt-2">
                Klik untuk upload gambar. JPEG/PNG max 5MB.
            </small>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3">Informasi Item</h5>

            <div class="mb-3">
                <label class="form-label">Nama Item <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Size <span class="text-danger">*</span></label>

                    <div id="size-wrapper">
                        <div class="input-group mb-2">
                            <input type="text" name="sizes[]" class="form-control" placeholder="Contoh: M">
                            <button type="button" class="btn btn-outline-danger remove-size">X</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="add-size">
                        + Tambah Size
                    </button>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="harga" id="harga"
                            class="form-control"
                            placeholder="0"
                            required>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stok" class="form-control" min="0" required>
                    <small class="text-muted">Satuan: <strong>pasang</strong></small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    @foreach (App\Models\Category::orderBy('categori_name')->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->categori_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" class="form-control" rows="4" maxlength="500" required></textarea>
                <small class="text-muted">Maksimal 500 karakter.</small>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-4">
            <button type="submit" class="btn btn-primary px-4">Simpan</button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/peri/crud-item.js') }}"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('add-size').addEventListener('click', function () {
        const wrapper = document.getElementById('size-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <input type="text" name="sizes[]" class="form-control" placeholder="Contoh: L">
                <button type="button" class="btn btn-outline-danger remove-size">X</button>
            </div>
        `);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-size')) {
            e.target.closest('.input-group').remove();
        }
    });

});

document.getElementById('harga').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, '');
    this.value = new Intl.NumberFormat('id-ID').format(value);
});
</script>
@endpush


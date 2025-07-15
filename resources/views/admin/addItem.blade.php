@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h5 class="mb-4">Tambah Item Baru</h5>

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if(session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show fadeout" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <form action="{{ route('admin.storeItem') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="thumbnail_index" id="thumbnail_index" value="0">
        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Foto Item <span class="text-danger">*</span></h6>
            <div id="imageUploadWrapper" class="d-flex flex-wrap gap-2">
                <div class="upload-box">
                    <label class="upload-trigger">
                        <input type="file" name="photo_Item[]" accept="image/*"
                               class="d-none" onchange="handleImageUpload(this)" multiple>
                        <div class="upload-placeholder">
                            <i class="bi bi-image fs-2 d-block text-secondary"></i>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Informasi Item</h6>

            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Item <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" id="nama_barang" class="form-control" maxlength="100" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kode_barang" class="form-label">Kode Item <span class="text-danger">*</span></label>
                    <input type="text" name="kode_barang" id="kode_barang" class="form-control text-uppercase" maxlength="30" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stok_awal" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                    <input type="number" name="stok_awal" id="stok_awal" class="form-control" min="0" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <select name="satuan" id="satuan" class="form-select" required>
                        <option value="" selected disabled>Pilih Satuan</option>
                        @foreach(['pcs','buah','rim','pack','dus','botol'] as $sat)
                            <option value="{{ $sat }}">{{ ucfirst($sat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="" selected disabled>Pilih Kategori</option>
                        @foreach(App\Models\Category::orderBy('categori_name')->get() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->categori_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" maxlength="500" required placeholder="Tuliskan deskripsi item..."></textarea>
                <small class="text-muted">Maksimal 500 karakter.</small>
            </div>
        </div>

        <div class="d-flex gap-3 mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 hover-3d">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-success px-4 hover-3d">
                <i class="bi bi-save2 me-1"></i> Simpan
            </button>
        </div>
    </form>

    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-8">
                        <img id="cropperImage" class="img-fluid" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preview:</label>
                        <div class="border rounded" style="height: 200px; overflow: hidden;">
                            <img id="cropPreview" class="img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-success" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" id="saveCropBtn">Simpan Gambar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

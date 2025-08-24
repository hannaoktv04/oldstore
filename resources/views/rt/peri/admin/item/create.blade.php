@extends('peri::layouts.admin')

@section('content')
    <div class="container">

        @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
            @if (session($key))
                <div class="alert alert-{{ $type }} alert-dismissible fade show fadeout" role="alert">
                    {{ session($key) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach
        <form id="itemForm" action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="thumbnail_index" id="thumbnail_index" value="0">
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="fw-semibold mb-3">Foto Item <span class="text-danger">*</span></h5>
                <div id="imageUploadWrapper" class="d-flex flex-wrap gap-2 upload-scroller">
                    <div class="upload-box">
                        <label class="upload-trigger">
                            <input type="file" name="photo_Item[]" accept="image/*" class="d-none"
                                onchange="handleImageUpload(this)" multiple>
                            <div class="upload-placeholder">
                                <i class="ri-image-add-line ri-30px"></i>
                            </div>
                        </label>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Klik gambar untuk memilih thumbnail. Format JPEG/PNG max 5 MB.</small>
            </div>

            <div class="card shadow-sm p-4 mb-4">
                <h5 class="fw-semibold mb-3">Informasi Item</h5>
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Item <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control" maxlength="100" required>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="kode_barang" class="form-label">Kode Item <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" id="kode_barang" class="form-control text-uppercase"
                            maxlength="30" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                        <select name="satuan" id="satuan" class="form-select" required>
                            <option value="" selected disabled>Pilih Satuan</option>
                            @foreach (App\Models\Satuan::orderBy('nama_satuan')->get() as $satuan)
                                <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="" selected disabled>Pilih Kategori</option>
                            @foreach (App\Models\Category::orderBy('categori_name')->get() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->categori_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="stok_awal" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stok_awal" id="stok_awal" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stok_minimum" class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                        <input type="number" name="stok_minimum" id="stok_minimum" class="form-control" required></input>
                    </div>

                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" maxlength="500" required
                        placeholder="Tuliskan deskripsi item..."></textarea>
                    <small class="text-muted">Maksimal 500 karakter.</small>
                </div>
            </div>
            <div class="d-flex gap-3 mb-4 justify-content-end">
                <button id="submitBtn" type="submit" class="btn btn-primary px-4">
                    Simpan
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
                
            </div>
        </form>
        <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-titler">Pangkas Gambar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="width: 100%; height: 350px; overflow: hidden;">
                            <img id="cropperImage" style="width: 100%; height: 100%; object-fit: contain;"
                                class="img-fluid">
                        </div>
                    </div>
                    <div class="modal-footer border-0 mt-2">
                        <button class="btn btn-primary" id="saveCropBtn">Simpan</button>
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/peri/crud-item.js') }}"></script>
@endpush

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

    <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Hidden input untuk thumbnail index (keperluan script crud-item.js) --}}
        <input type="hidden" name="thumbnail_index" id="thumbnail_index"
            value="{{ $item->images->search(function ($img) use ($item) {return $img->id == $item->photo_id;}) }}">

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3">Foto Item <span class="text-danger">*</span></h5>
            <div id="imageUploadWrapper" class="d-flex flex-wrap gap-2 upload-scroller">
                {{-- Menampilkan Gambar yang Sudah Ada --}}
                @foreach ($item->images as $index => $img)
                <div class="upload-box {{ $img->id == $item->photo_id ? 'selected-thumbnail' : '' }}"
                    data-image-id="{{ $img->id }}">
                    <img src="{{ asset('storage/' . $img->image) }}" class="preview" onclick="setThumbnail(this)">
                    <div class="tools">
                        <i class="ri-crop-line" onclick="openCropper(this)"></i>
                        <i class="ri-delete-bin-7-line" onclick="removeImage(this, {{ $img->id }})"></i>
                    </div>
                    <span class="badge bg-primary position-absolute top-0 start-0 m-1 {{ $img->id == $item->photo_id ? '' : 'd-none' }}">Thumbnail</span>
                    <input type="hidden" name="existing_images[]" value="{{ $img->id }}">
                </div>
                @endforeach

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
                Klik untuk upload/pilih thumbnail. JPEG/PNG max 5MB.
            </small>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3">Informasi Item</h5>

            <div class="mb-3">
                <label class="form-label">Nama Item <span class="text-danger">*</span></label>
                <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $item->nama_barang) }}" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Size <span class="text-danger">*</span></label>
                    <div id="size-wrapper">
                        @if(isset($item->sizes) && $item->sizes->count() > 0)
                            @foreach($item->sizes as $size)
                            <div class="input-group mb-2">
                                {{-- Menampilkan $size->size agar tidak muncul JSON --}}
                                <input type="text" name="sizes[]" class="form-control" value="{{ $size->size }}" placeholder="Contoh: M">
                                <button type="button" class="btn btn-outline-danger remove-size">X</button>
                            </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" name="sizes[]" class="form-control" placeholder="Contoh: M">
                                <button type="button" class="btn btn-outline-danger remove-size">X</button>
                            </div>
                        @endif
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
                            value="{{ old('harga', number_format($item->harga, 0, ',', '.')) }}"
                            placeholder="0"
                            required>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stok" class="form-control" min="0" value="{{ old('stok', $item->stok) }}" required>
                    <small class="text-muted">Satuan: <strong>pasang</strong></small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    @foreach (App\Models\Category::orderBy('categori_name')->get() as $cat)
                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->categori_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" class="form-control" rows="4" maxlength="500" required>{{ old('deskripsi', $item->deskripsi) }}</textarea>
                <small class="text-muted">Maksimal 500 karakter.</small>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-4">
            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">Batal</a>
        </div>
    </form>
</div>

{{-- Modal Cropper --}}
<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Pangkas Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div style="width: 100%; height: 350px; overflow: hidden;">
                    <img id="cropperImage" style="width: 100%; height: 100%; object-fit: contain;" class="img-fluid">
                </div>
            </div>
            <div class="modal-footer border-0 mt-2">
                <button class="btn btn-primary" id="saveCropBtn">Simpan</button>
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/peri/crud-item.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Logic Tambah Size
    document.getElementById('add-size').addEventListener('click', function () {
        const wrapper = document.getElementById('size-wrapper');
        wrapper.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <input type="text" name="sizes[]" class="form-control" placeholder="Contoh: L">
                <button type="button" class="btn btn-outline-danger remove-size">X</button>
            </div>
        `);
    });

    // Logic Hapus Size
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-size')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Auto format harga ke Rupiah
    const hargaInput = document.getElementById('harga');
    hargaInput.addEventListener('input', function (e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            this.value = '';
        }
    });
});
</script>
@endpush
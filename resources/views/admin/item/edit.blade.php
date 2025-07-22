@extends('layouts.admin')

@section('title', 'Edit Item')

@section('content')
<div class="container mt-4">
    <h5 class="mb-4">Edit Item</h5>

    @foreach (['success'=>'success','error'=>'danger'] as $k=>$t)
    @if(session($k))
    <div class="alert alert-{{ $t }} alert-dismissible fade show" role="alert">
        {{ session($k) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @endforeach

    <form action="{{ route('admin.item.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <input type="hidden" name="thumbnail_index" id="thumbnail_index"
            value="{{ $item->images->search(function($img) use ($item) { return $img->id == $item->photo_id; }) }}">

        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Foto Item</h6>
            <div id="imageUploadWrapper" class="d-flex flex-wrap gap-2">
                @foreach ($item->images as $index => $img)
                <div class="upload-box {{ $img->id == $item->photo_id ? 'selected-thumbnail' : '' }}"
                    data-image-id="{{ $img->id }}">
                    <img src="{{ asset('storage/' . $img->image) }}" class="preview" onclick="setThumbnail(this)">
                    <div class="tools">
                        <i class="bi bi-crop" onclick="openCropper(this)"></i>
                        <i class="bi bi-trash" onclick="removeImage(this, {{ $img->id }})"></i>
                    </div>
                    <span
                        class="badge bg-success position-absolute top-0 start-0 m-1 {{ $img->id == $item->photo_id ? '' : 'd-none' }}">Thumbnail</span>
                    <input type="hidden" name="existing_images[]" value="{{ $img->id }}">
                    <input type="file" name="photo_Item[]" class="d-none">
                </div>
                @endforeach

                <div class="upload-box">
                    <label class="upload-trigger">
                        <input type="file" accept="image/*" class="d-none" onchange="handleImageUpload(this)" multiple>
                        <div
                            class="upload-placeholder d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-image fs-2 d-block text-secondary"></i>
                        </div>
                    </label>
                </div>
            </div>
            <small class="text-muted d-block mt-2">Klik gambar untuk memilih thumbnail. Format JPEG/PNG max 2MB.</small>
        </div>

        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Informasi Item</h6>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Item</label>
                    <input type="text" name="kode_barang" class="form-control text-uppercase"
                        value="{{ old('kode_barang', $item->kode_barang) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Item</label>
                    <input type="text" name="nama_barang" class="form-control"
                        value="{{ old('nama_barang', $item->nama_barang) }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-control"
                        value="{{ old('stok_minimum', $item->stok_minimum) }}" required>
                </div>

            </div>
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Satuan</label>
                    <select name="satuan" class="form-select" required>
                        @foreach(['pcs','buah','rim','pack','dus','botol'] as $sat)
                        <option value="{{ $sat }}" {{ $item->satuan === $sat ? 'selected' : '' }}>
                            {{ ucfirst($sat) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        @foreach(App\Models\Category::orderBy('categori_name')->get() as $cat)
                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->categori_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="form-control" maxlength="500"
                    required>{{ old('deskripsi', $item->deskripsi) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-3 mb-4">
            <a href="{{ route('admin.items') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save2 me-1"></i> Update
            </button>
        </div>
    </form>
</div>
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
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success" id="saveCropBtn">Simpan Gambar</button>
            </div>
        </div>
    </div>
</div>

@endsection

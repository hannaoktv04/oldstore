@extends('layouts.admin')

@section('title', 'Edit Item')

@section('content')
<div class="container mt-4">
    <h5 class="mb-4">Edit Item</h5>
    <form action="{{ route('admin.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                        <i class="ri-crop-line" onclick="openCropper(this)"></i>
                        <i class="ri-delete-bin-7-line" onclick="removeImage(this, {{ $img->id }})"></i>
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
                            <i class="ri-image-add-line text-secondary"></i>
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
                        @foreach (App\Models\Satuan::orderBy('nama_satuan')->get() as $satuan)
                        <option value="{{ $satuan->id }}" {{ (old('satuan_id') ?? ($item->satuan_id ?? '')) ==
                            $satuan->id ? 'selected' : '' }}>
                            {{ ucfirst($satuan->nama_satuan) }}
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
            <a href="{{ url()->previous() }}"" class=" btn btn-outline-secondary hover-3d">
                Kembali
            </a>
            <button type="submit" class="btn btn-success hover-3d">
                Update
            </button>
        </div>
    </form>
</div>
<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
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
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success" id="saveCropBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    let cropper = null;
let cropTargetImage = null;
let cropTargetInput = null;

window.handleImageUpload = function (input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const wrapper = document.getElementById("imageUploadWrapper");
        if (!wrapper) return;

        const box = document.createElement("div");
        box.className = "upload-box";
        box.innerHTML = `
            <img src="${e.target.result}" class="preview" onclick="setThumbnail(this)">
            <div class="tools">
                <i class="ri-crop-line" onclick="openCropper(this)"></i>
                <i class="ri-delete-bin-7-line" onclick="removeImage(this)"></i>
            </div>
            <span class="badge bg-success position-absolute top-0 start-0 m-1 d-none">Thumbnail</span>
            <input type="file" name="photo_Item[]" accept="image/*" style="display:none;">
        `;

        const hiddenInput = box.querySelector('input[type="file"]');
        const dt = new DataTransfer();
        dt.items.add(file);
        hiddenInput.files = dt.files;

        wrapper.insertBefore(box, wrapper.lastElementChild);
        input.value = "";
        if (!wrapper.querySelector(".upload-trigger")) {
            const newBox = document.createElement("div");
            newBox.className = "upload-box";
            newBox.innerHTML = `
                <label class="upload-trigger">
                    <input type="file" accept="image/*" class="d-none" onchange="handleImageUpload(this)">
                    <div class="upload-placeholder">
                        <i class="bi bi-image fs-2 d-block text-secondary"></i>
                        <span class="text-secondary small">Tambah Gambar</span>
                    </div>
                </label>
            `;
            wrapper.appendChild(newBox);
        }
    };
    reader.readAsDataURL(file);
};

window.removeImage = function (el, imageId = null) {
    const box = el.closest(".upload-box");
    if (!box) return;

    if (imageId) {
        if (confirm("Yakin ingin menghapus gambar ini?")) {
            fetch(`/admin/items/images/${imageId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        box.remove();
                        updateThumbnailIndex();
                    } else {
                        alert(data.message || "Gagal menghapus gambar");
                    }
                })
                .catch((err) => {
                    console.error(err);
                    alert("Terjadi kesalahan saat menghapus gambar");
                });
        }
    } else {
        box.remove();
        updateThumbnailIndex();
    }
};

function updateThumbnailIndex() {
    const wrapper = document.getElementById("imageUploadWrapper");
    const selectedBox = wrapper?.querySelector(".selected-thumbnail");
    const newIndex = selectedBox
        ? Array.from(wrapper.children).indexOf(selectedBox)
        : 0;
    const thumbnailIndex = document.getElementById("thumbnail_index");
    if (thumbnailIndex) thumbnailIndex.value = newIndex;
}

window.setThumbnail = function (imgEl) {
    const box = imgEl.closest(".upload-box");
    if (!box) return;

    const allBoxes = document.querySelectorAll(
        "#imageUploadWrapper .upload-box"
    );
    allBoxes.forEach((b) => {
        b.classList.remove("selected-thumbnail");
        b.querySelector(".badge")?.classList.add("d-none");
    });

    box.classList.add("selected-thumbnail");
    box.querySelector(".badge")?.classList.remove("d-none");

    const wrapper = document.getElementById("imageUploadWrapper");
    const index = Array.from(wrapper.children).indexOf(box);
    const thumbnailIndex = document.getElementById("thumbnail_index");
    if (thumbnailIndex) thumbnailIndex.value = index;
};

window.openCropper = function (icon) {
    const box = icon.closest(".upload-box");
    const img = box?.querySelector("img.preview");
    cropTargetImage = img;
    cropTargetInput = box.querySelector('input[type="file"]');

    if (!cropTargetInput && box?.hasAttribute("data-image-id")) {
        const input = document.createElement("input");
        input.type = "file";
        input.name = "photo_Item[]";
        input.style.display = "none";
        box.appendChild(input);
        cropTargetInput = input;
    }

    const cropperImage = document.getElementById("cropperImage");
    if (!cropperImage || !img) return;

    if (cropper) {
        cropper.destroy();
        cropper = null;
    }

    cropperImage.src = img.src;

    const modalEl = document.getElementById("cropperModal");
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    modalEl.addEventListener('shown.bs.modal', function onShown() {
        modalEl.removeEventListener('shown.bs.modal', onShown);

        cropper = new Cropper(cropperImage, {
            aspectRatio: 1,
            viewMode: 1,
            autoCrop: true,
            autoCropArea: 1,
            responsive: true,
            ready() {
                this.cropper.setCropBoxData({
                    width: 350,
                    height: 350
                });
            }
        });
    }, {once: true});
};

document.getElementById("saveCropBtn").addEventListener("click", function() {
    if (cropper && cropTargetImage && cropTargetInput) {
        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500,
            minWidth: 256,
            minHeight: 256,
            maxWidth: 2000,
            maxHeight: 2000,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        canvas.toBlob(function(blob) {
            cropTargetImage.src = canvas.toDataURL('image/jpeg', 0.92);
            const file = new File([blob], 'cropped-image.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            cropTargetInput.files = dt.files;
            bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
            cropper.destroy();
            cropper = null;
        }, 'image/jpeg', 0.92);
    }
});
</script>
@endpush

@extends('layouts.admin')

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

    <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="thumbnail_index" id="thumbnail_index" value="0">
        <div class="card shadow-sm p-4 mb-4">
            <h6 class="fw-semibold mb-3">Foto Item <span class="text-danger">*</span></h6>
            <div id="imageUploadWrapper" class="d-flex flex-wrap gap-2">
                <div class="upload-box">
                    <label class="upload-trigger">
                        <input type="file" name="photo_Item[]" accept="image/*" class="d-none"
                            onchange="handleImageUpload(this)" multiple>
                        <div class="upload-placeholder">
                            <i class="ri-image-add-line"></i>
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

        <div class="d-flex gap-3 mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 hover-3d">
                Batal
            </a>
            <button type="submit" class="btn btn-success px-4 hover-3d">
                Simpan
            </button>
        </div>
    </form>
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
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
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success" id="saveCropBtn">Simpan</button>
                </div>
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

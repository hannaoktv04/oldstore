let cropper = null;
let cropTargetImage = null;
let cropTargetInput = null;

window.handleImageUpload = function (input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const wrapper = document.getElementById("imageUploadWrapper");

        const box = document.createElement("div");
        box.className = "upload-box";
        box.innerHTML = `
            <img src="${e.target.result}" class="preview" onclick="setThumbnail(this)">
            <div class="tools">
                <i class="bi bi-crop" onclick="openCropper(this)"></i>
                <i class="bi bi-trash" onclick="removeImage(this)"></i>
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
    if (imageId) {
        if (confirm("Yakin ingin menghapus gambar ini?")) {
            fetch(`/admin/items/images/${imageId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        el.closest(".upload-box").remove();
                        updateThumbnailIndex();
                    } else {
                        alert(data.message || "Gagal menghapus gambar");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan saat menghapus gambar");
                });
        }
    } else {
        const box = el.closest(".upload-box");
        box.remove();
        updateThumbnailIndex();
    }
};

function updateThumbnailIndex() {
    const wrapper = document.getElementById("imageUploadWrapper");
    const selectedBox = wrapper.querySelector(".selected-thumbnail");
    const newIndex = selectedBox
        ? Array.from(wrapper.children).indexOf(selectedBox)
        : 0;
    document.getElementById("thumbnail_index").value = newIndex;
}
window.setThumbnail = function (imgEl) {
    const allBoxes = document.querySelectorAll(
        "#imageUploadWrapper .upload-box"
    );
    allBoxes.forEach((box) => {
        box.classList.remove("selected-thumbnail");
        box.querySelector(".badge")?.classList.add("d-none");
    });

    const box = imgEl.closest(".upload-box");
    box.classList.add("selected-thumbnail");
    box.querySelector(".badge").classList.remove("d-none");

    const index = Array.from(
        document.getElementById("imageUploadWrapper").children
    ).indexOf(box);
    document.getElementById("thumbnail_index").value = index;
};

window.openCropper = function (icon) {
    const box = icon.closest(".upload-box");
    const img = box.querySelector("img.preview");
    cropTargetImage = img;
    cropTargetInput = box.querySelector('input[type="file"]');

    if (!cropTargetInput && box.hasAttribute("data-image-id")) {
        const input = document.createElement("input");
        input.type = "file";
        input.name = "photo_Item[]";
        input.style.display = "none";
        box.appendChild(input);
        cropTargetInput = input;
    }

    const cropperImage = document.getElementById("cropperImage");
    const previewEl = document.getElementById("cropPreview");

    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    cropperImage.src = img.src;
    previewEl.src = img.src;
    const modal = new bootstrap.Modal(document.getElementById("cropperModal"));
    modal.show();
    setTimeout(() => {
        cropper = new Cropper(cropperImage, {
            aspectRatio: 3 / 4,
            viewMode: 1,
            preview: "#cropPreview",
            autoCrop: false,
        });
    }, 300);
};

document.addEventListener("DOMContentLoaded", function () {
    const saveBtn = document.getElementById("saveCropBtn");
    if (saveBtn) {
        saveBtn.addEventListener("click", function () {
            if (cropper && cropTargetImage && cropTargetInput) {
                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    minWidth: 256,
                    minHeight: 256,
                    maxWidth: 2000,
                    maxHeight: 2000,
                    fillColor: "#fff",
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: "high",
                });

                // When saving cropped image
                canvas.toBlob(
                    function (blob) {
                        const url = URL.createObjectURL(blob);
                        cropTargetImage.src = url;

                        // Create new file input if needed
                        if (!cropTargetInput) {
                            cropTargetInput = document.createElement("input");
                            cropTargetInput.type = "file";
                            cropTargetInput.name = "photo_Item[]";
                            cropTargetInput.style.display = "none";
                            cropTargetImage
                                .closest(".upload-box")
                                .appendChild(cropTargetInput);
                        }

                        // Create file from blob
                        const file = new File(
                            [blob],
                            `cropped-${Date.now()}.jpg`,
                            {
                                type: "image/jpeg",
                                lastModified: Date.now(),
                            }
                        );

                        const dt = new DataTransfer();
                        dt.items.add(file);
                        cropTargetInput.files = dt.files;

                        // Remove the existing image reference if this was a crop of an existing image
                        const box = cropTargetImage.closest(".upload-box");
                        if (box.hasAttribute("data-image-id")) {
                            box.querySelector(
                                'input[name="existing_images[]"]'
                            ).remove();
                        }

                        // Close modal
                        bootstrap.Modal.getInstance(
                            document.getElementById("cropperModal")
                        ).hide();
                    },
                    "image/jpeg",
                    0.92
                );
            }
        });
    }

    document
        .getElementById("cropperModal")
        .addEventListener("hidden.bs.modal", function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            cropTargetImage = null;
            cropTargetInput = null;
        });

    const initialThumbnail = document.querySelector(".selected-thumbnail");
    if (initialThumbnail) {
        const wrapper = document.getElementById("imageUploadWrapper");
        const index = Array.from(wrapper.children).indexOf(initialThumbnail);
        document.getElementById("thumbnail_index").value = index;
    }
});

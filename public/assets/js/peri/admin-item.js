document.addEventListener("DOMContentLoaded", function () {
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
            <span class="badge bg-primary position-absolute top-0 start-0 m-1 d-none">Thumbnail</span>
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

        if (!imageId) {
            box.remove();
            if (typeof updateThumbnailIndex === 'function') updateThumbnailIndex();
            return;
        }

        swalConfirm('Gambar akan dihapus.')
            .then((ok) => {
                if (!ok) return;
                return apiJson(`/admin/items/images/${imageId}`, { method: 'DELETE' });
            })
            .then((data) => {
                if (!data) return;
                return swalSuccess(data.message || 'Gambar berhasil dihapus').then(() => {
                    box.remove();
                    if (typeof updateThumbnailIndex === 'function') updateThumbnailIndex();
                });
            })
            .catch((e) => {
                swalError(e.message || 'Terjadi kesalahan saat menghapus gambar');
            });
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
        updateThumbnailIndex();
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

        modalEl.addEventListener(
            "shown.bs.modal",
            function onShown() {
                modalEl.removeEventListener("shown.bs.modal", onShown);

                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCrop: true,
                    autoCropArea: 1,
                    responsive: true,
                    ready() {
                        this.cropper.setCropBoxData({
                            width: 350,
                            height: 350,
                        });
                    },
                });
            },
            { once: true }
        );
    };

    const saveCropBtn = document.getElementById("saveCropBtn");
    if (saveCropBtn) {
        saveCropBtn.addEventListener("click", function () {
            if (cropper && cropTargetImage && cropTargetInput) {
                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    fillColor: "#fff",
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: "high",
                });

                canvas.toBlob(
                    function (blob) {
                        cropTargetImage.src = canvas.toDataURL(
                            "image/jpeg",
                            0.92
                        );
                        const file = new File([blob], "cropped-image.jpg", {
                            type: "image/jpeg",
                        });
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        cropTargetInput.files = dt.files;
                        bootstrap.Modal.getInstance(
                            document.getElementById("cropperModal")
                        ).hide();
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                    },
                    "image/jpeg",
                    0.92
                );
            }
        });
    }

    const form = document.getElementById("itemForm");
if (form) {
  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById("submitBtn");
    const originalBtnText = submitBtn.innerHTML;
    const restore = () => { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; };

    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    try {
      const data = await apiJson(form.action, {
        method: "POST",
        body: new FormData(form)
      });

      await swalSuccess(data.message || "Item berhasil disimpan!", {
        showConfirmButton: true,
        showCancelButton: false,
        allowOutsideClick: false,
        timer: undefined
      });

      if (data.redirect_url) {
        window.location.href = data.redirect_url;
      } else {
        restore();
      }
    } catch (e) {
      await swalError(e.message || "Terjadi kesalahan.");
      restore();
    }
  });
}

});

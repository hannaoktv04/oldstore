import Cropper from 'cropperjs';

let cropper = null;
let cropTargetImage = null;
let cropTargetInput = null;

window.handleImageUpload = function (input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const wrapper = document.getElementById('imageUploadWrapper');

        const box = document.createElement('div');
        box.className = 'upload-box';
        box.innerHTML = `
            <img src="${e.target.result}" class="preview">
            <div class="tools">
                <i class="bi bi-crop" onclick="openCropper(this)"></i>
                <i class="bi bi-trash" onclick="removeImage(this)"></i>
            </div>
            <input type="file" name="photo_Item[]" accept="image/*" style="display:none;">
        `;

        const hiddenInput = box.querySelector('input[type="file"]');
        const dt = new DataTransfer();
        dt.items.add(file);
        hiddenInput.files = dt.files;

        wrapper.insertBefore(box, wrapper.lastElementChild);
        input.value = ''; 

        if (!wrapper.querySelector('.upload-trigger')) {
            const newBox = document.createElement('div');
            newBox.className = 'upload-box';
            newBox.innerHTML = `
                <label class="upload-trigger">
                    <input type="file" accept="image/*" class="d-none" onchange="handleImageUpload(this)">
                    <div class="upload-placeholder d-flex flex-column justify-content-center align-items-center h-100">
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

window.removeImage = function (el) {
    el.closest('.upload-box').remove();
};

window.openCropper = function (icon) {
    const box = icon.closest('.upload-box');
    const img = box.querySelector('img.preview');
    cropTargetImage = img;
    cropTargetInput = box.querySelector('input[type="file"]');

    const cropperImage = document.getElementById('cropperImage');
    const previewEl = document.getElementById('cropPreview');

    cropperImage.src = img.src;
    previewEl.src = img.src;

    const modal = new bootstrap.Modal(document.getElementById('cropperModal'));
    modal.show();

    setTimeout(() => {
        cropper = new Cropper(cropperImage, {
            aspectRatio: 3 / 4,
            viewMode: 1,
            preview: '#cropPreview'
        });
    }, 300);
};

document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveCropBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            if (cropper && cropTargetImage && cropTargetInput) {
                const canvas = cropper.getCroppedCanvas({ width: 300, height: 400 });

                canvas.toBlob((blob) => {
                    const url = URL.createObjectURL(blob);
                    cropTargetImage.src = url;
                    const dt = new DataTransfer();
                    const file = new File([blob], `cropped-${Date.now()}.jpg`, { type: 'image/jpeg' });
                    dt.items.add(file);
                    cropTargetInput.files = dt.files;
                }, 'image/jpeg');

                cropper.destroy();
                cropper = null;
                cropTargetImage = null;
                cropTargetInput = null;

                bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
  const wrapper = document.getElementById("imageUploadWrapper");
  if (wrapper) {
    wrapper.style.flexWrap = "nowrap";
    wrapper.style.overflowX = "auto";
    wrapper.style.overflowY = "hidden";
  }

  window.swalSuccess = window.swalSuccess || function (text, opts = {}) {
    return Swal.fire(Object.assign({
      icon: 'success',
      title: 'Berhasil!',
      text: text || 'Aksi berhasil.',
      confirmButtonText: 'Oke',
      showCancelButton: false,
      allowOutsideClick: false
    }, opts));
  };
  window.swalError = window.swalError || function (text, opts = {}) {
    return Swal.fire(Object.assign({
      icon: 'error',
      title: 'Gagal!',
      text: text || 'Terjadi kesalahan.',
      confirmButtonText: 'Oke',
      showCancelButton: false
    }, opts));
  };
  window.swalConfirm = window.swalConfirm || function (text, opts = {}) {
    return Swal.fire(Object.assign({
      icon: 'warning',
      title: 'Anda Yakin?',
      text: text || 'Tindakan ini tidak dapat dibatalkan.',
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Batal'
    }, opts)).then(r => r.isConfirmed);
  };

  window.apiJson = window.apiJson || async function (url, options = {}) {
    const resp = await fetch(url, Object.assign({
      headers: { 'Accept': 'application/json' }
    }, options));
    if (!resp.ok) {
      let msg = 'Request gagal';
      try { const j = await resp.json(); msg = j.message || msg; } catch(e){}
      throw new Error(msg);
    }
    return resp.json();
  };

  function ensureTriggerBox() {
    if (!wrapper) return;
    if (!wrapper.querySelector(".upload-trigger")) {
      const newBox = document.createElement("div");
      newBox.className = "upload-box";
      newBox.innerHTML = `
        <label class="upload-trigger">
          <input type="file" name="photo_Item[]" accept="image/*" class="d-none"
                 onchange="handleImageUpload(this)" multiple>
          <div class="upload-placeholder">
            <i class="bi bi-image fs-2 d-block text-secondary"></i>
            <span class="text-secondary small">Tambah Gambar</span>
          </div>
        </label>
      `;
      wrapper.appendChild(newBox);
    }
  }
  ensureTriggerBox();

  if (wrapper) {
    wrapper.addEventListener("click", function (e) {
      const box = e.target.closest(".upload-box");
      if (!box) return;
      const triggerInput = box.querySelector(".upload-trigger input[type='file']");
      if (triggerInput) {
        const isTools = e.target.closest(".tools");
        if (!isTools) triggerInput.click();
      }
    });
  }

  function prevent(e){ e.preventDefault(); e.stopPropagation(); }
  if (wrapper) {
    ["dragenter","dragover"].forEach(ev => wrapper.addEventListener(ev, prevent));
    ["dragleave","drop"].forEach(ev => wrapper.addEventListener(ev, prevent));

    wrapper.addEventListener("drop", function (e) {
      const files = e.dataTransfer?.files;
      if (!files || !files.length) return;
      appendFiles(files);
    });
  }

  function appendOneFile(file) {
    if (!wrapper || !file || !/^image\//.test(file.type)) return;

    const box = document.createElement("div");
    box.className = "upload-box";
    box.innerHTML = `
      <img src="" class="preview" onclick="setThumbnail(this)">
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

    const reader = new FileReader();
    reader.onload = function (ev) {
      box.querySelector(".preview").src = ev.target.result;
    };
    reader.readAsDataURL(file);
    const triggerBox = wrapper.querySelector(".upload-trigger")?.closest(".upload-box") || wrapper.lastElementChild;
    wrapper.insertBefore(box, triggerBox);
    wrapper.scrollLeft = wrapper.scrollWidth;
  }

  function appendFiles(fileList) {
    Array.from(fileList).forEach(f => appendOneFile(f));
    if (!wrapper.querySelector(".upload-box.selected-thumbnail")) {
      const firstPreview = wrapper.querySelector(".upload-box .preview");
      if (firstPreview) setThumbnail(firstPreview);
    }
    ensureTriggerBox();
  }

  window.handleImageUpload = function (input) {
    if (!input || !input.files || !input.files.length) return;
    appendFiles(input.files);
    input.value = "";
  };

  window.removeImage = function (el, imageId = null) {
    const box = el.closest(".upload-box");
    if (!box) return;
    if (!imageId) {
      box.remove();
      ensureTriggerBox();
      if (typeof updateThumbnailIndex === 'function') updateThumbnailIndex();
      return;
    }
    swalConfirm('Gambar akan dihapus.').then(ok => {
      if (!ok) return;
      return apiJson(`/admin/items/images/${imageId}`, { method: 'DELETE' });
    }).then((data) => {
      if (!data) return;
      return swalSuccess(data.message || 'Gambar berhasil dihapus'); 
    }).then(() => {
      box.remove();
      ensureTriggerBox();
      if (typeof updateThumbnailIndex === 'function') updateThumbnailIndex();
    }).catch((e) => {
      swalError(e.message || 'Terjadi kesalahan saat menghapus gambar');
    });
  };

  (function normalizeEditBadges(){
    document.querySelectorAll("#imageUploadWrapper .upload-box .badge").forEach(b=>{
      b.classList.remove("bg-success","bg-danger","bg-secondary");
      b.classList.add("bg-primary");
    });
  })();

  function updateThumbnailIndex() {
    if (!wrapper) return;
    const boxes = Array.from(wrapper.querySelectorAll('.upload-box'))
      .filter(b => b.querySelector('.preview')); 
    const selected = wrapper.querySelector('.upload-box.selected-thumbnail');
    const idx = selected ? boxes.indexOf(selected) : 0;
    const thumbnailIndex = document.getElementById("thumbnail_index");
    if (thumbnailIndex) thumbnailIndex.value = Math.max(0, idx);
  }

  window.setThumbnail = function (imgEl) {
    const box = imgEl.closest(".upload-box");
    if (!box) return;
    document.querySelectorAll("#imageUploadWrapper .upload-box").forEach((b) => {
      b.classList.remove("selected-thumbnail");
      b.querySelector(".badge")?.classList.add("d-none");
    });

    box.classList.add("selected-thumbnail");
    box.querySelector(".badge")?.classList.remove("d-none");
    updateThumbnailIndex();
  };

  let cropper = null;
  let cropTargetImage = null;
  let cropTargetInput = null;

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

    if (cropper) { cropper.destroy(); cropper = null; }

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
            this.cropper.setCropBoxData({ width: 350, height: 350 });
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
            cropTargetImage.src = canvas.toDataURL("image/jpeg", 0.92);
            const file = new File([blob], "cropped-image.jpg", { type: "image/jpeg" });
            const dt = new DataTransfer();
            dt.items.add(file);
            cropTargetInput.files = dt.files;

            bootstrap.Modal.getInstance(document.getElementById("cropperModal")).hide();
            if (cropper) { cropper.destroy(); cropper = null; }
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
      const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
      const restore = () => { if (submitBtn){ submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; } };

      if (submitBtn){
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
      }

      try {
        const data = await apiJson(form.action, {
          method: "POST",
          body: new FormData(form)
        });

        await swalSuccess(data.message || "Item berhasil disimpan!", {
          showConfirmButton: false,
          showCancelButton: false,
          allowOutsideClick: false,
          timer: 2000
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

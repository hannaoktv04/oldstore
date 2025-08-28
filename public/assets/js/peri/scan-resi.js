
(function () {
  document.addEventListener("DOMContentLoaded", () => {
    const readerEl = document.getElementById("reader");
    if (!readerEl) return;

    if (typeof Html5Qrcode === "undefined") {
      console.error("Html5Qrcode tidak tersedia. Pastikan CDN dimuat.");
      return;
    }

    if (window.__qrScannerInitialized) {
      console.warn("Scanner sudah diinisialisasi. Skip.");
      return;
    }
    window.__qrScannerInitialized = true;

    const selectEl = document.getElementById("camera-select");
    const switchBtn = document.getElementById("switch-camera");
    const statusEl  = document.getElementById("scan-status");

    let html5QrCode = null;
    let currentCameraId = null;
    let isStarting = false;
    let isRunning  = false;

    const setStatus = (msg) => { if (statusEl) statusEl.textContent = msg; };

    const stopScanner = async () => {
      if (html5QrCode && isRunning) {
        try { await html5QrCode.stop(); } catch (e) {  }
        isRunning = false;
      }
    };

    const clearScanner = async () => {
      await stopScanner();
      if (html5QrCode) {
        try { await html5QrCode.clear(); } catch (e) {  }
        html5QrCode = null;
      }
    };

    const startScanner = async (cameraIdOrFacingMode) => {
      if (isStarting) return;
      isStarting = true;
      setStatus("Menyalakan kamera…");
      try {
        if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");

        await html5QrCode.start(
          cameraIdOrFacingMode,
          { fps: 10, qrbox: 250 },
          onScanSuccess,
          onScanFailure
        );
        isRunning = true;
        setStatus("Scanner aktif. Arahkan ke barcode.");
      } catch (err) {
        console.error("Gagal start kamera:", err);
        setStatus("Gagal menyalakan kamera: " + err);
      } finally {
        isStarting = false;
      }
    };

    const onScanSuccess = async (decodedText) => {
      console.log("Scanned:", decodedText);

      if (decodedText.includes("/staff-pengiriman/konfirmasi/KP")) {
        await stopScanner();
        window.location.href = decodedText;
        return;
      }
      if (decodedText.startsWith("KP")) {
        const id = decodedText.replace(/^KP/, "").replace(/^0+/, "");
        await stopScanner();
        window.location.href = `/staff-pengiriman/konfirmasi/${id}`;
        return;
      }

      console.warn("QR tidak sesuai format:", decodedText);
      setStatus("QR tidak sesuai format resi (harus diawali 'KP…'). Coba lagi.");
    };

    const onScanFailure = (/* error */) => {
    };

    const chooseDefaultCamera = (cameras) => {
      const prefers = ["back", "rear", "environment"];
      const found = cameras.find(c => {
        const label = (c.label || "").toLowerCase();
        return prefers.some(p => label.includes(p));
      });
      return found ? found.id : cameras[cameras.length - 1].id;
    };

    const populateCameraSelect = (cameras, defaultId) => {
      if (!selectEl) return;
      selectEl.innerHTML = "";
      cameras.forEach((cam, idx) => {
        const opt = document.createElement("option");
        opt.value = cam.id;
        opt.textContent = cam.label || `Camera ${idx + 1}`;
        selectEl.appendChild(opt);
      });
      selectEl.value = defaultId;
    };

    const switchToCamera = async (cameraId) => {
      if (cameraId === currentCameraId) return;
      await clearScanner();
      currentCameraId = cameraId;
      localStorage.setItem("preferred_camera_id", cameraId);
      await startScanner(cameraId);
    };
    setStatus("Mendeteksi kamera…");
    Html5Qrcode.getCameras().then(async (cameras) => {
      if (!cameras || cameras.length === 0) {
        setStatus("Tidak ada kamera ditemukan.");
        alert("Tidak ada kamera ditemukan.");
        return;
      }

      console.log("Kamera tersedia:", cameras);
      const saved = localStorage.getItem("preferred_camera_id");
      const hasSaved = saved && cameras.some(c => c.id === saved);

      const defaultId = hasSaved ? saved : chooseDefaultCamera(cameras);
      populateCameraSelect(cameras, defaultId);

      if (selectEl) {
        selectEl.addEventListener("change", async (e) => {
          const newId = e.target.value;
          await switchToCamera(newId);
        });
      }
      if (switchBtn) {
        switchBtn.addEventListener("click", async () => {
          if (!selectEl) return;
          const idx = Array.from(selectEl.options).findIndex(o => o.value === selectEl.value);
          const nextIdx = (idx + 1) % selectEl.options.length;
          const nextId = selectEl.options[nextIdx].value;
          selectEl.value = nextId;
          await switchToCamera(nextId);
        });
      }
      await switchToCamera(defaultId);
    }).catch((err) => {
      console.error("Tidak bisa mengakses kamera:", err);
      setStatus("Tidak bisa mengakses kamera: " + err);
      alert("Gagal mengakses kamera: " + err);
    });
  });
})();

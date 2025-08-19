console.log("ri-checkbox-circle-line scan-resi.js LOADED");

if (window.location.pathname === '/staff-pengiriman/dashboard') {
    console.log("ri-dropbox-fill Memulai scanner...");

    document.addEventListener("DOMContentLoaded", () => {
        const readerElement = document.getElementById("reader");

        if (!readerElement) {
            console.warn("ri-alert-line Elemen #reader tidak ditemukan.");
            return;
        }

        if (typeof Html5Qrcode === 'undefined') {
            console.error("ri-close-circle-line Html5Qrcode tidak tersedia. Pastikan CDN dimuat.");
            return;
        }

        const html5QrCode = new Html5Qrcode("reader");

        Html5Qrcode.getCameras().then((cameras) => {
            if (!cameras || cameras.length === 0) {
                alert("ri-camera-off-line Tidak ada kamera ditemukan.");
                return;
            }

            console.log("ri-camera-line Kamera tersedia:", cameras);

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                (decodedText, decodedResult) => {
                    if (decodedText.startsWith("KP")) {
                        const id = decodedText.replace("KP", "").replace(/^0+/, '');
                        console.log(`ri-checkbox-circle-line Ditemukan: ${decodedText}, redirect ke ID: ${id}`);

                        html5QrCode.stop().then(() => {
                            window.location.href = `/staff-pengiriman/konfirmasi/${id}`;
                        });
                    } else {
                        console.warn(`ri-error-warning-line Bukan resi KP: ${decodedText}`);
                    }
                }
            ).then(() => {
                console.log("ri-qr-scan-2-line Scanner aktif.");
            }).catch((err) => {
                console.error("ri-close-circle-line Gagal nyalakan kamera:", err);
            });
        }).catch((err) => {
            console.error("ri-close-circle-line Tidak bisa akses kamera:", err);
            alert("Gagal mengakses kamera: " + err);
        });
    });
}

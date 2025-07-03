console.log("✅ scan-resi.js LOADED");

if (window.location.pathname === '/staff-pengiriman/dashboard') {
    console.log("📦 Memulai scanner...");

    document.addEventListener("DOMContentLoaded", () => {
        const readerElement = document.getElementById("reader");

        if (!readerElement) {
            console.warn("❗️Elemen #reader tidak ditemukan.");
            return;
        }

        if (typeof Html5Qrcode === 'undefined') {
            console.error("❌ Html5Qrcode tidak tersedia. Pastikan CDN dimuat.");
            return;
        }

        const html5QrCode = new Html5Qrcode("reader");

        Html5Qrcode.getCameras().then((cameras) => {
            if (!cameras || cameras.length === 0) {
                alert("📵 Tidak ada kamera ditemukan.");
                return;
            }

            console.log("🎥 Kamera tersedia:", cameras);

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                (decodedText, decodedResult) => {
                    if (decodedText.startsWith("KP")) {
                        const id = decodedText.replace("KP", "").replace(/^0+/, '');
                        console.log(`✅ Ditemukan: ${decodedText}, redirect ke ID: ${id}`);

                        html5QrCode.stop().then(() => {
                            window.location.href = `/staff-pengiriman/konfirmasi/${id}`;
                        });
                    } else {
                        console.warn(`⛔️ Bukan resi KP: ${decodedText}`);
                    }
                }
            ).then(() => {
                console.log("📸 Scanner aktif.");
            }).catch((err) => {
                console.error("❌ Gagal nyalakan kamera:", err);
            });
        }).catch((err) => {
            console.error("❌ Tidak bisa akses kamera:", err);
            alert("Gagal mengakses kamera: " + err);
        });
    });
}

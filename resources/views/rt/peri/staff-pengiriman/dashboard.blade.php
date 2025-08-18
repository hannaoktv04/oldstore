@extends('peri::layouts.staff')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body text-center">
                    <h5 class="mb-3 text-success fw-semibold">📦 Scan Resi Pengiriman</h5>
                    <p class="text-muted small mb-4">
                        Arahkan kamera ke barcode resi untuk memproses konfirmasi pengiriman.
                    </p>

                    <div id="reader" class="w-100 border rounded-3" style="min-height: 260px;"></div>

                    <div class="alert alert-info mt-3 small">
                        Pastikan kamera belakang aktif dan barcode terlihat jelas.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    function onScanSuccess(decodedText, decodedResult) {
        console.log("Scanned:", decodedText);

        if (decodedText.includes("/staff-pengiriman/konfirmasi/KP")) {
            window.location.href = decodedText;
            return;
        }

        if (decodedText.startsWith("KP")) {
            const id = decodedText.replace("KP", "").replace(/^0+/, '');
            window.location.href = `/staff-pengiriman/konfirmasi/${id}`;
            return;
        }

        alert("QR Code tidak sesuai format.");
    }

    const html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                onScanSuccess
            );
        } else {
            alert("Kamera tidak ditemukan.");
        }
    }).catch(err => {
        alert("Tidak bisa mengakses kamera: " + err);
    });
</script>
@endsection

@extends('peri::layouts.staff')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body text-center">
                    <h5 class="mb-3 text-success fw-semibold">
                        <i class="ri-qr-scan-2-line align-middle me-2"></i>
                        Scan Resi Pengiriman
                    </h5>
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

@endsection
@push('scripts')
<script src="{{ asset('assets/js/peri/scan-resi.js') }}"></script>
@endpush
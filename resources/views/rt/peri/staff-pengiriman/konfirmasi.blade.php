@extends('layouts.staff')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">📦 Konfirmasi Pengiriman - <span class="text-succ">{{ $kodeResi }}</span></h4>

    <div class="mb-3">
        <p><strong>Pemohon:</strong> {{ $pengajuan->user->nama }}</p>
        <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->translatedFormat('d F Y') }}</p>
    </div>

    <div class="mb-4">
        <h6>Daftar Barang</h6>
        <ul class="list-group">
            @foreach ($pengajuan->details as $detail)
                @if ($detail->qty_approved > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $detail->item->nama_barang }}
                        <span>{{ $detail->qty_approved }} {{ $detail->item->satuan->nama_satuan }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    <form method="POST" action="{{ route('staff-pengiriman.konfirmasi.submit', $kodeResi) }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">📷 Bukti Pengiriman</label>
            <input type="file" name="bukti_foto" class="form-control @error('bukti_foto') is-invalid @enderror" accept="image/*" required onchange="previewImage(event)">
            @error('bukti_foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="mt-2 position-relative" style="max-width: 300px;">
                <img id="buktiPreview" src="#" class="img-thumbnail d-none w-100 object-fit-cover" style="height: 200px;">
                <button type="button" onclick="removeImage()" class="btn btn-sm btn-danger position-absolute top-0 end-0 d-none" id="removeBtn" style="z-index:10;">&times;</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">📝 Catatan (Opsional)</label>
            <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">✔️ Konfirmasi Pengiriman</button>
    </form>
</div>

<script>
    function previewImage(event) {
        const preview = document.getElementById('buktiPreview');
        const removeBtn = document.getElementById('removeBtn');
        const input = event.target;

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                removeBtn.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const input = document.querySelector('input[name="bukti_foto"]');
        const preview = document.getElementById('buktiPreview');
        const removeBtn = document.getElementById('removeBtn');

        input.value = '';
        preview.src = '#';
        preview.classList.add('d-none');
        removeBtn.classList.add('d-none');
    }
</script>
@endsection
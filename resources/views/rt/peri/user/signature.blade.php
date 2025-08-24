@extends('peri::layouts.app')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

          <h5 class="mb-1 fw-semibold">Bubuhi Tanda Tangan E-Nota</h5>
          <div class="text-muted small mb-3">
            Pengajuan #{{ $request->id }} • {{ \Carbon\Carbon::parse($request->created_at)->format('d M Y') }}
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-primary rounded-3 px-3 py-2">Tanda Tangan Disini</span>
            <button id="btnClear" type="button" class="btn btn-link text-primary fw-semibold p-0">Hapus</button>
          </div>

          <div class="signature-wrapper position-relative">
            <canvas id="signatureCanvas"></canvas>
          </div>

          <form id="signatureForm" action="{{ route('pengajuan.signature.store', $request->id) }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="signature" id="signatureInput">
            <div class="d-flex gap-2">
              <a href="{{ route('pengajuan.enota', $request->id) }}" class="btn btn-outline-secondary">Batal</a>
              <button type="submit" id="btnSave" class="btn btn-primary">
                Simpan Tanda Tangan <i class="bi bi-check2 ms-1"></i>
              </button>
            </div>
          </form>

          @if ($request->ttd_path ?? false)
            <div class="alert alert-info mt-3 mb-0">
              Tanda tangan sebelumnya tersimpan. Menyimpan ulang akan menimpa file lama.
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('style')
<style>
  :root { --sig-h: 260px; }
  .signature-wrapper{
    border: 2px solid #1570ef;
    border-radius: 12px;
    height: var(--sig-h);
    max-width: 100%;
    background: #fff;
    overflow: hidden;
  }
  #signatureCanvas{
    display:block;
    width:100%;
    height:100%;
    touch-action:none;
  }
  @media (max-width:576px){ :root { --sig-h: 220px; } }
  @media print{ :root { --sig-h: 200px; } .btn,a{display:none!important;} }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
<script>
(function() {
  const canvas   = document.getElementById('signatureCanvas');
  const form     = document.getElementById('signatureForm');
  const input    = document.getElementById('signatureInput');
  const btnClear = document.getElementById('btnClear');
  const btnSave  = document.getElementById('btnSave');

  const pad = new SignaturePad(canvas, {
    penColor: '#0d6efd',
    minWidth: 0.8,
    maxWidth: 2.5,
    throttle: 16,
    backgroundColor: 'rgba(255,255,255,1)'
  });

  function resizeCanvasKeepDrawing() {
    const data  = pad.toData();
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const rect  = document.querySelector('.signature-wrapper').getBoundingClientRect();

    canvas.width  = Math.floor(rect.width * ratio);
    canvas.height = Math.floor(rect.height * ratio);
    canvas.style.width  = rect.width + 'px';
    canvas.style.height = rect.height + 'px';

    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    pad.clear();
    if (data && data.length) pad.fromData(data);
  }
  resizeCanvasKeepDrawing();
  window.addEventListener('resize', resizeCanvasKeepDrawing);

  btnClear.addEventListener('click', () => {
    pad.clear();
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  });

  /**
   * Cari bounding box piksel bertinta (non-putih) lalu pad ke square 1:1.
   * @param {HTMLCanvasElement} srcCanvas
   * @param {number} marginPx - padding di sekeliling konten (pixel di DPR).
   * @param {number|null} targetSize - kalau ingin seragamkan ukuran output, isi angka (mis. 512). Null = pakai ukuran asli.
   * @param {string} mime - 'image/png' atau 'image/jpeg'
   * @param {number|undefined} quality - untuk JPEG
   * @returns {string|null} dataURL atau null jika kosong
   */
  function exportSquareTrimmed(srcCanvas, marginPx = 24, targetSize = 512, mime = 'image/png', quality) {
    const w = srcCanvas.width, h = srcCanvas.height;
    const ctx = srcCanvas.getContext('2d');
    const img = ctx.getImageData(0, 0, w, h);
    const data = img.data;

    const isInk = (r,g,b,a) => a > 0 && !(r >= 250 && g >= 250 && b >= 250);

    let minX = w, minY = h, maxX = -1, maxY = -1;

    for (let y = 0; y < h; y++) {
      const row = y * w * 4;
      for (let x = 0; x < w; x++) {
        const i = row + x * 4;
        const r = data[i], g = data[i+1], b = data[i+2], a = data[i+3];
        if (isInk(r,g,b,a)) {
          if (x < minX) minX = x;
          if (x > maxX) maxX = x;
          if (y < minY) minY = y;
          if (y > maxY) maxY = y;
        }
      }
    }

    if (maxX < 0 || maxY < 0) return null;

    minX = Math.max(0, minX - marginPx);
    minY = Math.max(0, minY - marginPx);
    maxX = Math.min(w - 1, maxX + marginPx);
    maxY = Math.min(h - 1, maxY + marginPx);

    const cropW = maxX - minX + 1;
    const cropH = maxY - minY + 1;
    const boxSize = Math.max(cropW, cropH); 

    let sqW = boxSize, sqH = boxSize;

    const wantResize = Number.isFinite(targetSize) && targetSize > 0;
    if (wantResize) { sqW = targetSize; sqH = targetSize; }

    const out = document.createElement('canvas');
    out.width = sqW;
    out.height = sqH;
    const octx = out.getContext('2d');

    octx.fillStyle = '#fff';
    octx.fillRect(0, 0, sqW, sqH);

    const scale = wantResize ? Math.min(sqW / cropW, sqH / cropH) : 1;

    const drawW = Math.round(cropW * scale);
    const drawH = Math.round(cropH * scale);

    const dx = Math.floor((sqW - drawW) / 2);
    const dy = Math.floor((sqH - drawH) / 2);

    octx.imageSmoothingEnabled = true;
    octx.imageSmoothingQuality = 'high';
    octx.drawImage(srcCanvas, minX, minY, cropW, cropH, dx, dy, drawW, drawH);

    return out.toDataURL(mime, quality);
  }

  form.addEventListener('submit', function (e) {
    if (pad.isEmpty()) {
      e.preventDefault();
      alert('Silakan bubuhkan tanda tangan terlebih dahulu.');
      return;
    }

    const dataUrl = exportSquareTrimmed(canvas, 24, 512, 'image/png');
    if (!dataUrl) {
      e.preventDefault();
      alert('Gagal memproses tanda tangan. Coba ulangi.');
      return;
    }

    input.value = dataUrl;
    btnSave.disabled = true;
    btnSave.innerText = 'Menyimpan...';
  });
})();
</script>
@endpush

@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))

<div class="modal fade" id="receiveModal-{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="POST" action="{{ route('admin.pengajuan.received', $pengajuan->id) }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
        <div class="modal-header bg-success text-white py-3">
          <h5 class="modal-title">
            <i class="bi bi-box-arrow-in-down me-2"></i> Konfirmasi Penerimaan #{{ $no }}
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <h6 class="fw-semibold text-muted mb-3">Detail Barang</h6>

          <ul class="list-group mb-4">
            @foreach ($pengajuan->details as $d)
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="me-auto">
                  <div class="fw-semibold">{{ $d->item->nama_barang }}</div>
                  <small class="text-muted">{{ $d->item->category->categori_name ?? '-' }}</small>
                </div>
                <span class="badge bg-success rounded-pill align-self-center">
                  {{ $d->qty_approved ?? $d->qty_requested }} {{ $d->item->satuan }}
                </span>
              </li>
            @endforeach
          </ul>
          <div class="mb-4">
            <label class="form-label fw-semibold">Catatan Penerimaan <small class="text-muted">(opsional)</small></label>
            <textarea class="form-control rounded-3" name="catatan" rows="3" ></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold mb-1">
              Bukti Foto <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <input type="file" name="bukti_foto" accept="image/*" capture="environment" class="form-control rounded-start" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-3 px-4">
          <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-check-circle me-1"></i> Konfirmasi Terima
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


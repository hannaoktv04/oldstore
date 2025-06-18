@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))
<div class="modal fade" id="receiveModal-{{ $pengajuan->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="POST"
          action="{{ route('admin.pengajuan.received', $pengajuan->id) }}"
          enctype="multipart/form-data">
      @csrf
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header bg-secondary text-white rounded-top-4">
          <h5 class="modal-title">Terima Pengajuan #{{ $no }}</h5>
          <button type="button" class="btn-close btn-close-white"data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="fw-semibold mb-2">Detail Barang</p>
          <ul class="list-group mb-4">
            @foreach ($pengajuan->details as $d)
              <li class="list-group-item d-flex justify-content-between">
                <span>{{ $d->item->nama_barang }}<small class="text-muted d-block">{{ $d->item->category->categori_name ?? '-' }}
                  </small>
                </span>
                <span class="fw-bold">{{ $d->qty_approved ?? $d->qty_requested }}{{ $d->item->satuan }}</span>
              </li>
            @endforeach
          </ul>

          <div class="mb-3">
            <label class="form-label">Catatan Penerimaan (opsional)</label>
            <textarea class="form-control" name="catatan"
                      rows="2" placeholder="Mis. kondisi barang, lokasi simpan"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label d-block">
              Bukti Foto (wajib)
            </label>

            <input type="file"
                   name="bukti_foto"
                   accept="image/*"
                   capture="environment"
                   class="form-control"
                   required>
            <small class="text-muted">Ambil foto langsung atau pilih dari galeri (maks 2 MB).</small>
          </div>
        </div>

        <div class="modal-footer bg-light rounded-bottom-4">
          <button type="submit" class="btn btn-secondary px-4 py-2">
            Konfirmasi Terima
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

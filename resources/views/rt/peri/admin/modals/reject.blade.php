@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))

<div class="modal fade" id="rejectModal-{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}">
        @csrf

        <div class="modal-header rounded-top-4">
          <h5 class="modal-title">Alasan Penolakan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <textarea name="admin_note" rows="3" class="form-control rounded-3"
            placeholder="Contoh: barang tidak tersedia atau melebihi batas pengajuan."></textarea>
        </div>

        <div class="modal-footer justify-content-end">
          <button type="submit" class="btn btn-md btn-danger">Tolak</button>
          <button type="button" class="btn btn-md btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

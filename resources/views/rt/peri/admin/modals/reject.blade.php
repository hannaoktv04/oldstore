@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))

<div class="modal fade" id="rejectModal-{{ $pengajuan->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title">Tolak Pengajuan #{{ $no }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Berikan alasan penolakan (opsional).</p>
                    <textarea name="admin_note" rows="3" class="form-control"></textarea>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-danger px-4">Tolak Pengajuan</button>
                </div>
            </div>
        </form>
    </div>
</div>

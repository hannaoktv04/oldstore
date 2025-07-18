<div class="modal fade" id="assignModal-{{ $pengajuan->id }}" tabindex="-1" aria-labelledby="assignModalLabel-{{ $pengajuan->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.pengajuan.assign', $pengajuan->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel-{{ $pengajuan->id }}">Assign Staff Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="staff_pengiriman" class="form-label">Pilih Staff</label>
                        <select name="staff_pengiriman" id="staff_pengiriman" class="form-select" required>
                            <option value="">-- Pilih Staff --</option>
                            @foreach($staff_pengiriman as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Assign</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="assignModal-{{ $pengajuan->id }}" tabindex="-1" aria-labelledby="assignModalLabel-{{ $pengajuan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0">
            <form action="{{ route('admin.pengajuan.assign', $pengajuan->id) }}" method="POST">
                @csrf
                <div class="modal-header rounded-top-4 border-bottom py-2">
                    <h5 class="modal-title" id="assignModalLabel-{{ $pengajuan->id }}">Assign Staff Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div>
                        <label for="staff_pengiriman_{{ $pengajuan->id }}" class="form-label">Pilih Staff Pengiriman</label>
                        <select name="staff_pengiriman" id="staff_pengiriman_{{ $pengajuan->id }}" class="form-select" required>
                            <option value="">-- Pilih Staff --</option>
                            @foreach($staff_pengiriman as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_{{ $pengajuan->id }}" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan_{{ $pengajuan->id }}" class="form-control rounded-3" rows="2"
                            placeholder="Contoh: Kirim siang hari atau ada barang fragile."></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-start border-0">
                    <button type="submit" class="btn btn-md btn-primary">Assign</button>
                    <button type="button" class="btn btn-md btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

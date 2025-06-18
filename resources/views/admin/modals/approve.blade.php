@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))

<div class="modal fade" id="approveModal-{{ $pengajuan->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('pengajuan.approve', $pengajuan->id) }}">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title">Setujui Pengajuan #{{ $no }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4 text-muted">Silakan sesuaikan jumlah barang</p>

                    @foreach ($pengajuan->details as $detail)
                        <div class="mb-4 border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $detail->item->nama_barang }}</strong><br>
                                    <small class="text-muted">Diminta: {{ $detail->qty_requested }}</small><br>
                                    <small class="text-muted">Stok tersedia: {{ $detail->item->stocks->sum('qty') }}</small>
                                </div>
                                <div class="form-check text-end">
                                    <input type="checkbox" class="form-check-input setujui-semua"
                                           data-target="#approvedQty{{ $detail->id }}"
                                           data-jumlah="{{ $detail->qty_requested }}"
                                           id="check{{ $detail->id }}">
                                    <label class="form-check-label small" for="check{{ $detail->id }}">Sesuai</label>
                                </div>
                            </div>
                            <input type="number" name="approved_qty[{{ $detail->id }}]"
                                   id="approvedQty{{ $detail->id }}" class="form-control mt-2"
                                   max="{{ $detail->qty_requested }}" min="0" required>
                        </div>
                    @endforeach

                    <div class="mb-3">
                        <label class="form-label">Keterangan Persetujuan (opsional)</label>
                        <textarea class="form-control" name="admin_note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-success px-4 py-2">Setujui Pengajuan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))

<div class="modal fade" id="approveModal-{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('pengajuan.approve', $pengajuan->id) }}">
            @csrf
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title">
                        <i class="bi bi-check2-square me-2"></i> Persetujuan Pengajuan #{{ $no }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>


                <div class="modal-body p-4">
                    <p class="text-muted mb-4">Silakan sesuaikan jumlah barang yang akan disetujui.</p>

                    @foreach ($pengajuan->details as $detail)
                        <div class="border rounded-3 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $detail->item->nama_barang }}</div>
                                    <div class="text-muted small">
                                        Diminta: {{ $detail->qty_requested }} |
                                        Stok tersedia: {{ $detail->item->stocks->sum('qty') }}
                                    </div>
                                </div>
                                <div class="form-check text-nowrap">
                                    <input type="checkbox" class="form-check-input setujui-semua"
                                           data-target="#approvedQty{{ $detail->id }}"
                                           data-jumlah="{{ $detail->qty_requested }}"
                                           id="check{{ $detail->id }}">
                                    <label class="form-check-label small" for="check{{ $detail->id }}">Sesuai</label>
                                </div>
                            </div>

                            <input type="number"
                                   name="approved_qty[{{ $detail->id }}]"
                                   id="approvedQty{{ $detail->id }}"
                                   class="form-control"
                                   placeholder="Jumlah disetujui"
                                   max="{{ $detail->qty_requested }}"
                                   min="0"
                                   required>
                        </div>
                    @endforeach

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan (opsional)</label>
                        <textarea name="admin_note" class="form-control rounded-3" rows="3"
                                  placeholder="Contoh: dipenuhi sebagian karena stok terbatas."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-1"></i> Setujui Pengajuan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

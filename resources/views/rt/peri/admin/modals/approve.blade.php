@php($no = str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT))

<div class="modal fade" id="approveModal-{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0">
            <form method="POST" action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}">
                @csrf
                <div class="modal-header rounded-top-4">
                    <h5 class="modal-title">Jumlah Barang Disetujui</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @foreach ($pengajuan->details as $detail)
                        <div class="border rounded-3 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $detail->item->nama_barang }}</div>
                                    <div class="text-muted small">
                                        Diminta: {{ $detail->qty_requested }} |
                                        Stok tersedia: {{ $detail->item->stocks->qty ?? 0 }}
                                    </div>
                                </div>
                                <div class="form-check text-nowrap">
                                    <input type="checkbox" class="form-check-input setujui-semua"
                                        data-target="#approvedQty{{ $detail->id }}"
                                        data-jumlah="{{ $detail->qty_requested }}" id="check{{ $detail->id }}">
                                    <label class="form-check-label small" for="check{{ $detail->id }}">Sesuai</label>
                                </div>
                            </div>

                            <input type="number" name="approved_qty[{{ $detail->id }}]"
                                id="approvedQty{{ $detail->id }}" class="form-control" placeholder="Jumlah disetujui"
                                max="{{ $detail->qty_requested }}" min="0" required>
                        </div>
                    @endforeach
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Keterangan (opsional)</label>
                        <textarea name="admin_note" class="form-control rounded-3" rows="3"
                            placeholder="Contoh: dipenuhi sebagian karena stok terbatas."></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-md btn-primary">Setujui</button>
                    <button type="button" class="btn btn-md btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/peri/pengajuan.js') }}"></script>
@endpush

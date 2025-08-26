@props(['pengajuan'])

<div class="modal fade" id="detailTransaksiModal-{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 p-3">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pengajuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-unstyled fs-6">
          <li class="mb-3">
            <strong><i class="ri-checkbox-circle-line me-1"></i> Pengajuan Diajukan</strong><br>
            {{ \Carbon\Carbon::parse($pengajuan->created_at)->format('H:i:s, d M Y') }}
          </li>

          @if(in_array($pengajuan->status, ['approved', 'delivered', 'received']) && $pengajuan->itemDelivery)
          <li class="mb-3">
            <strong><i class="ri-box-3-fill text-primary me-1"></i> Disetujui</strong><br>
            {{ \Carbon\Carbon::parse($pengajuan->itemDelivery->created_at)->format('H:i:s, d M Y') }}
          </li>
          @endif

          @if($pengajuan->tanggal_pengiriman)
          <li class="mb-3">
            <strong><i class="ri-calendar-event-fill text-info me-1"></i> Dikirimkan sesuai jadwal pengiriman</strong><br>
            {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengiriman)->format('H:i, d M Y') }}
          </li>
          @endif

          @if($pengajuan->itemDelivery && $pengajuan->itemDelivery->status === 'in_progress')
          <li class="mb-3">
            <strong><i class="ri-truck-fill text-warning me-1"></i> Dalam Pengiriman</strong><br>
            Sedang diantar oleh <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Staff tidak ditemukan' }}</strong><br>
            {{ \Carbon\Carbon::parse($pengajuan->itemDelivery->tanggal_kirim)->format('H:i:s, d M Y') }}
          </li>
          @endif

          @if($pengajuan->status === 'received' && $pengajuan->itemDelivery && $pengajuan->itemDelivery->bukti_foto)
          <li class="mb-3">
            <strong><i class="ri-download-2-fill text-success me-1"></i> Diterima</strong><br>
            <a href="#" data-bs-toggle="modal" data-bs-target="#buktiModal-{{ $pengajuan->id }}">
              Lihat Bukti Pengiriman
            </a><br>
            Diantar oleh <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Staff tidak ditemukan' }}</strong><br>
            {{ \Carbon\Carbon::parse($pengajuan->itemDelivery->updated_at)->format('H:i:s, d M Y') }}
          </li>
          @endif

          @if($pengajuan->status == 'rejected')
          <li class="mb-3 text-danger">
            <strong><i class="ri-close-circle-fill text-danger me-1"></i> Ditolak</strong><br>
            {{ $pengajuan->keterangan ?? 'Tidak ada keterangan' }}<br>
            {{ \Carbon\Carbon::parse($pengajuan->updated_at)->format('H:i:s, d M Y') }}
          </li>
          @endif
        </ul>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <div>
          <a href="{{ route('pengajuan.enota', $pengajuan->id) }}"
             class="btn btn-outline-success btn-sm"
             target="_blank">
             Lihat E-Nota
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="buktiModal-{{ $pengajuan->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title">Bukti Pengiriman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <div class="d-flex justify-content-center">
            <img src="{{ asset('storage/' . $pengajuan->itemDelivery->bukti_foto) }}"
             alt="Bukti Pengiriman"
             class="img-thumbnail"
             style="max-width: 100%; max-height: 300px; object-fit: contain;">
        </div>
      </div>
    </div>
  </div>
</div>

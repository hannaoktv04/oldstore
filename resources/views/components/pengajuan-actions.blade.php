@props(['pengajuan'])

@php
    $status = $pengajuan->status;
@endphp

@if ($status === 'submitted')
    <div class="d-flex justify-content-center justify-content-md-end gap-3">
        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
        data-bs-target="#approveModal-{{ $pengajuan->id }}" title="Setujui">
            <i class="ri-check-line"></i>
        </button>

        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
        data-bs-target="#rejectModal-{{ $pengajuan->id }}" title="Tolak">
            <i class="ri-close-line"></i>
        </button>
    </div>

@elseif ($status === 'approved')
    <div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
        <button onclick="printResi({{ $pengajuan->id }})" class="btn btn-sm btn-outline-primary">
            <i class="ri-printer-line" title="Cetak Resi"></i>
        </button>

        <div id="resi-{{ $pengajuan->id }}" style="display: none;">
            @include('admin.resi', ['pengajuan' => $pengajuan])
        </div>

        @if(!$pengajuan->itemDelivery || !$pengajuan->itemDelivery->staff_pengiriman)
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $pengajuan->id }}">
                <i class="ri-truck-line" title="Assign Staff Pengiriman"></i>
            </button>
        @else
            <small class="text-muted">
                Sudah diassign ke:
                <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Staff tidak ditemukan' }}</strong>
            </small>
        @endif
    </div>

@elseif ($status === 'delivered')
    <div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
        <small class="text-muted">
            Dikirim oleh: <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Tidak diketahui' }}</strong>
        </small>
    </div>

@elseif ($status === 'received')
<button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
    data-bs-target="#detailTransaksiModal-{{ $pengajuan->id }}">
    <i class="ri-more-2-line" title="Cetak Nota"></i>
</button>

@elseif ($status === 'rejected')
    <div class="text-danger small fst-italic mt-1">
        Ditolak: {{ $pengajuan->keterangan ?? 'Tidak ada keterangan' }}
    </div>
@endif

@push('scripts')
<script>
    function printResi(id) {
        const resi = document.getElementById('resi-' + id).innerHTML;

        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';

        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write('<html><head><title>Print Resi</title>');
        doc.write('<style>body{font-family: Arial; font-size: 12px;} table{width:100%; border-collapse:collapse;} th,td{border:1px solid #000; padding:5px;} .qr-img{text-align:center; margin:20px auto; display:block;}</style>');
        doc.write('</head><body>');
        doc.write(resi);
        doc.write('</body></html>');
        doc.close();

        iframe.contentWindow.focus();
        iframe.contentWindow.print();

        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    }
</script>
@endpush

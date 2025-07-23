@props(['pengajuan'])

@php
$status = $pengajuan->status;
@endphp

@if ($status === 'submitted')
<div class="d-flex justify-content-center justify-content-md-end gap-3">
    <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $pengajuan->id }}">
        <i class="bi bi-check-circle fs-5" title="Setujui"></i>
    </a>
    <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $pengajuan->id }}">
        <i class="bi bi-x-circle fs-5" title="Tolak"></i>
    </a>
</div>

@elseif ($status === 'approved')
<div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
    <button onclick="printResi({{ $pengajuan->id }})" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-printer"></i>
    </button>

    @if(!$pengajuan->itemDelivery)
    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
        data-bs-target="#assignModal-{{ $pengajuan->id }}">
        <i class="bi bi-truck" title="Assign Staff Pengiriman"></i>
    </button>
    @endif
</div>

@elseif ($status === 'delivered')
<div class="d-flex justify-content-center justify-content-md-end gap-3 align-items-center">
    <small class="text-muted">
        Dikirim oleh: <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Tidak diketahui' }}</strong>
    </small>
</div>

@elseif ($status === 'received')
<button class="btn btn-sm btn-outline-primary">
    <i class="bi bi-printer" title="Cetak Nota"></i>
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

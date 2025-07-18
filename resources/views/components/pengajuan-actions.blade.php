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
    <div class="d-flex justify-content-center justify-content-md-end gap-3">
        <a href="{{ route('pengajuan.resi', $pengajuan->id) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-qr-code" title="Cetak Resi"></i>
        </a>
    </div>

@elseif ($status === 'received')
    <a href="#', $request->id) }}"
    <a href="#', $request->id) }}"
    <a href="#', $request->id) }}"
       class="btn btn-sm btn-outline-primary">
        <i class="bi bi-printer" title="Cetak Nota"></i>
    </a>

@elseif ($status === 'rejected')
    <div class="text-danger small fst-italic mt-1">
        Ditolak: {{ $pengajuan->keterangan ?? 'Tidak ada keterangan' }}
    </div>
@endif


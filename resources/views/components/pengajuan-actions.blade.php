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
        
        <button onclick="printResi({{ $pengajuan->id }})" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-printer"></i>
        </button>
        <div id="resi-{{ $pengajuan->id }}" style="display: none;">
            @include('admin.resi', ['pengajuan' => $pengajuan])
        </div>



        @if(!$pengajuan->itemDelivery || !$pengajuan->itemDelivery->staff_pengiriman)
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $pengajuan->id }}">
                <i class="bi bi-truck" title="Assign Staff Pengiriman"></i>
            </button>
        @else
            <small class="text-muted">
                Sudah diassign ke: 
                <strong>{{ $pengajuan->itemDelivery->staff->nama ?? 'Staff tidak ditemukan' }}</strong>
            </small>
        @endif
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


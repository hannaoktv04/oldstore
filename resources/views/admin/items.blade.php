@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<style>
    .thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 0.375rem;
    }

    .item-label {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .stok-text {
        font-weight: bold;
        font-size: 1rem;
    }

    .card-item {
        border-radius: 12px;
    }
</style>

<div class="container py-4">
    <h4 class="fw-bold mb-4">Daftar Barang</h4>

    <div class="d-flex flex-column gap-3">
        @foreach ($items as $item)
            <div class="border shadow-sm p-3 card-item">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-4 d-flex align-items-start gap-3">
                        <img src="{{ $item->photo_url }}" class="thumb" alt="{{ $item->nama_barang }}">
                        <div>
                            <div class="item-label fw-bold">
                                {{ $item->category->categori_name ?? 'Kategori Tidak Diketahui' }}
                            </div>
                            <div class="fw-semibold">
                                {{ $item->nama_barang }}
                            </div>
                        </div>
                    </div>

                    <div class="col-4 col-md-4 text-md-center">
                        <div class="item-label">Total Barang Tersedia</div>
                        <div class="stok-text mt-1">{{ number_format($item->total_stok ?? $item->stok_minimum) }}</div>
                    </div>
                    <div class="col-4 col-md-3 text-md-center">
                        <div class="item-label mb-1">Aksi</div>
                        <div class="d-flex justify-content-md-center justify-content-start gap-2">
                            {{-- Edit --}}
                            <a href="{{ route('admin.items.edit', $item->id) }}" class="text-primary" title="Edit">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn p-0 text-danger" title="Hapus">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- TOGGLE --}}
                    <div class="col-4 col-md-1 text-md-center">
                        <div class="item-label mb-1">Status</div>
                        <form action="{{ route('admin.items.toggle', $item->id) }}" method="POST">
                            @csrf
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input"
                                       type="checkbox"
                                       onchange="this.form.submit()"
                                       {{ $item->is_active ? 'checked' : '' }}>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

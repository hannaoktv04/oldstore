@extends('layouts.admin')

@section('title', 'Edit Sesi Stock Opname')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Sesi Stock Opname</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.stock_opname.update', $session->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Periode Bulan</label>
                <input type="text" name="periode_bulan" class="form-control"
                    value="{{ old('periode_bulan', $session->periode_bulan) }}" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control"
                    value="{{ old('tanggal_mulai', $session->tanggal_mulai->format('Y-m-d')) }}" required>
            </div>

            <div class="mb-3">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $session->catatan) }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

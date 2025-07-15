@extends('layouts.admin')
@section('title', 'Mulai Sesi Stock Opname')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Buat Sesi Stock Opname</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.stock_opname.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Periode Bulan</label>
                <input type="text" name="periode_bulan" class="form-control" placeholder="Misal: Juni 2025" required>
            </div>
            <div class="mb-3">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Mulai Sesi</button>
            <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

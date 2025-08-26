@extends('peri::layouts.app')

@section('content')
<div class="container mt-4">
    <h4>{{ $judul }}</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $index => $req)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $req->user->nama ?? '-' }}</td>
                    <td>{{ $req->tanggal_permintaan }}</td>
                    <td><span class="badge bg-info text-dark">{{ $req->status }}</span></td>
                    <td>{{ $req->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

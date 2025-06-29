@extends('layouts.admin')

@section('title', 'Daftar Purchase Order')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">List of Purchase Orders</h3>
        <a href="{{ route('admin.purchase_orders.createPO') }}" class="btn btn-flat btn-primary ">
            <span class="bi bi-plus-lg"> Create New</span>
        </a>
    </div>
    <div class="card-body my-2">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="container-fluid">
            <table class="table table-bordered table-striped">
                <colgroup>
                    <col width="5%">
                    <col width="20%">
                    <col width="20%">
                    <col width="16%">
                    <col width="12%">
                    <col width="10%">
                    <col width="18%">
                </colgroup>
                <thead class="table-dark">
                    <tr">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode PO</th>
                        <th>Jumlah Item</th>
                        <th>Status</th>
                        <th>Aksi</th>
                        <th>Admin</th>
                        </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $index => $po)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($po->tanggal_po)->format('d M Y') }}</td>
                        <td>{{ $po->nomor_po }}</td>
                        <td class="text-end">{{ $po->details->count() }}</td>
                        <td class="text-center">
                            @switch($po->status)
                            @case('draft')
                            <span class="badge bg-secondary">Draft</span>
                            @break

                            @case('submitted')
                            <span class="badge bg-warning text-dark">Submitted</span>
                            @break

                            @case('received')
                            <span class="badge bg-success">Received</span>
                            @break
                            @endswitch
                        </td>
                        <td class="text-center ">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-flat btn-default dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    @if ($po->status === 'draft')
                                    <li>
                                        <form action="{{ route('admin.purchase_orders.submit', $po->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa fa-paper-plane text-primary"></i> Submit
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.purchase_orders.edit', $po->id) }}"><i
                                                class="fa fa-edit text-primary"></i> Edit</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.purchase_orders.showPO', $po->id) }}"><i
                                                class="fa fa-eye text-dark"></i> View</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.purchase_orders.destroy', $po->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item text-danger delete_data"
                                                style="cursor: pointer;">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                    @endif

                                    @if ($po->status === 'submitted')
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.purchase_orders.receive', $po->id) }}"><i
                                                class="fa fa-boxes text-dark"></i> Receive</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.purchase_orders.showPO', $po->id) }}"><i
                                                class="fa fa-eye text-dark"></i> View</a></li>
                                    @endif

                                    @if ($po->status === 'received')
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.purchase_orders.showPO', $po->id) }}"><i
                                                class="fa fa-eye text-dark"></i> View</a></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                        <td class="text-center">{{ $po->creator->nama ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data purchase order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

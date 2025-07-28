@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 mt-4">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->nama_role }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge {{ $user->active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#roleModal{{ $user->id }}">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($users as $user)
<div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <form action="{{ route('admin.users.assign-role', $user->id) }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h5>Edit User Information</h5>
                        <p class="mb-3 text-muted">Updating user details will receive a privacy audit.</p>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-7">
                            <label for="roles_{{ $user->id }}" class="form-label">Roles</label>
                            <select class="form-select select2-roles" name="roles[]" id="roles_{{ $user->id }}"
                                multiple>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->roles->contains('id', $role->id) ? 'selected'
                                    : '' }}>
                                    {{ $role->nama_role }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-5">
                            <label for="status_{{ $user->id }}" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status_{{ $user->id }}">
                                <option value="1" {{ $user->active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$user->active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center border-0">
                    <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
    $('#usersTable').DataTable({
        responsive: true,
         language: {
        search: "Cari:",
        lengthMenu: "_MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data",
        infoFiltered: "",
        zeroRecords: "Data tidak ditemukan",
        paginate: {
            previous: "<",
            next: ">"
        }
    },

        columnDefs: [
            { orderable: false, targets: [4, 5, 6] }
        ]

    });

    $('.select2-roles').select2({
        placeholder: 'Pilih role',
        width: '100%',
        dropdownParent: $('.modal-content')
    });

    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.select2-roles').select2({
            placeholder: 'Pilih role',
            width: '100%',
            dropdownParent: $(this).find('.modal-content')
        });
    });
});
</script>
@endpush

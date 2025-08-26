@extends('peri::layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4 mt-4">
            <div class="card-body">
                <h4>Role Angota</h4>
                <div class="table-responsive-sm">
                    <table class="table" id="usersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $user->nama }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-label-primary">{{ $role->nama_role }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->active ? 'bg-label-success' : 'bg-label-danger' }}">
                                            {{ $user->active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn" data-bs-toggle="modal"
                                            data-bs-target="#roleModal{{ $user->id }}">
                                            <i class="ri-more-2-line"></i>
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

    @foreach ($users as $user)
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
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>
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
                            <button type="submit" class="btn btn-md btn-primary">Update</button>
                            <button type="button" class="btn btn-md btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/peri/users-role.js') }}"></script>
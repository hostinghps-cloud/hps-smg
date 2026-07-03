@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <!-- TITLE -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="fw-bold mb-1">
                    👤 User Master
                </h3>

                <small class="text-muted">
                    Kelola user login system
                </small>

                <br>
                <!-- ADD BUTTON -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">

                    ➕ Add User

                </button>


            </div>



        </div>


        <!-- MODAL ADD -->
        <div class="modal fade" id="addUserModal" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <form action="/user-master/store" method="POST">

                        @csrf

                        <div class="modal-header">

                            <h5 class="modal-title">
                                👤 Tambah User
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <div class="row">

                                <!-- NAME -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Nama User
                                    </label>

                                    <input type="text" name="name" class="form-control" placeholder="Nama User" required>

                                </div>

                                <!-- EMAIL -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input type="email" name="email" class="form-control" placeholder="Email Login"
                                        required>

                                </div>

                                <div class="col-md-6 mb-3">

    <label class="form-label">
        SMTP Password
    </label>

    <input
        type="password"
        name="smtp_password"
        class="form-control"
        placeholder="Password Email">

</div>

<div class="mb-3">
    <label>CC Email</label>

    <textarea
        name="cc"
        class="form-control"
        rows="3"></textarea>

    <small>
        Pisahkan dengan titik koma (;)
    </small>
</div>

                                <!-- ROLE -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Role
                                    </label>

                                    <select name="role" class="form-select" required>

                                        <option value="">
                                            Pilih Role
                                        </option>

                                        <option value="admin">
                                            Admin
                                        </option>

                                        <option value="user">
                                            User
                                        </option>

                                    </select>

                                </div>

                                <!-- PASSWORD -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Password
                                    </label>

                                    <input type="password" name="password" class="form-control" placeholder="Password"
                                        required>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                Close

                            </button>

                            <button class="btn btn-primary">

                                💾 Save User

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>



        <!-- TABLE -->
        <div class="card shadow-sm border-0" style="border-radius:16px;">

            <div class="card-body">

                <h5 class="fw-bold mb-4">
                    📂 List User
                </h5>

                <div style="overflow:auto;">

                    <table class="table table-bordered align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="70">
                                    ID
                                </th>

                                <th>
                                    Nama
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    CC
                                </th>
                                

                                <th width="120">
                                    Role
                                </th>

                                <th width="180">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($users as $item)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $item->name }}
                                    </td>

                                    <td>
                                        {{ $item->email }}
                                    </td>

                                    <td>
                                        {{ $item->cc }}

                                    </td>

                                    <td>

                                        <span class="badge bg-primary">

                                            {{ ucfirst($item->role) }}

                                        </span>

                                    </td>

                                    <td class="d-flex gap-2">

    {{-- MASTER & ADMIN bisa edit semua --}}
    @if(in_array(auth()->user()->role, ['master','admin']))

        <button
            class="btn btn-warning btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#editUser{{ $item->id }}">

            ✏ Edit

        </button>

    {{-- USER hanya bisa edit dirinya sendiri --}}
    @elseif(auth()->user()->role == 'user' && auth()->id() == $item->id)

        <button
            class="btn btn-warning btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#editUser{{ $item->id }}">

            🔑 Password

        </button>

    @endif


    {{-- Hapus hanya Master & Admin --}}
    @if(in_array(auth()->user()->role, ['master','admin']))

        <form
            action="/user-master/delete/{{ $item->id }}"
            method="POST"
            style="display:inline"
            onsubmit="return confirm('Yakin hapus user?')">

            @csrf
            @method('DELETE')

            <button class="btn btn-danger btn-sm">
                🗑 Hapus
            </button>

        </form>

    @endif

</td>



                                <!-- MODAL EDIT -->
                                <div class="modal fade" id="editUser{{ $item->id }}" tabindex="-1">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form action="/user-master/update/{{ $item->id }}" method="POST">

                                                @csrf

                                                <div class="modal-header">

                                                    <h5 class="modal-title">

                                                        ✏ Edit User

                                                    </h5>

                                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                    </button>

                                                </div>

                                                <div class="modal-body">

    @if(auth()->user()->role == 'user')

        <div class="alert alert-info">
            Anda hanya dapat mengubah password akun sendiri.
        </div>

        <div class="mb-3">
            <label>Password Baru</label>
            <input
                type="password"
                name="password"
                class="form-control"
                required>
        </div>

    @else

        <div class="mb-3">

            <label>Nama</label>

            <input
                type="text"
                name="name"
                class="form-control"
                value="{{ $item->name }}"
                required>

        </div>

        <div class="mb-3">

            <label>Email</label>

            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ $item->email }}"
                required>

        </div>

        <div class="mb-3">

    <label>SMTP Password</label>

    <input
        type="password"
        name="smtp_password"
        class="form-control"
        value="{{ $item->smtp_password }}">

</div>

<div class="mb-3">
    <label>CC Email</label>

    <textarea
        name="cc"
        class="form-control"
        rows="3">{{ $item->cc }}</textarea>
</div>
        

        <div class="mb-3">

            <label>Role</label>

            <select name="role" class="form-select">

                <option value="admin"
                    {{ $item->role == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="user"
                    {{ $item->role == 'user' ? 'selected' : '' }}>
                    User
                </option>

            </select>

        </div>

        <div class="mb-3">

            <label>Password Baru</label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Kosongkan jika tidak diubah">

        </div>

    @endif

</div>

                                                <div class="modal-footer">

                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                                        Close

                                                    </button>

                                                    <button class="btn btn-primary">

                                                        💾 Update

                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center text-muted py-4">

                                        Belum ada user

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
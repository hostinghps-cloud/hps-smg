@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <!-- TITLE -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    📬 Email Master
                </h3>

                <small class="text-muted">
                    Mapping company code ke email
                </small>

                <br>
                <!-- ADD BUTTON -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmailModal">

                    ➕ Add Email

                </button>

            </div>


        </div>


        <!-- MODAL ADD -->
        <div class="modal fade" id="addEmailModal" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <form action="/email-master/store" method="POST">

                        @csrf

                        <div class="modal-header">

                            <h5 class="modal-title">
                                📬 Tambah Email
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <div class="row">

                                <div class="col-md-3 mb-3">

                                    <label class="form-label fw-semibold">
                                        Kode Company
                                    </label>

                                    <input type="text" name="kode_company" class="form-control" placeholder="Contoh: 511"
                                        required>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label fw-semibold">
                                        Company Name
                                    </label>

                                    <input type="text" name="company_name" class="form-control" placeholder="Nama Company"
                                        required>

                                </div>

                                <div class="col-md-5 mb-3">

                                    <label class="form-label fw-semibold">
                                        Email
                                    </label>

                                    <textarea name="email" rows="3" class="form-control"
                                        placeholder="email1@mail.com,email2@mail.com" required></textarea>

                                    <small class="text-muted">
                                        Pisahkan multiple email dengan koma
                                    </small>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                Close

                            </button>

                            <button class="btn btn-primary">

                                💾 Save Email

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
                    📂 List Email
                </h5>

                <div style="overflow:auto;">
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="🔍 Cari kode company, nama company, atau email...">
                    </div>

                    <table class="table table-bordered align-middle" id="emailTable">

                        <thead class="table-light">

                            <tr>
                                <th>
                                    No
                                </th>

                                <th width="80">
                                    Kode
                                </th>

                                <th>
                                    Company
                                </th>

                                <th>
                                    Email
                                </th>

                                <th width="170">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($emails as $item)

                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <span class="badge bg-dark">
                                            {{ $item->kode_company }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $item->company_name }}
                                    </td>

                                    <td>
                                        {{ $item->email }}
                                    </td>

                                    <td class="d-flex gap-2">

                                        <!-- EDIT -->
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $item->id }}">

                                            ✏ Edit

                                        </button>

                                        <!-- DELETE -->
                                        @if(auth()->user()->role == 'admin')

                                            <form action="/email-master/delete/{{ $item->id }}" method="POST" style="display:inline"
                                                onsubmit="return confirm('Yakin hapus data?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm">

                                                    🗑 Hapus

                                                </button>

                                            </form>

                                        @endif

                                    </td>

                                </tr>


                                <!-- MODAL EDIT -->
                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form action="/email-master/update/{{ $item->id }}" method="POST">

                                                @csrf

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        ✏ Edit Email
                                                    </h5>

                                                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                    </button>

                                                </div>

                                                <div class="modal-body">

                                                    <div class="mb-3">

                                                        <label>
                                                            Kode Company
                                                        </label>

                                                        <input type="text" name="kode_company" class="form-control"
                                                            value="{{ $item->kode_company }}" required>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label>
                                                            Company Name
                                                        </label>

                                                        <input type="text" name="company_name" class="form-control"
                                                            value="{{ $item->company_name }}" required>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label>
                                                            Email
                                                        </label>

                                                        <textarea name="email" rows="4" class="form-control"
                                                            required>{{ $item->email }}</textarea>

                                                    </div>

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

                                    <td colspan="4" class="text-center text-muted py-4">

                                        Belum ada data email

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
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function () {

            let filter = this.value.toLowerCase();

            let rows = document.querySelectorAll('#emailTable tbody tr');

            rows.forEach(function (row) {

                let text = row.innerText.toLowerCase();

                if (text.indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }

            });

        });
    </script>
@endsection
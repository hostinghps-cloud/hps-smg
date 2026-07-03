@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                📝 Footer Master
            </h3>

            <small class="text-muted">
                Kelola footer email system
            </small>

            <br>

            <button
                class="btn btn-primary mt-2"
                data-bs-toggle="modal"
                data-bs-target="#addFooterModal">

                ➕ Add Footer

            </button>

        </div>

    </div>

    <!-- ADD MODAL -->
    <div class="modal fade"
        id="addFooterModal"
        tabindex="-1">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form
                    action="/footer-master/store"
                    method="POST">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">

                            📝 Tambah Footer

                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">

                                Nama Footer

                            </label>

                            <input
                                type="text"
                                name="footer_name"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Footer HTML

                            </label>

                            <textarea
                                name="footer_html"
                                rows="10"
                                class="form-control"
                                required></textarea>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Close

                        </button>

                        <button
                            class="btn btn-primary">

                            💾 Save Footer

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-sm border-0"
        style="border-radius:16px;">

        <div class="card-body">

            <h5 class="fw-bold mb-4">

                📂 List Footer

            </h5>

            <div style="overflow:auto;">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="80">
                                ID
                            </th>

                            <th>
                                Footer Name
                            </th>

                            <th width="180">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($footers as $item)

                        <tr>

                            <td>
                                {{ $item->id }}
                            </td>

                            <td>
                                {{ $item->footer_name }}
                            </td>

                            <td class="d-flex gap-2">

                                <button
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editFooter{{ $item->id }}">

                                    ✏ Edit

                                </button>

                               @if(auth()->user()->role == 'admin')

<form
    action="/footer-master/delete/{{ $item->id }}"
    method="POST"
    style="display:inline"
    onsubmit="return confirm('Yakin hapus footer?')">

    @csrf
    @method('DELETE')

    <button
        class="btn btn-danger btn-sm">

        🗑 Hapus

    </button>

</form>

@endif

                            </td>

                        </tr>

                        <!-- EDIT MODAL -->

                        <div class="modal fade"
                            id="editFooter{{ $item->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-lg">

                                <div class="modal-content">

                                    <form
                                        action="/footer-master/update/{{ $item->id }}"
                                        method="POST">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">

                                            <h5 class="modal-title">

                                                ✏ Edit Footer

                                            </h5>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label>
                                                    Nama Footer
                                                </label>

                                                <input
                                                    type="text"
                                                    name="footer_name"
                                                    class="form-control"
                                                    value="{{ $item->footer_name }}"
                                                    required>

                                            </div>

                                            <div class="mb-3">

                                                <label>
                                                    Footer HTML
                                                </label>

                                                <textarea
                                                    name="footer_html"
                                                    rows="10"
                                                    class="form-control"
                                                    required>{{ $item->footer_html }}</textarea>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button
                                                type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">

                                                Close

                                            </button>

                                            <button
                                                class="btn btn-primary">

                                                💾 Update

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td colspan="3"
                                class="text-center text-muted py-4">

                                Belum ada footer

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
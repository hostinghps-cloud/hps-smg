@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                📄 Master Template Email
            </h3>

            <small class="text-muted">
                Kelola template email otomatis
            </small>
            <br>
            <!-- ADD -->
            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addTemplateModal">

                ➕ Add Template

            </button>

        </div>



    </div>


    <!-- MODAL ADD -->
    <div
        class="modal fade"
        id="addTemplateModal"
        tabindex="-1">

        <div class="modal-dialog modal-xl">

            <div class="modal-content">

                <form
                    action="/template-master/store"
                    method="POST">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">
                            📄 Tambah Template
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <!-- NAMA -->
                        <div class="mb-3">

                            <label class="form-label">

                                jenis Monitoring

                            </label>

                            <select
                                name="jenis_monitoring"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Monitoring --</option>

                                 <option value="W001-WIP">W001-WIP</option>
                                <option value="W002-TAT14D (Pending case 14 days)">W002-TAT14D (Pending case 14 days)</option>
                                <option value="W003-TAT5D (Pending case 5 days)">W003-TAT5D (Pending case 5 days)</option>
                                <option value="W004-KCI">W004-KCI</option>
                                <option value="W005-Finish Repair">W005-Finish Repair</option>
                                <option value="W006-Rerepair">W006-Rerepair</option>
                                <option value="W007-OLA">W007-OLA</option>
                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Nama Template

                            </label>

                            <input
                                type="text"
                                name="nama_template"
                                class="form-control"
                                placeholder="Contoh: Reminder Repair"
                                required>

                        </div>


                        <!-- SUBJECT -->
                        <div class="mb-3">

                            <label class="form-label">

                                Subject Email

                            </label>

                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                placeholder="Masukkan subject"
                                required>

                        </div>


                        <!-- BODY -->
                        <div class="mb-3">

                            <label class="form-label">

                                Isi Template

                            </label>

                            <textarea
                                name="body"
                                rows="10"
                                class="form-control"
                                placeholder="Isi template email..."
                                required></textarea>

                        </div>


                        <!-- VARIABLE -->
                        <div class="alert alert-info">

                            <strong>
                                Variable tersedia:
                            </strong>

                            <hr>

                            @{{case_id}} → Case ID
                            <br>

                            @{{company_name}} → Nama Company
                            <br>

                            @{{status}} → Status Case

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

                            💾 Save Template

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <!-- LIST -->
    <div
        class="card shadow-sm border-0"
        style="border-radius:16px;">

        <div class="card-body">

            <h5 class="fw-bold mb-4">

                📂 List Template

            </h5>
            <div class="row mb-3">

                <div class="col-md-4">

                    <select
                        id="filterMonitoring"
                        class="form-select">

                        <option value="">
                            Semua Monitoring
                        </option>

                        <option value="W001-WIP">W001-WIP</option>
                        <option value="W002-TAT14D (Pending case 14 days)">W002-TAT14D (Pending case 14 days)</option>
                        <option value="W003-TAT5D (Pending case 5 days)">W003-TAT5D (Pending case 5 days)</option>
                        <option value="W004-KCI">W004-KCI</option>
                        <option value="W005-Finish Repair">W005-Finish Repair</option>
                        <option value="W006-Rerepair">W006-Rerepair</option>
                        <option value="W007-OLA">W007-OLA</option>

                    </select>

                </div>

            </div>

            <div style="overflow:auto;">

                <table
                    class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                ID
                            </th>

                            <th>
                                Jenis Monitoring
                            </th>

                            <th>
                                Nama Template
                            </th>

                            <th>
                                Subject
                            </th>

                            <th width="180">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($templates as $item)

                        <tr data-monitoring="{{ $item->jenis_monitoring }}">

                           <td>
                           {{ $loop->iteration }}
                            </td>

                            <td>

                                <strong>
                                    {{ $item->jenis_monitoring }}
                                </strong>

                            </td>

                            <td>

                                <strong>
                                    {{ $item->nama_template }}
                                </strong>

                            </td>

                            <td>

                                {{ $item->subject }}

                            </td>

                            <td>

                                <div class="d-flex gap-2">

                                    <!-- EDIT -->
                                    <button
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $item->id }}">

                                        ✏ Edit

                                    </button>

                                    <!-- DELETE -->
                                    @if(auth()->user()->role == 'admin')

                                    <form
                                        action="/template-master/delete/{{ $item->id }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin hapus template ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="btn btn-danger btn-sm">

                                            🗑 Hapus

                                        </button>

                                    </form>

                                    @endif

                                </div>

                            </td>

                        </tr>



                        <!-- MODAL EDIT -->
                        <div
                            class="modal fade"
                            id="editModal{{ $item->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-xl">

                                <div class="modal-content">

                                    <form
                                        action="/template-master/update/{{ $item->id }}"
                                        method="POST">

                                        @csrf

                                        <div class="modal-header">

                                            <h5 class="modal-title">

                                                ✏ Edit Template

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

                                                    Jenis Monitoring

                                                </label>

                                                <select
                                                    name="jenis_monitoring"
                                                    class="form-select"
                                                    required>

                                                    						<option value="W001-WIP"
                                                        {{ $item->jenis_monitoring=='W001-WIP'  ? 'selected' : '' }}>
                                                        W001-WIP
                                                    </option>

                                                    <option value="W002-TAT14D (Pending case 14 days)"
                                                        {{ $item->jenis_monitoring=='W002-TAT14D (Pending case 14 days)' ? 'selected' : '' }}>
                                                        W002-TAT14D (Pending case 14 days)
                                                    </option>

                                                    <option value="W003-TAT5D (Pending case 5 days)"
                                                        {{ $item->jenis_monitoring=='W003-TAT5D (Pending case 5 days)' ? 'selected' : '' }}>
                                                        W003-TAT5D (Pending case 5 days)
                                                    </option>

                                                    <option value="W004-KCI"
                                                        {{ $item->jenis_monitoring=='W004-KCI' ? 'selected' : '' }}>
                                                        W004-KCI
                                                    </option>

                                                    <option value="W005-Finish Repair"
                                                        {{ $item->jenis_monitoring=='W005-Finish Repair' ? 'selected' : '' }}>
                                                        W005-Finish Repair
                                                    </option>

                                                    <option value="W006-Rerepair"
                                                        {{ $item->jenis_monitoring=='W006-Rerepair' ? 'selected' : '' }}>
                                                        W006-Rerepair
                                                    </option>

                                                    <option value="W007-OLA"
                                                        {{ $item->jenis_monitoring=='W007-OLA' ? 'selected' : '' }}>
                                                        W007-OLA
                                                    </option>

                                                </select>

                                            </div>

                                            <div class="mb-3">

                                                <label>

                                                    Nama Template

                                                </label>

                                                <input
                                                    type="text"
                                                    name="nama_template"
                                                    class="form-control"
                                                    value="{{ $item->nama_template }}"
                                                    required>

                                            </div>


                                            <div class="mb-3">

                                                <label>

                                                    Subject

                                                </label>

                                                <input
                                                    type="text"
                                                    name="subject"
                                                    class="form-control"
                                                    value="{{ $item->subject }}"
                                                    required>

                                            </div>


                                            <div class="mb-3">

                                                <label>

                                                    Body

                                                </label>

                                                <textarea
                                                    name="body"
                                                    rows="10"
                                                    class="form-control"
                                                    required>{{ $item->body }}</textarea>

                                            </div>


                                            <div class="alert alert-info">

                                                <strong>
                                                    Variable tersedia:
                                                </strong>

                                                <hr>

                                                @{{case_id}}
                                                <br>

                                                @{{company_name}}
                                                <br>

                                                @{{status}}

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

                                                💾 Update Template

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted py-4">

                                Belum ada template

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
    document
        .getElementById('filterMonitoring')
        .addEventListener('change', function() {

            let selected = this.value;

            document
                .querySelectorAll('tbody tr[data-monitoring]')
                .forEach(function(row) {

                    if (
                        selected === '' ||
                        row.dataset.monitoring === selected
                    ) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }

                });

        });
</script>
@endsection
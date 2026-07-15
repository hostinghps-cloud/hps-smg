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

                <!-- ★★★ DIUBAH  (1/5): id="addTemplateForm" baru ditambahkan
                    (untuk hook JS Summernote di bawah). ★★★ -->
                <form
                    id="addTemplateForm"
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
                                <option value="W005-FR (Finish Repair)">W005-FR (Finish Repair)</option>
                                
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

                            <!-- ★★★ DIUBAH  (2/5): textarea ini sekarang ditumpangi Summernote,
                                lihat initSummernote('#body_add') di script bawah. Atribut/nama tidak berubah. ★★★ -->
                            <textarea
                                name="body"
                                id="body_add"
                                class="form-control"
                                placeholder="Isi template email..."></textarea>

                        </div>

                        <!-- VARIABLE -->
                        <div class="alert alert-info">

                            <strong>
                                Variable tersedia (ketik langsung di editor):
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
                        <option value="W005-FR (Finish Repair)">W005-FR (Finish Repair)</option>

                    </select>

                </div>

            </div>

            <div style="overflow:auto;">

                <table
                    class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                No
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

                                    <!-- ★★★ DIUBAH  (3/5): id="editTemplateForm{{ $item->id }}" baru
                                        ditambahkan (untuk hook JS Summernote per baris). ★★★ -->
                                    <form
                                        id="editTemplateForm{{ $item->id }}"
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

                                                    <option value="W005-FR (Finish Repair)"
                                                        {{ $item->jenis_monitoring=='W005-FR (Finish Repair)' ? 'selected' : '' }}>
                                                        W005-FR (Finish Repair)
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

                                                <!-- ★★★ DIUBAH (4/5): textarea ini sekarang ditumpangi
                                                    Summernote via initSummernote() saat modal dibuka. ★★★ -->
                                                <textarea
                                                    name="body"
                                                    id="body_edit_{{ $item->id }}"
                                                    class="form-control">{{ $item->body }}</textarea>

                                            </div>


                                            <div class="alert alert-info">

                                                <strong>
                                                    Variable tersedia (ketik langsung di editor):
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


<!-- ★★★ DIUBAH : script <script src=".../bootstrap@5.3.3/...bundle.min.js"> yang tadinya
    ada di sini DIHAPUS. Ternyata layout (app.blade.php) SUDAH memuat Bootstrap versi 5.0.2
    duluan — jadi Bootstrap ke-load 2x versi beda, dan itu yang bikin semua tombol di dalam
    modal (termasuk toolbar Summernote) tidak merespon klik. Cukup pakai punya layout. ★★★ -->

<!-- ★★★ DIUBAH  (5/5): Seluruh blok mulai dari sini sampai akhir file BARU.
    CDN jQuery + Summernote, fungsi initSummernote(), dan pemanggilannya
    saat modal Add/Edit Template dibuka (shown.bs.modal). ★★★ -->
<!-- SUMMERNOTE RICH TEXT EDITOR (WYSIWYG gratis, sudah ada tombol Table & Code View) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">

<!-- ★★★ DIUBAH : CSS fix — dropdown Summernote (Font Size, Color, dll) sering ketutup/
    ke-clip kalau editornya ada di dalam modal Bootstrap. Paksa z-index tinggi & overflow visible. ★★★ -->
<style>
    .modal .note-editor .dropdown-menu,
    .modal .note-editor .note-dropdown-menu {
        z-index: 3000 !important;
    }
    .modal-body {
        overflow: visible !important;
    }
    .modal .note-editor {
        position: relative;
        z-index: 1;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>

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

<script>
    /*
    |--------------------------------------------------------------------------
    | RICH TEXT EDITOR (SUMMERNOTE) untuk Body Template
    | Dipasang langsung di atas <textarea name="body">, jadi controller &
    | database TIDAK berubah sama sekali. Isi textarea otomatis ter-update
    | tiap kali user mengetik (callback onChange), sehingga saat form
    | disubmit datanya sudah pasti HTML terbaru dari editor.
    |--------------------------------------------------------------------------
    */

    const SUMMERNOTE_TOOLBAR = [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link']],
        ['view', ['fullscreen', 'codeview']]
    ];

    function initSummernote(selector) {

        // hindari inisialisasi dobel kalau modal dibuka berkali-kali
        if ($(selector).next('.note-editor').length) {
            return;
        }

        $(selector).summernote({
            height: 250,
            toolbar: SUMMERNOTE_TOOLBAR,
            callbacks: {
                onChange: function(contents) {
                    $(selector).val(contents);
                }
            }
        });
    }

    // ===== EDITOR ADD TEMPLATE =====
    document.getElementById('addTemplateModal')
        .addEventListener('shown.bs.modal', function() {
            initSummernote('#body_add');
        });

    // ===== EDITOR EDIT TEMPLATE (tiap baris) =====
    document.querySelectorAll('[id^="editModal"]').forEach(function(modalEl) {

        modalEl.addEventListener('shown.bs.modal', function() {

            const textarea = modalEl.querySelector('textarea[name="body"]');

            if (textarea) {
                initSummernote('#' + textarea.id);
            }
        });
    });
</script>

<!-- ★★★ DIUBAH : workaround dropdown Summernote (Font Family, Font Size, Color, dll)
    yang tidak merespon klik di dalam modal. Logic buka/tutupnya ditulis manual di sini,
    TIDAK bergantung ke mekanisme Bootstrap sama sekali, supaya pasti berfungsi. ★★★ -->
<script>
    document.addEventListener('click', function(e) {

        const toggle = e.target.closest('.note-editor .dropdown-toggle');

        if (!toggle) {
            if (!e.target.closest('.note-editor .dropdown-menu')) {
                document.querySelectorAll('.note-editor .dropdown-menu.show')
                    .forEach(function(m) { m.classList.remove('show'); });
            }
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        const menu = toggle.nextElementSibling;

        if (!menu || !menu.classList.contains('dropdown-menu')) {
            return;
        }

        const isOpen = menu.classList.contains('show');

        document.querySelectorAll('.note-editor .dropdown-menu.show')
            .forEach(function(m) { m.classList.remove('show'); });

        if (!isOpen) {
            menu.classList.add('show');
        }

    }, true);
</script>
@endsection
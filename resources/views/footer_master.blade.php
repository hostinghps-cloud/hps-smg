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
                    id="addFooterForm"
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
                                id="footer_html_add"
                                rows="10"
                                class="form-control"></textarea>

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
                                No
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
                                  {{ $loop->iteration }}
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
                                        id="editFooterForm{{ $item->id }}"
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
                                                    id="footer_html_edit_{{ $item->id }}"
                                                    rows="10"
                                                    class="form-control">{{ $item->footer_html }}</textarea>

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

<!-- ★★★ DIUBAH b: <script src=".../bootstrap@5.3.3/...bundle.min.js"> DIHAPUS dari sini.
     Layout (app.blade.php) sudah memuat Bootstrap 5.0.2 duluan, jadi ini duplikat versi beda
     yang menyebabkan semua tombol di dalam modal (termasuk toolbar Summernote) tidak
     merespon klik. Baris ini sudah ada sejak file asli, bukan ditambahkan untuk Summernote. ★★★ -->

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
    /*
    |--------------------------------------------------------------------------
    | RICH TEXT EDITOR (SUMMERNOTE) untuk Footer HTML
    | Dipasang langsung di atas <textarea name="footer_html">, jadi controller &
    | database TIDAK berubah sama sekali. Isi textarea otomatis ter-update
    | tiap kali user mengetik (callback onChange).
    |--------------------------------------------------------------------------
    */

    const SUMMERNOTE_TOOLBAR_FOOTER = [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link']],
        ['view', ['fullscreen', 'codeview']]
    ];

    function initFooterSummernote(selector) {

        // hindari inisialisasi dobel kalau modal dibuka berkali-kali
        if ($(selector).next('.note-editor').length) {
            return;
        }

        $(selector).summernote({
            height: 200,
            toolbar: SUMMERNOTE_TOOLBAR_FOOTER,
            callbacks: {
                onChange: function(contents) {
                    $(selector).val(contents);
                }
            }
        });
    }

    // ===== EDITOR ADD FOOTER =====
    document.getElementById('addFooterModal')
        .addEventListener('shown.bs.modal', function() {
            initFooterSummernote('#footer_html_add');
        });

    // ===== EDITOR EDIT FOOTER (tiap baris) =====
    document.querySelectorAll('div.modal[id^="editFooter"]').forEach(function(modalEl) {

        modalEl.addEventListener('shown.bs.modal', function() {

            const textarea = modalEl.querySelector('textarea[name="footer_html"]');

            if (textarea) {
                initFooterSummernote('#' + textarea.id);
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
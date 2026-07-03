@extends('layouts.app')

@section('content')



<div class="container-fluid">

    <!-- TITLE -->
    <h3 class="fw-bold mb-4">
        📧 BC Email
    </h3>

    <!-- FILTER -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius:16px;">
        <div class="card-body">
            <h5 class="mb-3 fw-semibold">Filter Monitoring</h5>

            <form method="GET" action="/bulk">
                <div class="row g-3 align-items-end">

                    <!-- Jenis Monitoring -->
                    <div class="col-md-3">
                        <label class="form-label">Jenis Monitoring</label>
                        <select
                            name="jenis_upload"
                            id="jenis_upload"
                            class="form-select">
                            <option value="">Pilih Jenis</option>
                            <option value="W001-WIP" {{ request('jenis_upload') == 'W001-WIP' ? 'selected' : '' }}>
                                W001-WIP
                            </option>
                            <option value="W002-TAT14D (Pending case 14 days)" {{ request('jenis_upload') == 'W002-TAT14D (Pending case 14 days)' ? 'selected' : '' }}>
                                W002-TAT14D (Pending case 14 days)
                            </option>

                             <option value="W003-TAT5D (Pending case 5 days)" {{ request('jenis_upload') == 'W003-TAT5D (Pending case 5 days)' ? 'selected' : '' }}>
                                W003-TAT5D (Pending case 5 days)
                            </option>

                            <option value= "W004-KCI" {{ request('jenis_upload') == 'W004-KCI' ? 'selected' : '' }}>
                                W004-KCI
                            </option>

                            <option value="W005-FR (Finish Repair)" {{ request('jenis_upload') == 'W005-FR (Finish Repair)' ? 'selected' : '' }}>
                                W005-FR (Finish Repair)
                            </option>
                            
                          
                        </select>
                    </div>

                    <!-- Batch -->
                    <div class="col-md-3">
                        <label class="form-label">Batch</label>
                        <select
                            name="kode_upload"
                            id="kode_upload"
                            class="form-select">

                            <option value="">Semua Batch</option>

                            @foreach($batches as $batch)
                                <option value="{{ $batch->kode_upload }}">
                                    {{ $batch->kode_upload }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Aging -->
                    <div class="col-md-3">
                        <label class="form-label">Aging</label>

                        <input type="text" name="aging_filter" class="form-control" placeholder="Contoh: >5 atau <14"
                            value="{{ request('aging_filter') }}">
                    </div>

                    <!-- Tombol -->
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Filter
                            </button>

                            <a href="/bulk" class="btn btn-outline-secondary flex-fill">
                                Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- TOTAL SELECTED -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius:16px;">

        <div class="card-body d-flex justify-content-between align-items-center">

            <div>

                <h6 class="mb-1 fw-bold">
                    📌 Total Dipilih
                </h6>

                <small class="text-muted">
                    Jumlah company yang dicentang
                </small>

            </div>

            <div>

                <span id="selectedCount" class="badge bg-primary px-4 py-3" style="font-size:16px;">

                    0

                </span>

            </div>

        </div>

    </div>

    <form method="POST" id="bulkForm" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="aging_filter" value="{{ request('aging_filter') }}">


        <!-- DATA -->
        <div class="card shadow-sm border-0" style="border-radius:16px;">

            <div class="card-body">

                <div style="overflow:auto; max-height:600px;">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light" style="
                                                            position:sticky;
                                                            top:0;
                                                            z-index:1;
                                                        ">

                            <tr>

                                <th width="50">
                                    <input type="checkbox" id="checkAll">
                                </th>

                                <th>
                                    Kode
                                </th>

                                <th>
                                    Company
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Total Case
                                </th>

                                <th width="120">
                                    Preview
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($data as $item)

                            <tr>

                                <!-- CHECKBOX -->
                                <td>

                                    <input type="checkbox" class="bulk-checkbox" name="selected_company[]"
                                        value="{{ $item->kode_company }}">

                                </td>

                                <!-- KODE -->
                                <td>

                                    <span class="badge bg-dark px-3 py-2">

                                        {{ $item->kode_company }}

                                    </span>

                                </td>

                                <!-- COMPANY -->
                                <td>

                                    {{ $item->company_name }}

                                </td>

                                <!-- EMAIL -->
                                <td>

                                    @php

                                    $emails = DB::table('email_masters')
                                    ->where('kode_company', $item->kode_company)
                                    ->pluck('email');

                                    @endphp

                                    @if($emails->count())

                                    @foreach($emails as $email)

                                    <div class="badge bg-success mb-1">

                                        {{ $email }}

                                    </div>

                                    @endforeach

                                    @else

                                    <span class="text-muted">

                                        Belum ada email

                                    </span>

                                    @endif

                                </td>
            
                               
                                   @php

                                    switch (request('jenis_upload')) {

                                        case 'W002-TAT14D (Pending case 14 days)':

                                            $tableName = 'pending_14d';
                                            $fieldCase = 'case_id';
                                            break;

                                        case 'W003-TAT5D (Pending case 5 days)':

                                            $tableName = 'pending';
                                            $fieldCase = 'case_id';
                                            break;

                                        case 'W004-KCI':

                                            $tableName = 'kci';
                                            $fieldCase = 'case_id';
                                            break;

                                        case 'W005-FR (Finish Repair)':

                                            $tableName = 'finish_repair';
                                            $fieldCase = 'case_id';
                                            break;

                                        default:

                                            $tableName = 'wip_datas';
                                            $fieldCase = 'case_id_manual';
                                            break;
                                    }

                                    $totalCaseQuery = DB::table($tableName)
                                        ->where(
                                            DB::raw("LEFT($fieldCase,3)"),
                                            $item->kode_company
                                        )
                                        ->whereNull('sent_at');
                                        if(request()->filled('kode_upload')){
                                        $totalCaseQuery->where(
                                            'kode_upload',
                                            request('kode_upload')
                                        );
                                    }

                                    if (request()->filled('aging_filter')) {

                                        $filter = trim(request('aging_filter'));

                                        if (
                                            preg_match(
                                                '/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/',
                                                $filter,
                                                $match
                                            )
                                        ) {

                                            $totalCaseQuery->where(
                                                'aging',
                                                $match[1],
                                                (float) $match[2]
                                            );
                                        }
                                    }

                                    $totalCase = $totalCaseQuery->count();

                                    @endphp

                            

                                <td class="text-center">
                                    <span class="fw-bold">
                                        {{ $totalCase }}
                                    </span>
                                </td>

                                <!-- PREVIEW -->
                                <td>

                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modal{{ $item->kode_company }}">

                                        👁 Preview

                                    </button>

                                </td>

                            </tr>

                            <!-- MODAL -->
                            <div class="modal fade" id="modal{{ $item->kode_company }}" tabindex="-1"
                                data-bs-backdrop="static" data-bs-keyboard="false">

                                <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                    <div class="modal-content border-0" style="border-radius:18px;">

                                        <!-- HEADER -->
                                        <div class="modal-header bg-dark text-white">

                                            <h5 class="modal-title">

                                                📂 Company Code:
                                                {{ $item->kode_company }}

                                            </h5>

                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <!-- BODY -->
                                        <div class="modal-body">


                                            @php

                                           $caseQuery = DB::table($tableName)
                                                ->where(
                                                    DB::raw("LEFT($fieldCase,3)"),
                                                    $item->kode_company
                                                )
                                                ->whereNull('sent_at');
                                                if(request()->filled('kode_upload')){
                                                $caseQuery->where(
                                                    'kode_upload',
                                                    request('kode_upload')
                                                );
                                            }

                                            /*
                                            |--------------------------------------------------------------------------
                                            | FILTER AGING
                                            |--------------------------------------------------------------------------
                                            */
                                            if (request()->filled('aging_filter')) {

                                                $filter = trim(request('aging_filter'));

                                                if (
                                                    preg_match(
                                                        '/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/',
                                                        $filter,
                                                        $match
                                                    )
                                                ) {

                                                    $caseQuery->where(
                                                        'aging',
                                                        $match[1],
                                                        (float) $match[2]
                                                    );
                                                }
                                            }

                                            $cases = $caseQuery
                                                ->orderByDesc('aging')
                                                ->get([
                                                    $fieldCase,
                                                    'aging',
                                                    'case_status'
                                                ]);

                                            $totalCase = $cases->count();

                                            @endphp


                                                    <!-- TOTAL -->
                                                    <div class="alert alert-primary">

                                                        <strong>Total Case :</strong>
                                                            {{ $totalCase }}
                                                        @if(request('aging_filter'))
                                                        | Filter Aging : {{ request('aging_filter') }}
                                                        @endif

                                                    </div>

                                                    <!-- LIST -->
                                                    <div class="row">

                                                        @forelse($cases as $case)

                                                        <div class="col-md-6 mb-2">

                                                            <div class="border rounded p-2 bg-light">

                                                                <div>
                                                                    <strong>{{ $case->{$fieldCase} }}</strong>
                                                                </div>

                                                                <div>
                                                                    Status :
                                                                    {{ $case->case_status }}
                                                                </div>

                                                                <div>
                                                                    Aging :
                                                                    <span class="badge bg-danger">
                                                                        {{ number_format($case->aging, 1) }}
                                                                    </span>
                                                                </div>

                                                            </div>

                                                        </div>

                                                        @empty

                                                        <div class="col-12">

                                                            <div class="alert alert-warning mb-0">

                                                                Tidak ada case sesuai filter

                                                            </div>

                                                        </div>

                                                        @endforelse

                                                    </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            @empty

                            <tr>

                                <td colspan="5" class="text-center text-muted py-4">

                                    Tidak ada data

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- TEMPLATE EMAIL -->
        <div class="card shadow-sm border-0 mt-4" style="border-radius:16px;">
            <div class="card-body">

                <h5 class="fw-bold mb-4">
                    ✉️ Template Email
                </h5>

                <div class="row g-3 align-items-end">

                    <!-- FILTER MONITORING -->


                    <!-- TEMPLATE -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Pilih Template
                        </label>

                        <select id="templateSelect" name="template_id" class="form-select">

                            <option value="">
                                -- Pilih Template --
                            </option>

                            @foreach($templates as $template)

                            <option value="{{ $template->id }}" data-monitoring="{{ $template->jenis_monitoring }}"
                                data-subject="{{ $template->subject }}" data-body="{{ $template->body }}"
                                data-nama="{{ $template->nama_template }}">

                                {{ $template->nama_template }}

                            </option>

                            @endforeach

                        </select>
                    </div>

                    <!-- EDIT -->
                    <div class="col-md-3">
                        <button type="button" class="btn btn-warning w-100" id="btnEditTemplate">

                            ✏️ Edit Template

                        </button>
                    </div>

                </div>

                <hr class="my-4">

                <!-- footer -->
                <!-- FOOTER MASTER -->
                <div class="row g-3 align-items-end">

                    <!-- FOOTER -->
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Pilih Footer
                        </label>

                        <select
                            id="footerSelect"
                            name="footer_id"
                            class="form-select">

                            <option value="">
                                -- Pilih Footer --
                            </option>

                            @foreach($footers as $footer)

                            <option
                            value="{{ $footer->id }}"
                            data-footer="{{ htmlentities($footer->footer_html) }}"
                            data-name="{{ $footer->footer_name }}"
                            {{ $footer->id == 1 ? 'selected' : '' }}>

                            {{ $footer->footer_name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- EDIT -->
                    <div class="col-md-3">

                        <button
                            type="button"
                            class="btn btn-warning w-100"
                            id="btnEditFooter">

                            ✏️ Edit Footer

                        </button>

                    </div>

                </div>

                <hr class="my-4">

                <div class="mt-3">

                    <label class="form-label fw-semibold">
                        Attachment
                    </label>

                    <input
                        type="file"
                        name="attachments[]"
                        class="form-control"
                        multiple>

                    <small class="text-muted">
                        Bisa upload JPG, PNG, PDF, XLSX, DOCX
                    </small>

                </div>

                <!-- ACTION BUTTON -->
                <div class="d-flex gap-2">

                    <button type="submit" formaction="{{ route('email.preview') }}" formtarget="_blank"
                        class="btn btn-info">

                        👁 Preview Email

                    </button>

                    <button type="submit" formaction="{{ route('email.send') }}" class="btn btn-primary">

                        📧 Kirim Bulk Email

                    </button>

                </div>

            </div>
        </div>
    </form>

    <!-- MODAL EDIT FOOTER -->
    <div class="modal fade"
        id="footerModal"
        tabindex="-1">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form method="POST" id="editFooterForm">

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
                            <label class="form-label">
                                Nama Footer
                            </label>

                            <input
                                type="text"
                                name="footer_name"
                                id="edit_footer_name"
                                class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Isi Footer
                            </label>

                            <textarea
                                name="footer_html"
                                id="edit_footer_html"
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
                            type="submit"
                            class="btn btn-success">
                            💾 Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 🔥 HITUNG TOTAL CHECKBOX
        function updateSelectedCount() {

            let total =
                document.querySelectorAll(
                    '.bulk-checkbox:checked'
                ).length;

            document.getElementById('selectedCount')
                .innerText = total;
        }

        // 🔥 EVENT REALTIME
        document.addEventListener('change', function(e) {

            if (e.target.classList.contains('bulk-checkbox')) {

                updateSelectedCount();

            }

        });
    </script>

    <script>
        document.getElementById('templateSelect')
            .addEventListener('change', function() {

                let selected =
                    this.options[this.selectedIndex];

                let subject =
                    selected.getAttribute('data-subject');

                let body =
                    selected.getAttribute('data-body');

                document.getElementById('emailSubject')
                    .value = subject ?? '';

                document.getElementById('emailBody')
                    .value = body ?? '';

            });

        document.getElementById('checkAll')
            .addEventListener('change', function() {

                document.querySelectorAll('.bulk-checkbox')
                    .forEach(function(cb) {

                        cb.checked = document.getElementById('checkAll').checked;

                    });

                updateSelectedCount();

            });

        document.addEventListener('DOMContentLoaded', function() {

            updateSelectedCount();

        });
    </script>
    <script>
        document.getElementById('bulkForm')
            .addEventListener('submit', function(e) {

                let checked =
                    document.querySelectorAll(
                        '.bulk-checkbox:checked'
                    );

                let template =
                    document.getElementById(
                        'templateSelect'
                    ).value;

                if (checked.length === 0) {

                    e.preventDefault();

                    alert(
                        'Pilih minimal 1 company'
                    );

                    return;
                }

                if (template === '') {

                    e.preventDefault();

                    alert(
                        'Pilih template email'
                    );

                    return;
                }

            });
    </script>


    </form>
    <!-- MODAL EDIT TEMPLATE -->
    <div class="modal fade" id="editTemplateModal" tabindex="-1">

        <div class="modal-dialog modal-xl">

            <div class="modal-content">

                <form method="POST" id="editTemplateForm">

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <h5 class="modal-title">
                            ✏ Edit Template
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Jenis Monitoring
                            </label>

                            <select name="jenis_monitoring" id="edit_jenis_monitoring" class="form-select" required>

                                <option value="">
                                    -- Pilih Jenis Monitoring --
                                </option>

                                <option value="W001-WIP">
                                    W001-WIP
                                </option>

                                <option value="W002-TAT14D (Pending case 14 days)">
                                    W002-TAT14D (Pending case 14 days)
                                </option>

                                <option value="W003-TAT5D (Pending case 5 days)">
                                    W003-TAT5D (Pending case 5 days)
                                </option>

                                <option value="W004-KCI">
                                    W004-KCI
                                </option>

                                <option value="W005-Finish Repair">
                                    W005-Finish Repair
                                </option>

                                <option value="W004-KCI">
                                    W004-KCI
                                </option>

                                <option value="W005-Finish Repair">
                                    W005-Finish Repair
                                </option>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Nama Template
                            </label>

                            <input type="text" name="nama_template" id="edit_nama_template" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Subject
                            </label>

                            <input type="text" name="subject" id="edit_subject" class="form-control">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Body Email
                            </label>

                            <textarea name="body" id="edit_body" rows="15" class="form-control"></textarea>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success">

                            💾 Simpan Perubahan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <script>
        document
            .getElementById('btnEditTemplate')
            .addEventListener('click', function() {

                let select =
                    document.getElementById('templateSelect');

                if (select.value === '') {

                    alert('Pilih template terlebih dahulu');
                    return;

                }

                let option =
                    select.options[select.selectedIndex];

                document.getElementById(
                        'edit_nama_template'
                    ).value =
                    option.dataset.nama;

                document.getElementById(
                        'edit_subject'
                    ).value =
                    option.dataset.subject;

                document.getElementById(
                        'edit_body'
                    ).value =
                    option.dataset.body;

                document.getElementById(
                        'editTemplateForm'
                    ).action =
                    '/template-master/update/' +
                    option.value;

                let modal =
                    new bootstrap.Modal(
                        document.getElementById(
                            'editTemplateModal'
                        )
                    );

                modal.show();

            });
    </script>
    <script>
        document.getElementById('filterMonitoring')
            .addEventListener('change', function() {

                let monitoring = this.value;

                document.querySelectorAll(
                    '#templateSelect option'
                ).forEach(function(option) {

                    if (option.value === '') {
                        option.hidden = false;
                        return;
                    }

                    let jenis =
                        option.dataset.monitoring;

                    option.hidden =
                        monitoring &&
                        jenis !== monitoring;

                });

            });
    </script>
    <script>
        document.getElementById('jenisMonitoring')
            .addEventListener('change', function() {

                let monitoring = this.value;

                let templateSelect =
                    document.getElementById('templateSelect');

                Array.from(templateSelect.options).forEach(function(opt) {

                    if (opt.value === '') {
                        opt.hidden = false;
                        return;
                    }

                    let templateMonitoring =
                        opt.dataset.monitoring;

                    opt.hidden =
                        templateMonitoring !== monitoring;
                });

                templateSelect.value = '';
            });
    </script>

    <script>
        document
            .getElementById('btnEditFooter')
            .addEventListener('click', function() {

                let select =
                    document.getElementById('footerSelect');

                if (select.value === '') {

                    alert('Pilih footer terlebih dahulu');
                    return;
                }

                let option =
                    select.options[select.selectedIndex];

                document.getElementById(
                        'edit_footer_name'
                    ).value =
                    option.dataset.name;

                document.getElementById(
                        'edit_footer_html'
                    ).value =
                    option.dataset.footer;

                document.getElementById(
                        'editFooterForm'
                    ).action =
                    '/footer-master/update/' +
                    option.value;

                let modal =
                    new bootstrap.Modal(
                        document.getElementById(
                            'footerModal'
                        )
                    );

                modal.show();

            });
    </script>
    <script>
        function filterTemplateByMonitoring() {

            let jenisUpload =
                document.getElementById('jenis_upload').value;

            let mapping = {

                'W001-WIP': 'W001-WIP',

                'W002-TAT14D (Pending case 14 days)': 'W002-TAT14D (Pending case 14 days)',

                'W003-TAT5D (Pending case 5 days)': 'W003-TAT5D (Pending case 5 days)',

                'W004-KCI': 'W004-KCI',

                'W005-FR (Finish Repair)': 'W005-FR (Finish Repair)',


            };

            let monitoring =
                mapping[jenisUpload] || '';

            let templateSelect =
                document.getElementById('templateSelect');

            Array.from(templateSelect.options)
                .forEach(function(option) {

                    if (option.value === '') {
                        option.hidden = false;
                        return;
                    }

                    option.hidden =
                        monitoring &&
                        option.dataset.monitoring !== monitoring;

                });

            templateSelect.value = '';

        }

        document
            .getElementById('jenis_upload')
            .addEventListener(
                'change',
                filterTemplateByMonitoring
            );

        document.addEventListener(
            'DOMContentLoaded',
            filterTemplateByMonitoring
        );
    </script>
    <script>
document.getElementById('jenis_upload').addEventListener('change', function () {

    let jenis = this.value;

    fetch('/get-batches?jenis_upload=' + encodeURIComponent(jenis))
        .then(response => response.json())
        .then(data => {

            let batch = document.getElementById('kode_upload');

            batch.innerHTML = '<option value="">Semua Batch</option>';

            data.forEach(function(item){

                batch.innerHTML +=
                    `<option value="${item.kode_upload}">
                        ${item.kode_upload}
                    </option>`;

            });

        });

});
</script>
    @endsection
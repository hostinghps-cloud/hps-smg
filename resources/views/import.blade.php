@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">📤 Upload Data Excel</h3>
            <small class="text-muted">
                Preview seluruh data excel
            </small>
        </div>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm border-0" style="border-radius:18px;">
        <div class="card-body p-4">

            <!-- FORM -->
            <form action="/import" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div>
                    Contoh FORMAT upload file ada
                    <a href="https://drive.google.com/drive/folders/1YeCJJaNSWWEibJPIw-_iHghPxt57wgif?usp=drive_link" target="_blank">
                        disini
                    </a>
                </div>
                <br>

                <div class="row">


                    <!-- JENIS -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">
                            Jenis Monitoring
                        </label>

                        <select name="jenis_upload" class="form-select" id="jenisUpload" required>

                            <option value="">-- Pilih Jenis --</option>

                            <option value="W001-WIP">
                                W001 - WIP
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

                            <option value="W005-FR (Finish Repair)">
                                W005-FR (Finish Repair)
                            </option>



                        </select>
                    </div>

                    <!-- KODE -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">
                            Nama Batch
                        </label>

                        <input type="text" name="kode_upload" id="kodeUpload" class="form-control"
                            placeholder="Contoh: WIP0428" required>


                    </div>

                    <!-- FILE -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">
                            File Excel
                        </label>

                        <input type="file" name="file" class="form-control" id="excelFile" accept=".xlsx,.xls" required>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-3">
                    <button class="btn btn-primary px-4 py-2" style="border-radius:12px;">

                        🚀 Upload & Process

                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- PREVIEW -->
    <div class="card shadow-sm border-0 mt-4" style="border-radius:18px;">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="fw-bold mb-0">
                    📄 Preview Excel
                </h5>

                <small class="text-muted" id="totalCaseText">
                    Total Case : 0
                </small>


            </div>

            <div style="
                                overflow:auto;
                                max-height:500px;
                                border:1px solid #e5e7eb;
                                border-radius:12px;
                            ">

                <table class="table table-bordered table-sm table-hover align-middle" id="previewTable" style="
                                                    white-space:nowrap;
                                                    font-size:12px;
                                                    margin-bottom:0;
                                                ">

                </table>

            </div>

        </div>

    </div>

    <!-- HISTORY -->
    <div class="card shadow-sm border-0 mt-4" style="border-radius:18px;">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                📂 History Upload Batch
            </h5>

            <div style="overflow-x:auto;">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Jenis</th>
                            <th>Batch</th>
                            <th>File</th>
                            <th>Tanggal Upload</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($history as $item)

                        <tr>

                            <td>
                                <span class="badge bg-dark">
                                    {{ $item->jenis_upload }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ $item->kode_upload }}
                                </span>
                            </td>

                            <td>
                                {{ $item->file_name }}
                            </td>

                            <td>
                                {{ $item->created_at }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada upload
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<!-- LOADING -->
<div id="loadingOverlay" style="
                                display:none;
                                position:fixed;
                                top:0;
                                left:0;
                                width:100%;
                                height:100%;
                                background:rgba(0,0,0,0.5);
                                z-index:9999;
                                justify-content:center;
                                align-items:center;
                            ">

    <div style="
                                    background:white;
                                    padding:30px;
                                    border-radius:16px;
                                    text-align:center;
                                    width:300px;
                                ">

        <div class="spinner-border text-primary"></div>

        <h5 class="mt-3">
            Processing Excel...
        </h5>

        <small class="text-muted">
            Mohon tunggu sebentar
        </small>

    </div>

</div>
<style>
    #previewTable th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 2;
    }

    #previewTable td,
    #previewTable th {
        padding: 6px 10px;
    }
</style>

<!-- XLSX -->
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SUCCESS -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('
        success ') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

<!-- AUTO KODE -->
<script>
    document.getElementById('jenisUpload').addEventListener('change', function() {

        let jenis = this.value;

        let tanggal = new Date();

        let bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
        let hari = String(tanggal.getDate()).padStart(2, '0');

        let kode = '';

        switch (jenis) {

            case 'W001-WIP':
                kode = 'WIP';
                break;

            case 'W002-TAT14D (Pending case 14 days)':
                kode = 'TAT14D';
                break;

            case 'W003-TAT5D (Pending case 5 days)':
                kode = 'TAT5D';
                break;

            case 'W004-KCI':
                kode = 'KCI';
                break;

            case 'W005-FR (Finish Repair)':
                kode = 'FR';
                break;

        }

        document.getElementById('kodeUpload').value =
            kode + bulan + hari;

    });
</script>

<!-- PREVIEW EXCEL -->
<!-- PREVIEW EXCEL -->
<script>
    document.getElementById('excelFile')
        .addEventListener('change', function(e) {

            const file = e.target.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function(event) {

                const data = new Uint8Array(event.target.result);

                const workbook = XLSX.read(data, {
                    type: 'array'
                });

                // Cari sheet tanpa membedakan huruf besar/kecil
                const keywords = ['parts', 'part'];

                let sheetName = workbook.SheetNames.find(sheet =>
                    keywords.some(keyword =>
                        sheet.toLowerCase().includes(keyword)
                    )
                );

                // 🔥 JIKA TIDAK ADA → SHEET PERTAMA
                if (!sheetName) {
                    sheetName = workbook.SheetNames[0];
                }

                const sheet = workbook.Sheets[sheetName];

                const excelData = XLSX.utils.sheet_to_json(sheet, {
                    header: 1,
                    defval: ''
                });

                let table = document.getElementById('previewTable');

                table.innerHTML = "";

                // 🔥 JENIS UPLOAD
                let jenisUpload =
                    document.getElementById('jenisUpload').value;


                // 🔥 TIPE MONITORING
                function getMonitoringType(jenisUpload) {

                    if (jenisUpload === 'W001-WIP') {
                        return 'wip';
                    }

                    if (jenisUpload === 'W003-TAT5D (Pending case 5 days)') {
                        return 'pending5d';
                    }

                    if (
                        jenisUpload === 'W002-TAT14D (Pending case 14 days)'
                    ) {
                        return 'pending14d';
                    }

                    if (
                        jenisUpload === 'W004-KCI'
                    ) {
                        return 'kci';
                    }
                    if (
                        jenisUpload === 'W005-FR (Finish Repair)'
                    ) {
                        return 'finishrepair';

                    }

                    return null;
                }

                const type = getMonitoringType(jenisUpload);


                // 🔥 HEADER DINAMIS
                const headerMap = {

                    wip: [
                        "No",
                        "Jenis",
                        "Batch",
                        "Case ID",
                        "Company Name",
                        "Finish Date",
                        "Case Status",
                        "HP Part No",
                        "SO No",
                        "AWB No Part Return",
                        "Part In Date"
                    ],

                    pending5d: [
                        "No",
                        "Jenis",
                        "Batch",
                        "Case ID",
                        "Count",
                        "Received Date",
                        "Start repair date",
                        "Company Name",
                        "Aging",
                        "Customer name",
                        "Customer company Hierarchy",
                        "Case Status",
                        "CE Name",
                        "Company City",
                        "Part name",
                        "HP part no.",
                        "Part Request Date",
                        "SO No",
                        "ETA Date",
                        "Part In Date",
                        "Product No",
                        "Product Name",
                        "Toatal Case >5d"
                    ],

                    pending14d: [
                        "No",
                        "Jenis",
                        "Batch",
                        "Company Name",
                        "Aging",
                        "Case ID",
                        "Received Date",
                        "Start repair date",
                        "Part Request Date",
                        "Part Order Date",
                        "ETA Date",
                        "Part In Date",
                        "Part In Status",
                        "SO No",
                        "HP Part No",
                        "Case Status",
                        "Product Tower",
                        "Product Type",
                        "Vendor part no.",
                        "CE Name"
                    ],
                    kci: [
                        "No",
                        "Jenis",
                        "Batch",
                        "Case ID",
                        "Count",
                        "Company Name",
                        "Aging",
                        "Customer Name",
                        "Customer company Hierarchy - Customer company",
                        "Case Status",
                        "CE Name",
                        "Company City"
                    ],

                    finishrepair: [
                        "No",
                        "Jenis",
                        "Batch",
                        "Case ID",
                        "Count",
                        "Company Name",
                        "Aging",
                        "Customer Name",
                        "Customer company Hierarchy - Customer company",
                        "Case Status",
                        "CE Name",
                        "Company City"
                    ]


                };

                const headers = headerMap[type] || [];
                // 🔥 THEAD
                let thead = document.createElement('thead');

                thead.className = "table-light";

                let trHead = document.createElement('tr');

                headers.forEach(head => {

                    let th = document.createElement('th');

                    th.innerText = head;

                    trHead.appendChild(th);

                });

                thead.appendChild(trHead);

                table.appendChild(thead);

                // 🔥 TBODY
                let tbody = document.createElement('tbody');

                // 🔥 SKIP HEADER EXCEL
                // 🔥 Tentukan baris awal sesuai jenis upload
                const previewRows = (type === 'wip') ?
                    excelData.slice(4) // WIP mulai dari baris Excel ke-4
                    :
                    excelData.slice(1); // Jenis lain tetap mulai dari baris ke-2

                // Total Case
                let totalCase = previewRows.filter(row => row.length > 0).length;

                document.getElementById('totalCaseText').innerText =
                    'Total Case : ' + totalCase;

                // Preview Data
                previewRows.forEach((row, index) => {

                    if (row.length === 0) return;

                    let tr = document.createElement('tr');

                    // 🔥 NOMOR
                    let tdNo = document.createElement('td');
                    tdNo.innerText = index + 1;
                    tdNo.style.fontWeight = 'bold';
                    tr.appendChild(tdNo);

                    // ... kode Anda yang lain tetap ...

                    // 🔥 AMBIL JENIS & BATCH
                    let jenisUpload =
                        document.getElementById('jenisUpload').value;



                    let kodeUpload =
                        document.getElementById('kodeUpload').value;

                    // 🔥 KOLOM JENIS
                    let tdJenis = document.createElement('td');

                    tdJenis.innerText = jenisUpload;

                    tdJenis.style.fontWeight = 'bold';

                    tr.appendChild(tdJenis);

                    // 🔥 KOLOM BATCH
                    let tdBatch = document.createElement('td');

                    tdBatch.innerText = kodeUpload;

                    tdBatch.style.color = '#2563eb';

                    tr.appendChild(tdBatch);

                    // ======================================
                    // PREVIEW PENDING CASE TAT5D
                    // ======================================
                    if (type === 'pending5d') {

                        for (let i = 0; i <= 19; i++) {

                            let td = document.createElement('td');

                            let value = row[i] ?? '';

                            // FORMAT DATE
                            if ([2, 3, 13, 15, 16].includes(i)) {

                                if (typeof value === 'number') {

                                    let date = XLSX.SSF.parse_date_code(value);

                                    if (date) {

                                        value =
                                            String(date.d).padStart(2, '0') + '/' +
                                            String(date.m).padStart(2, '0') + '/' +
                                            date.y;
                                    }
                                }
                            }

                            // AGING
                            if (i === 4) {

                                let aging = parseFloat(
                                    String(value).replace(',', '.')
                                );

                                if (!isNaN(aging) && aging >= 5) {

                                    td.style.backgroundColor = '#fecaca';
                                    td.style.fontWeight = 'bold';
                                }
                            }

                            td.innerText = value;

                            tr.appendChild(td);
                        }

                    } else if (type === 'pending14d') {

                        for (let i = 0; i <= 16; i++) {

                            let td = document.createElement('td');
                            let value = row[i] ?? '';

                            // Kolom tanggal Pending 14D
                            if ([3, 4, 5, 6, 7, 8].includes(i)) {

                                if (typeof value === 'number') {

                                    let date = XLSX.SSF.parse_date_code(value);

                                    if (date) {

                                        value =
                                            String(date.d).padStart(2, '0') + '/' +
                                            String(date.m).padStart(2, '0') + '/' +
                                            date.y;
                                    }
                                }
                            }

                            // Aging ada di kolom index 1
                            if (i === 1) {

                                let aging = parseFloat(
                                    String(value).replace(',', '.')
                                );

                                if (!isNaN(aging) && aging >= 14) {

                                    td.style.backgroundColor = '#fecaca';
                                    td.style.fontWeight = 'bold';
                                }
                            }

                            td.innerText = value;
                            tr.appendChild(td);
                        }
                    } else if (type === 'kci') {

                        // Mapping sesuai HEADER EXCEL
                        const map = [
                            0, // Case ID
                            1, // Count
                            2, // Company Name
                            3, // Aging
                            4, // Customer name
                            5, // Customer company Hierarchy - Customer company
                            6, // Case status
                            7, // CE name
                            8, // Company city

                        ];

                        map.forEach((excelIndex, indexTable) => {

                            let td = document.createElement('td');

                            let value = row[excelIndex] ?? '';



                            // AGING
                            if (indexTable === 3) {

                                let aging = parseFloat(value);

                                if (!isNaN(aging)) {

                                    aging = Math.round(aging);

                                    value = aging;

                                    if (aging >= 5) {

                                        td.style.backgroundColor = '#fecaca';
                                        td.style.fontWeight = 'bold';

                                    }

                                }

                            }

                            td.innerText = value;

                            tr.appendChild(td);

                        });

                    } else if (type === 'finishrepair') {

                        // Mapping sesuai HEADER EXCEL
                        const map = [
                            0, // Case ID
                            1, // Count
                            2, // Company Name
                            3, // Aging
                            4, // Customer name
                            5, // Customer company Hierarchy - Customer company
                            6, // Case status
                            7, // CE name
                            8, // Company city

                        ];

                        map.forEach((excelIndex, indexTable) => {

                            let td = document.createElement('td');

                            let value = row[excelIndex] ?? '';



                            // AGING
                            if (indexTable === 3) {

                                let aging = parseFloat(value);

                                if (!isNaN(aging)) {

                                    aging = Math.round(aging);

                                    value = aging;

                                    if (aging >= 5) {

                                        td.style.backgroundColor = '#fecaca';
                                        td.style.fontWeight = 'bold';

                                    }

                                }

                            }

                            td.innerText = value;

                            tr.appendChild(td);

                        });
                    }
                    // ======================================
                    // PREVIEW WIP
                    // ======================================
                    else if (type === 'wip') {

                        const map = [
                            1, // Case ID
                            2, // Company Name
                            4, // Finish Date
                            6, // Case Status
                            12, // HP Part No
                            24, // SO No
                            28, // AWB No
                            36 // Part In Date
                        ];
                        map.forEach((excelIndex) => {

                            let td = document.createElement('td');
                            let value = row[excelIndex] ?? '';

                            td.innerText = value;
                            tr.appendChild(td);

                        });

                    }

                    tbody.appendChild(tr);

                });

                table.appendChild(tbody);

            };

            reader.readAsArrayBuffer(file);

        });
</script>
<!-- LOADING -->
<script>
    document.getElementById('uploadForm')
        .addEventListener('submit', function() {

            document.getElementById('loadingOverlay')
                .style.display = 'flex';

        });
</script>

@endsection
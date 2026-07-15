@extends('layouts.app')

@section('content')

    <h3 class="fw-bold mb-4">
        📊 Dashboard Email Terkirim
    </h3>

    <div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">📧 Total email terkirim</p>
                <h4 class="fw-bold text-success mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">📅 Terkirim hari ini</p>
                <h4 class="fw-bold mb-0">{{ $stats['hari_ini'] }}</h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">🏢 Company unik</p>
                <h4 class="fw-bold mb-0">{{ $stats['company'] }}</h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">📁 Total case</p>
                <h4 class="fw-bold mb-0">{{ $stats['case'] }}</h4>
            </div>
        </div>
    </div>

</div>

    <!-- ★★★ DIUBAH : statistik per jenis monitoring, ditampilkan sebagai
         gauge lingkaran persentase (CSS conic-gradient, tanpa library JS) ★★★ -->
    <h6 class="fw-bold text-muted mb-3">
        📌 Statistik per Jenis Monitoring
    </h6>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4">

        @foreach($monitoringStats as $m)

        @php
            // Warna gauge mengikuti skema speedometer: hijau -> oranye -> merah
            $percent = $m['percent'];

            if ($percent >= 60) {
                $gaugeColor = '#dc3545'; // merah
            } elseif ($percent >= 30) {
                $gaugeColor = '#fd7e14'; // oranye
            } else {
                $gaugeColor = '#28a745'; // hijau
            }
        @endphp

        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">

                    <p class="text-muted small mb-2">
                        {{ $m['label'] }}
                    </p>

                    <div class="gauge-circle mx-auto mb-2"
                         style="--percent: {{ $percent }}; --gauge-color: {{ $gaugeColor }};">
                        <div class="gauge-inner">
                            <span class="fw-bold">{{ $percent }}%</span>
                        </div>
                    </div>

                    <div class="small text-muted mb-1">
                        {{ $m['total'] }} case
                    </div>

                    <div class="small">

                        <span class="{{ $m['aging_alert'] > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            ⚠ {{ $m['aging_alert'] }} aging tinggi
                        </span>

                        <br>

                        <span class="text-muted">
                            Max aging: {{ $m['max_aging'] ?? '-' }}
                        </span>

                    </div>

                </div>
            </div>
        </div>

        @endforeach

    </div>

    <style>
        .gauge-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: conic-gradient(
                var(--gauge-color) calc(var(--percent) * 1%),
                #e9ecef 0
            );
        }

        .gauge-inner {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }
    </style>

    <div class="card shadow-sm border-0">

        

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Template</th>
                        <th>Kode</th>
                        <th>Company</th>
                        <th>Email Tujuan</th>
                        <th>Total Case</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

@forelse($dashboard as $row)

<tr>
    <td align="center">
        {{ $dashboard->firstItem() + $loop->index }}
    </td>

    <td align="center">{{ $row->template_name }}</td>

    <td>
        <span class="badge bg-dark">
            {{ $row->kode_company }}
        </span>
    </td>

    <td>{{ $row->company_name }}</td>

    <td>{{ $row->recipient }}</td>

    <td>{{ $row->total_case }}</td>

    <td>{{ $row->sent_at }}</td>

    <td>
        <span class="badge bg-success">
            Terkirim
        </span>
    </td>
</tr>

@empty

<tr>
    <td colspan="8" class="text-center">
        Belum ada email terkirim
    </td>
</tr>

@endforelse

</tbody>
            </table>

        </div>

    </div>
<div class="mt-3 d-flex justify-content-center">
    {{ $dashboard->links() }}
</div>
@endsection
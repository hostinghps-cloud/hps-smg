@extends('layouts.app')

@section('content')

    <h3 class="fw-bold mb-4">
        📊 Dashboard Email Terkirim
    </h3>

    
    
    <div class="card shadow-sm border-0">

        <div class="card-header">
            <strong>📧 Riwayat Email Terkirim</strong>
        </div>

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
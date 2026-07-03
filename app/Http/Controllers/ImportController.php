<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MultiSheetImport;
use App\Imports\PnMasterImport;
use App\Models\UploadBatch;
use App\Models\EmailTemplate;
use App\Exports\RawDataExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    // =========================================================
    // 🔥 HALAMAN UPLOAD
    // =========================================================
    public function uploadPage()
    {
        $history = UploadBatch::latest()->paginate(10);

        return view('import', compact('history'));
    }

    // =========================================================
    // 🔥 IMPORT EXCEL
    // =========================================================
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'jenis_upload' => 'required',
            'kode_upload' => 'required'
        ]);
        if ($request->jenis_upload == 'W001-WIP') {
            DB::table('wip_datas')->delete();
        }

        if ($request->jenis_upload == 'W003-TAT5D (Pending case 5 days)') {
            DB::table('pending')->delete();
        }

        if ($request->jenis_upload == '002-PART') {
            DB::table('part_datas')->delete();
        }

        // 🔥 SIMPAN HISTORY / BATCH
        UploadBatch::create([
            'jenis_upload' => $request->jenis_upload,
            'kode_upload' => $request->kode_upload,
            'file_name' => $request->file('file')->getClientOriginalName()
        ]);

        // 🔥 IMPORT SHEET 1
        Excel::import(
            new MultiSheetImport(
                $request->jenis_upload,
                $request->kode_upload
            ),
            $request->file('file')
        );

        return redirect('/upload')
            ->with('success', 'Data berhasil diupload!');
    }

    // 🔥 DASHBOARD (DATA GABUNGAN)
    public function dashboard()
    {
        $dashboard = DB::table('email_logs')
            ->select(
                'kode_company',
                'company_name',
                'template_name',
                'total_case',
                'recipient',
                'sent_at'
            )
            ->orderByDesc('sent_at')
            ->paginate(10); // 10 data per halaman

        return view('dashboard', compact('dashboard'));
    }
    // 🔥 RAW DATA (SUDAH JOIN)
    public function rawData()
    {

        $data = DB::table('raw_data')
            ->leftJoin('pn_master', DB::raw('TRIM(raw_data.case_id)'), '=', DB::raw('TRIM(pn_master.case_id)'))
            ->select(
                'raw_data.*',

                'pn_master.pn_code',
                'pn_master.part_request',
                'pn_master.so_no',
                'pn_master.eta_date',
                'pn_master.partin_date',

                DB::raw('DATEDIFF(NOW(), raw_data.received_date) as tat')
            )
            ->orderByDesc('raw_data.case_id')
            ->paginate(10);

        return view('raw_data', compact('data'));
    }
    public function previewEmail()
    {
        $data = DB::table('raw_data')
            ->leftJoin(
                'pn_master',
                DB::raw('TRIM(raw_data.case_id)'),
                '=',
                DB::raw('TRIM(pn_master.case_id)')
            )
            ->select(

                'raw_data.case_id',
                'raw_data.received_date',
                'raw_data.start_repair_date',
                'raw_data.company_name',
                'raw_data.case_status',
                'raw_data.company_city',
                'raw_data.product_no',
                'raw_data.product_name',
                'raw_data.part_in_date',

                'pn_master.pn_code',
                'pn_master.part_request',
                'pn_master.so_no',
                'pn_master.eta_date',
                'pn_master.part_in_date',

                DB::raw('DATEDIFF(NOW(), raw_data.received_date) as tat')

            )
            ->orderByDesc('raw_data.case_id')
            ->get();

        // 🔥 FILTER
        $fast = $data->where('tat', '<=', 5);

        $slow = $data->where('tat', '>', 5);

        return view(
            'email.report',
            compact('fast', 'slow')
        );
    }

    // 🔥 PN MASTER
    public function pnMaster()
    {
        $data = DB::table('pn_master')->paginate(10);
        return view('pn_master', compact('data'));
    }
    public function filterPrefix(Request $request)
    {
        $prefix = $request->prefix;

        $query = DB::table('raw_data')
            ->leftJoin('pn_master', 'raw_data.case_id', '=', 'pn_master.case_id');

        if ($prefix) {
            $query->where('raw_data.case_id', 'like', $prefix . '%');
        }

        $data = $query->select(
            'raw_data.case_id',
            'raw_data.received_date',
            'raw_data.start_repair_date',
            'raw_data.company_name',
            'raw_data.case_status',
            'raw_data.company_city',
            'raw_data.product_no',
            'raw_data.product_name',
            'raw_data.tat_case',


            'pn_master.pn_code',
            'pn_master.part_request',
            'pn_master.so_no',
            'pn_master.eta_date',
            'pn_master.partin_date'
        )
            ->orderByDesc('raw_data.case_id')
            ->paginate(10)
            ->appends(['prefix' => $prefix]);

        $total = $data->total(); // 🔥 ganti count()

        return view('raw_data', compact('data', 'total', 'prefix'));
    }
    public function exportPrefix(Request $request)
    {
        $prefix = $request->prefix;

        $data = DB::table('raw_data')
            ->leftJoin('pn_master', 'raw_data.case_id', '=', 'pn_master.case_id')
            ->where('raw_data.case_id', 'like', $prefix . '%')
            ->select(
                'raw_data.case_id',
                'raw_data.company_name',
                'raw_data.product_name',

                'pn_master.pn_code',
                'pn_master.part_request',
                'pn_master.so_no',
                'pn_master.eta_date',
                'pn_master.partin_date'
            )
            ->get();

        return Excel::download(new RawDataExport($data), 'export_' . $prefix . '.xlsx');
    }

    public function bulk(Request $request)
    {
       $batches = DB::table('upload_batches')
        ->when($request->filled('jenis_upload'), function ($query) use ($request) {
            $query->where('jenis_upload', $request->jenis_upload);
        })
        ->select('kode_upload', DB::raw('MAX(id) as last_id'))
        ->groupBy('kode_upload')
        ->orderByDesc('last_id')
        ->limit(1)
        ->get();

    $templates = EmailTemplate::latest()->get();

    $footers = DB::table('footer_masters')
        ->orderBy('footer_name')
        ->get();

    $data = collect();

        $templates = EmailTemplate::latest()->get();

        $footers = DB::table('footer_masters')
            ->orderBy('footer_name')
            ->get();

        $data = collect();

        // ==========================
        // WIP
        // ==========================
        if ($request->jenis_upload == 'W001-WIP') {

            $query = DB::table('wip_datas')
                ->select(
                    DB::raw('LEFT(case_id_manual,3) as kode_company'),
                    'company_name'
                )
                ->whereNull('sent_at');

            // FILTER BATCH
            if ($request->kode_upload) {

                $query->where(
                    'kode_upload',
                    $request->kode_upload
                );
            }

            // FILTER KODE COMPANY
            if ($request->kode_company) {

                $query->where(
                    DB::raw('LEFT(case_id_manual,3)'),
                    'like',
                    '%' . $request->kode_company . '%'
                );
            }

            // FILTER CASE ID
            if ($request->case_id) {

                $query->where(
                    'case_id_manual',
                    'like',
                    '%' . $request->case_id . '%'
                );
            }

            // FILTER AGING
            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+)$/', $filter, $match)) {

                    $query->where(
                        'aging',
                        $match[1],
                        (int) $match[2]
                    );
                }
            }

            $data = $query
                ->groupBy(
                    DB::raw('LEFT(case_id_manual,3)'),
                    'company_name'
                )
                ->get();
        }

        // ==========================
        // Pending case 5days
        // ==========================
        elseif ($request->jenis_upload == 'W003-TAT5D (Pending case 5 days)') {

            $query = DB::table('pending')
                ->select(
                    DB::raw('LEFT(case_id,3) as kode_company'),
                    'company_name'
                )
                ->whereNull('sent_at')
                ->groupBy(
                    DB::raw('LEFT(case_id,3)'),
                    'company_name'
                );

            // FILTER BATCH
            if ($request->kode_upload) {

                $query->where(
                    'kode_upload',
                    $request->kode_upload
                );
            }

            // FILTER KODE COMPANY
            if ($request->kode_company) {

                $query->where(
                    DB::raw('LEFT(case_id,3)'),
                    'like',
                    '%' . $request->kode_company . '%'
                );
            }

            // FILTER CASE ID
            if ($request->case_id) {

                $query->where(
                    'case_id_manual',
                    'like',
                    '%' . $request->case_id . '%'
                );
            }

            // FILTER AGING
            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+)$/', $filter, $match)) {

                    $query->where(
                        'aging',
                        $match[1],
                        (int) $match[2]
                    );
                }
            }


            $data = $query
                ->groupBy(
                    DB::raw('LEFT(case_id,3)'),
                    'company_name'
                )
                ->get();
        }

        // ==========================
        // Pending case 14days
        // ==========================
        elseif ($request->jenis_upload == 'W002-TAT14D (Pending case 14 days)') {

            $query = DB::table('pending_14d')
                ->select(
                    DB::raw('LEFT(case_id,3) as kode_company'),
                    'company_name'
                )
                ->whereNull('sent_at')
                ->groupBy(
                    DB::raw('LEFT(case_id,3)'),
                    'company_name'
                );

            // FILTER BATCH
            if ($request->kode_upload) {

                $query->where(
                    'kode_upload',
                    $request->kode_upload
                );
            }

            // FILTER KODE COMPANY
            if ($request->kode_company) {

                $query->where(
                    DB::raw('LEFT(case_id,3)'),
                    'like',
                    '%' . $request->kode_company . '%'
                );
            }

            // FILTER CASE ID
            if ($request->case_id) {

                $query->where(
                    'case_id',
                    'like',
                    '%' . $request->case_id . '%'
                );
            }

            // FILTER AGING
            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+)$/', $filter, $match)) {

                    $query->where(
                        'aging',
                        $match[1],
                        (int) $match[2]
                    );
                }
            }


            $data = $query->get();
        }
        return view('bulk', compact(
            'data',
            'batches',
            'templates',
            'footers'
        ));
    }

public function getBatches(Request $request)
{
    $batch = DB::table('upload_batches')
        ->where('jenis_upload', $request->jenis_upload)
        ->latest('id')
        ->first();

    return response()->json(
        $batch ? [$batch] : []
    );
}

    public function deleteTemplate($id)
    {
        \App\Models\EmailTemplate::find($id)->delete();

        return back()->with(
            'success',
            'Template berhasil dihapus'
        );
    }
    public function updateTemplate(Request $request, $id)
    {
        \App\Models\EmailTemplate::find($id)
            ->update([

                'nama_template' =>
                    $request->nama_template,

                'subject' =>
                    $request->subject,

                'body' =>
                    $request->body

            ]);

        return back()->with(
            'success',
            'Template berhasil diupdate'
        );
    }
}

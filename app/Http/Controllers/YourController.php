<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YourController extends Controller
{
    public function previewEmail()
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
            ->get();

        // 🔥 BAGI DATA
        $fast = $data->where('tat', '<=', 5);
        $slow = $data->where('tat', '>', 5);

        // 🔥 TAMPILKAN EMAIL TEMPLATE
        return view('email.report', compact('fast', 'slow'));
    }
}
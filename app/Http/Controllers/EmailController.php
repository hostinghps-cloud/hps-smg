<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Blade;
use App\Models\EmailTemplate;

class EmailController extends Controller
{
    public function preview(Request $request)
    {
        $selectedCompanies = $request->selected_company ?? [];
        $template = EmailTemplate::find($request->template_id);

        if (!$template) {
            return 'Template belum dipilih';
        }

        $allPreview = '';

        foreach ($selectedCompanies as $kodeCompany) {
            $query = DB::table('wip_datas')
                ->select(
                    'case_id_manual',
                    'company_name',
                    'finish_date',
                    'case_status',
                    'hp_part_no',
                    'so_no',
                    'awb_no_part_return',
                    'part_in_date',
                    'aging',
                    'created_at'
                )
                ->where(DB::raw('LEFT(case_id_manual,3)'), $kodeCompany)
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);
                // Mendukung desimal
                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $query->where('aging', $match[1], (float) $match[2]);
                }
            }

            $rows = $query->orderByDesc('aging')->get()->map(function ($row) {
                $row->aging = number_format($row->aging, 1);
                return $row;
            });



            // PENDING CASE 5D
            $pendingQuery = DB::table('pending')
                ->select(
                    'case_id',
                    'company_name',
                    'received_date',
                    'start_repair_date',
                    'case_status',
                    'ce_name',
                    'company_city',
                    'part_name',
                    'aging',
                    'hp_part_no',
                    'part_request_date',
                    'so_no',
                    'eta_date',
                    'part_in_date',
                    'product_name',
                    'product_no'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING PENDING 5D
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $pendingQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $pendingCase5dRows = $pendingQuery
                ->orderByDesc('aging')
                ->get()
                ->map(function ($row) {
                    $row->aging = number_format($row->aging, 1);
                    return $row;
                });
            $emailMaster = DB::table('email_masters')
                ->where('kode_company', $kodeCompany)
                ->first();

            // PENDING CASE 14D
            $pending14dQuery = DB::table('pending_14d')
                ->select(

                    'company_name',
                    'aging',
                    'case_id',
                    'received_date',
                    'part_request_date',
                    'eta_date',
                    'part_in_date',
                    'so_no',
                    'hp_part_no',
                    'case_status',
                    'ce_name'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING PENDING 14D
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $pending14dQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $pendingCase14dRows = $pending14dQuery
                ->orderByDesc('aging')
                ->get()
                ->map(function ($row) {
                    $row->aging = number_format($row->aging, 1);
                    return $row;
                });


            $companyName = $emailMaster->company_name
                ?? $kodeCompany;

            $pendingBelow5Rows = $pendingCase5dRows->filter(function ($row) {
                return (float) $row->aging < 5;
            });

            $pendingAbove5Rows = $pendingCase5dRows->filter(function ($row) {
                return (float) $row->aging >= 5;
            });
            $pendingAbove14Rows = $pendingCase14dRows;

            $footer = DB::table('footer_masters')->where('id', $request->footer_id)->first();
            $body = $template->body;

            $footerHtml = nl2br($footer->footer_html ?? '');

            $html = Blade::render(
                $body,
                [
                    'rows' => $rows,
                    'pendingBelow5Rows' => $pendingBelow5Rows,
                    'pendingAbove5Rows' => $pendingAbove5Rows,
                    'pendingAbove14Rows' => $pendingAbove14Rows,
                    'showTable' => true,
                    'footer' => $footerHtml,
                ]
            );

            // 1. Buat pengecekan jenis monitoring (Tidak case-sensitive)
            $jenisMonitoring = strtolower(trim($template->jenis_monitoring ?? ''));

            $isWip = str_contains($jenisMonitoring, 'wip');

            $isPending5d =
                str_contains($jenisMonitoring, '5day') ||
                str_contains($jenisMonitoring, 'tat5');

            $isPending14d =
                str_contains($jenisMonitoring, '14day') ||
                str_contains($jenisMonitoring, 'tat14');
            // 2. Siapkan array untuk menampung teks
            $totalArray = [];

            if ($isWip) {
                $totalArray[] = 'Total Case WIP : ' . $rows->count();
            }

            if ($isPending5d) {
                $totalArray[] = 'Total Pending 5D : ' . $pendingCase5dRows->count();
            }

            if ($isPending14d) {
                $totalArray[] = 'Total Pending 14D : ' . $pendingCase14dRows->count();
            }

            // 3. Gabungkan teks (Jika namanya tidak mengandung kata wip/pending, tampilkan keduanya sebagai fallback)
            if (empty($totalArray)) {
                $totalText =
                    'Total Case WIP : ' . $rows->count() .
                    ' | Total Pending 5D : ' . $pendingCase5dRows->count() .
                    ' | Total Pending 14D : ' . $pendingCase14dRows->count();
            } else {
                $totalText = implode(' | ', $totalArray);
            }

            // 4. Masukkan $totalText ke dalam HTML allPreview
            $allPreview .= '
<div style="
    margin-bottom:25px;
    background:#ffffff;
    border:1px solid #dee2e6;
    border-radius:12px;
    overflow:hidden;
">

    <div style="
        background:#0d6efd;
        color:#ffffff;
        padding:15px 20px;
    ">
        <div style="
            font-size:22px;
            font-weight:600;
            line-height:1.3;
        ">
            ' . e($companyName) . '
        </div>

        <div style="
            font-size:13px;
            opacity:0.9;
            margin-top:3px;
        ">
            Company Code : ' . e($kodeCompany) . '
        </div>
    </div>

    <div style="padding:15px;">

        <div style="
            background:#f8f9fa;
            border-left:4px solid #0d6efd;
            padding:10px 15px;
            border-radius:6px;
            margin-bottom:10px;
        ">
            <strong>' . $totalText . '</strong>
        </div>

        <div style="
            margin-bottom:10px;
            font-size:14px;
        ">
            <strong>Subject :</strong>
            <span style="color:#495057;">
                ' . e($template->subject) . '
            </span>
        </div>

        <div style="
            margin:0;
            padding:0;
        ">
            ' . $html . '
        </div>

    </div>

</div>
';
        }

        return response($allPreview);
    }

    public function send(Request $request)
    {
        $selectedCompanies = $request->selected_company ?? [];
        $template = EmailTemplate::find($request->template_id);

        if (!$template) {
            return back()->with('error', 'Template belum dipilih');
        }

        $footer = DB::table('footer_masters')->where('id', $request->footer_id)->first();

        foreach ($selectedCompanies as $kodeCompany) {
            $emailMaster = DB::table('email_masters')
                ->where('kode_company', $kodeCompany)
                ->first();

            if (!$emailMaster) {
                continue;
            }

            $emails = explode(',', $emailMaster->email);

            // 1. Ambil Data WIP (Tambahkan whereNull agar konsisten dengan preview)
            $query = DB::table('wip_datas')
                ->where('case_id_manual', 'like', $kodeCompany . '%')
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);
                // Disamakan regex-nya dengan preview agar mendukung desimal
                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $query->where('aging', $match[1], (float) $match[2]);
                }
            }

            $rows = $query->orderByDesc('aging')->get()->map(function ($row) {
                $row->aging = number_format($row->aging, 1);
                return $row;
            });

            // 2. Ambil Data Pending Case 5D (Dipindahkan ke ATAS sebelum penentuan $companyName)
            $pendingQuery = DB::table('pending')
                ->select(
                    'case_id',
                    'company_name',
                    'received_date',
                    'start_repair_date',
                    'case_status',
                    'ce_name',
                    'company_city',
                    'part_name',
                    'aging',
                    'hp_part_no',
                    'part_request_date',
                    'so_no',
                    'eta_date',
                    'part_in_date',
                    'product_no',
                    'product_name'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING PENDING 5D
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $pendingQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $pending14dQuery = DB::table('pending_14d')
                ->select(
                    'company_name',
                    'aging',
                    'case_id',
                    'received_date',
                    'part_request_date',
                    'eta_date',
                    'part_in_date',
                    'so_no',
                    'hp_part_no',
                    'case_status',
                    'ce_name'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $pending14dQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $pendingCase14dRows = $pending14dQuery
                ->orderByDesc('aging')
                ->get()
                ->map(function ($row) {
                    $row->aging = number_format($row->aging, 1);
                    return $row;
                });

            $pendingAbove14Rows = $pendingCase14dRows->filter(function ($row) {
                return (float) $row->aging >= 14;
            });

            $pendingCase5dRows = $pendingQuery
                ->orderByDesc('aging')
                ->get()
                ->map(function ($row) {
                    $row->aging = number_format($row->aging, 1);
                    return $row;
                });

            $pendingBelow5Rows = $pendingCase5dRows->filter(function ($row) {
                return (float) $row->aging < 5;
            });

            $pendingAbove5Rows = $pendingCase5dRows->filter(function ($row) {
                return (float) $row->aging >= 5;
            });

            // Sekarang aman, tidak akan menimbulkan Undefined Variable
            $companyName = $emailMaster->company_name;


            // SUBJECT DINAMIS
            $subject = str_replace(
                ['{{tanggal_kirim}}', '{{company_name}}'],
                [
                    now()->format('d/m/Y'),
                    $companyName
                ],
                $template->subject
            );
            $body = $template->body;
            $html = Blade::render(
                $body,
                [
                    'rows' => $rows,
                    'pendingBelow5Rows' => $pendingBelow5Rows,
                    'pendingAbove5Rows' => $pendingAbove5Rows,
                    'pendingAbove14Rows' => $pendingAbove14Rows,
                    'showTable' => true,
                    'footer' => nl2br($footer->footer_html ?? ''),
                ]
            );

            $ccEmails = [];

            if (!empty(auth()->user()->cc)) {

                $ccEmails = array_map(
                    'trim',
                    explode(';', auth()->user()->cc)
                );
            }

            $bccEmails = [];

            $attachments = $request->file('attachments');

            $user = auth()->user();

            config([
                'mail.mailers.smtp.host' => 'mail.harmoniputra.com',
                'mail.mailers.smtp.port' => 465,
                'mail.mailers.smtp.encryption' => 'ssl',

                'mail.mailers.smtp.username' => $user->email,
                'mail.mailers.smtp.password' => $user->smtp_password,

                'mail.from.address' => $user->email,
                'mail.from.name' => 'PT. Harmoni Putra Solusindo',
            ]);

            Mail::purge('smtp');

            Mail::html($html, function ($message) use ($emails, $ccEmails, $subject, $attachments, $bccEmails) {
                $message
                    ->from(
                        auth()->user()->email,
                        'PT. Harmoni Putra Solusindo'
                    )
                    ->to($emails)
                    ->cc($ccEmails)
                    ->bcc($bccEmails)
                    ->subject($subject);

                if ($attachments) {
                    foreach ($attachments as $file) {
                        $message->attach($file->getRealPath(), [
                            'as' => $file->getClientOriginalName()
                        ]);
                    }
                }
            });

            // UPDATE WIP
            $updateWipQuery = DB::table('wip_datas')
                ->where('case_id_manual', 'like', $kodeCompany . '%')
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (
                    preg_match(
                        '/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/',
                        $filter,
                        $match
                    )
                ) {

                    $updateWipQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $updateWipQuery->update([
                'sent_at' => now(),
            ]);


            // UPDATE PENDING
            $updatePendingQuery = DB::table('pending')
                ->where('case_id', 'like', $kodeCompany . '%')
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (
                    preg_match(
                        '/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/',
                        $filter,
                        $match
                    )
                ) {

                    $updatePendingQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $updatePendingQuery->update([
                'sent_at' => now(),
            ]);
            // UPDATE PENDING 14D
            $updatePending14Query = DB::table('pending_14d')
                ->where('case_id', 'like', $kodeCompany . '%')
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {

                    $updatePending14Query->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $updatePending14Query->update([
                'sent_at' => now(),
            ]);
            // 4. Simpan log email
            DB::table('email_logs')->insert([
                'kode_company' => $kodeCompany,
                'template_name' => $template->nama_template,
                'subject' => $subject,
                'company_name' => $companyName,
                'recipient' => implode(', ', $emails),
                'total_case' =>
                $rows->count() +
                    $pendingCase5dRows->count(),
                'aging_filter' => $request->aging_filter,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }
        return back()->with('success', 'Email berhasil dikirim');
    }
}

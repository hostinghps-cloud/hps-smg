<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
// ★★★ DIUBAH: "use Illuminate\Support\Facades\Blade;" DIHAPUS dari sini.
// Sebelumnya dipakai untuk Blade::render($body, [...]) yang menyebabkan ParseError
// kalau body (dari WYSIWYG) mengandung karakter "&", "<", ">" hasil encode HTML.

class EmailController extends Controller
{
    // ★★★ DIUBAH : 3 method baru di bawah ini (sampai method injectCaseTables)
    // menggantikan Blade::render() dengan generator tabel HTML pakai PHP biasa. ★★★

    /**
     * Render satu tabel case menjadi HTML murni (bukan Blade), aman dipakai
     * bersama body yang berasal dari WYSIWYG (Summernote).
     *
     * @param mixed  $rows    Collection data (hasil query)
     * @param array  $columns ['nama_field_db' => 'Label Kolom', ...] — urutan sesuai urutan tampil
     * @param array  $options
     *   - aging_operator  : '>' atau '>=' (default: tidak ada highlight kalau null)
     *   - aging_threshold : angka pembanding aging
     *   - date_fields     : ['nama_field' => 'format_carbon'] contoh ['received_date' => 'd-M-Y']
     *   - empty_text      : teks saat data kosong (kalau null, tabel tidak ditampilkan sama sekali)
     */
    private function renderCaseTable($rows, array $columns, array $options = []): string
    {
        $agingOperator   = $options['aging_operator'] ?? null;
        $agingThreshold  = $options['aging_threshold'] ?? null;
        $dateFields      = $options['date_fields'] ?? [];
        $emptyText       = $options['empty_text'] ?? null;

        if ((!$rows || $rows->isEmpty())) {
            if ($emptyText === null) {
                return '';
            }
        }

        $html = '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">';

        $html .= '<thead><tr style="background-color:#d9d9d9; text-align:center;"><th>No</th>';

        foreach ($columns as $label) {
            $html .= '<th>' . e($label) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        if (!$rows || $rows->isEmpty()) {

            $html .= '<tr><td colspan="' . (count($columns) + 1) . '" align="center">'
                . e($emptyText) . '</td></tr>';
        } else {

            $no = 1;

            foreach ($rows as $row) {

                $html .= '<tr>';
                $html .= '<td align="center">' . $no++ . '</td>';

                foreach (array_keys($columns) as $field) {

                    $value = $row->{$field} ?? '';

                    // Format tanggal via Carbon (kalau field ini termasuk yang perlu diformat)
                    if (isset($dateFields[$field])) {
                        $value = $value
                            ? \Carbon\Carbon::parse($value)->format($dateFields[$field])
                            : '-';
                    }

                    // Highlight kolom aging sesuai ambang batas masing-masing jenis monitoring
                    if ($field === 'aging' && $agingOperator !== null && $agingThreshold !== null) {

                        $isHighlight = $agingOperator === '>='
                            ? ((float) $value >= $agingThreshold)
                            : ((float) $value > $agingThreshold);

                        $bg = $isHighlight ? '#ffb3b3' : 'white';

                        $html .= '<td style="background-color:' . $bg . '; text-align:center; font-weight:bold;">'
                            . e($value) . '</td>';
                    } elseif ($field === 'aging') {

                        // Aging tanpa highlight (dipakai di tabel Pending < 5 Hari)
                        $html .= '<td style="text-align:center; font-weight:bold;">' . e($value) . '</td>';
                    } else {
                        $html .= '<td>' . e($value) . '</td>';
                    }
                }

                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * Bangun semua tabel case yang mungkin dipakai, dikembalikan sebagai array
     * dengan key placeholder. Body template tinggal menaruh placeholder ini
     * di posisi mana pun teks yang diinginkan, misalnya:
     *
     *   [[TABEL_CASE]]              -> WIP / Pending 14D / KCI / Finish Repair (1 tabel)
     *   [[TABEL_PENDING_BELOW_5]]   -> khusus W003, tabel "aging < 5 hari"
     *   [[TABEL_PENDING_ABOVE_5]]   -> khusus W003, tabel "aging >= 5 hari"
     *
     * Kalau body TIDAK mengandung placeholder tsb (template lama belum diubah),
     * tabel otomatis ditempel di akhir body sebagai fallback.
     */
    private function buildCaseTablePlaceholders(
        bool $isWip,
        bool $isPending5d,
        bool $isPending14d,
        bool $iskci,
        bool $isfinishrepair,
        $rows,
        $pendingBelow5Rows,
        $pendingAbove5Rows,
        $pendingAbove14Rows,
        $kciRows,
        $finishrepairRows
    ): array {

        $placeholders = [];

        if ($isWip) {
            $placeholders['[[TABEL_CASE]]'] = $this->renderCaseTable(
                $rows,
                [
                    'case_id_manual' => 'Case ID',
                    'aging' => 'Aging',
                    'company_name' => 'Company Name',
                    'finish_date' => 'Finish Date',
                    'case_status' => 'Case Status',
                    'hp_part_no' => 'HP Part No',
                    'so_no' => 'SO No',
                    'awb_no_part_return' => 'AWB No Part Return',
                    'part_in_date' => 'Part In Date',
                    'created_at' => 'Today',
                ],
                [
                    'aging_operator' => '>',
                    'aging_threshold' => 14,
                    'empty_text' => 'Tidak ada data ditemukan',
                ]
            );
        }

        if ($isPending5d) {

            $columns5d = [
                'case_id' => 'Case ID',
                'received_date' => 'Received Date',
                'start_repair_date' => 'Start Repair Date',
                'company_name' => 'Company Name',
                'aging' => 'Aging',
                'case_status' => 'Case Status',
                'ce_name' => 'CE Name',
                'company_city' => 'Company City',
                'part_name' => 'Part Name',
                'hp_part_no' => 'HP Part No',
                'part_request_date' => 'Part Request Date',
                'so_no' => 'SO No',
                'eta_date' => 'ETA Date',
                'part_in_date' => 'Part In Date',
                'product_no' => 'Product No',
                'product_name' => 'Product Name',
            ];

            $placeholders['[[TABEL_PENDING_BELOW_5]]'] = $this->renderCaseTable(
                $pendingBelow5Rows,
                $columns5d,
                [
                    'date_fields' => ['part_request_date' => 'Y-m-d'],
                ]
            );

            $placeholders['[[TABEL_PENDING_ABOVE_5]]'] = $this->renderCaseTable(
                $pendingAbove5Rows,
                $columns5d,
                [
                    'aging_operator' => '>',
                    'aging_threshold' => 5,
                    'date_fields' => ['part_request_date' => 'Y-m-d'],
                ]
            );
        }

        if ($isPending14d) {
            $placeholders['[[TABEL_CASE]]'] = $this->renderCaseTable(
                $pendingAbove14Rows,
                [
                    'company_name' => 'Company Name',
                    'aging' => 'Aging',
                    'case_id' => 'Case ID',
                    'received_date' => 'Received Date',
                    'part_request_date' => 'Part Request Date',
                    'eta_date' => 'ETA Date',
                    'part_in_date' => 'Part In Date',
                    'so_no' => 'SO No',
                    'hp_part_no' => 'HP Part No.',
                    'case_status' => 'Case Status',
                    'ce_name' => 'CE Name',
                ],
                [
                    'aging_operator' => '>=',
                    'aging_threshold' => 14,
                    'date_fields' => [
                        'received_date' => 'd-M-Y',
                        'part_request_date' => 'd-M-Y',
                        'eta_date' => 'd-M-Y',
                        'part_in_date' => 'd-M-Y',
                    ],
                    'empty_text' => 'Tidak ada Pending Case di atas 14 hari.',
                ]
            );
        }

        if ($iskci) {
            $placeholders['[[TABEL_CASE]]'] = $this->renderCaseTable(
                $kciRows,
                [
                    'case_id' => 'Case ID',
                    'company_name' => 'Company Name',
                    'aging' => 'Aging',
                    'customer_name' => 'Customer name',
                    'case_status' => 'Case Status',
                    'ce_name' => 'CE name',
                    'company_city' => 'Company city',
                ],
                [
                    'aging_operator' => '>',
                    'aging_threshold' => 20,
                    'empty_text' => 'Tidak ada data ditemukan',
                ]
            );
        }

        if ($isfinishrepair) {
            $placeholders['[[TABEL_CASE]]'] = $this->renderCaseTable(
                $finishrepairRows,
                [
                    'case_id' => 'Case ID',
                    'count' => 'Count',
                    'company_name' => 'Company Name',
                    'aging' => 'Aging',
                    'customer_name' => 'Customer name',
                    'case_status' => 'Case Status',
                    'ce_name' => 'CE name',
                    'company_city' => 'Company city',
                ],
                [
                    'aging_operator' => '>',
                    'aging_threshold' => 20,
                    'empty_text' => 'Tidak ada data ditemukan',
                ]
            );
        }

        return $placeholders;
    }

    /**
     * Sisipkan tabel-tabel case ke posisi placeholder di dalam body.
     * Kalau body tidak punya placeholder-nya sama sekali (template lama),
     * tabel ditempel otomatis di akhir sebagai fallback supaya tidak hilang.
     */
    private function injectCaseTables(string $body, array $placeholders): string
    {
        $html = $body;
        $anyReplaced = false;

        // ★★★ DIUBAH : sebelumnya placeholder dengan tabel KOSONG (tidak ada data)
        // dilewati (skip), jadi teksnya (mis. "[[TABEL_PENDING_ABOVE_5]]") tertinggal
        // mentah di email. Sekarang tetap diganti — kalau kosong, ya diganti jadi
        // kosong (hilang), bukan dibiarkan. ★★★
        foreach ($placeholders as $tag => $tableHtml) {

            if (str_contains($html, $tag)) {
                $html = str_replace($tag, $tableHtml, $html);
                $anyReplaced = true;
            }
        }

        if (!$anyReplaced) {
            // Fallback: body belum pakai placeholder sama sekali, tempel di akhir
            foreach ($placeholders as $tableHtml) {
                $html .= $tableHtml;
            }
        }

        return $html;
    }

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

            // KCI
            $kciQuery = DB::table('kci')
                ->select(
                    'case_id',
                    'count',
                    'company_name',
                    'aging',
                    'customer_name',
                    'case_status',
                    'ce_name',
                    'company_city'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING KCI
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $kciQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $kciRows = $kciQuery
                ->orderByDesc('aging')
                ->get();

            // Finish Repair
            $finishrepairQuery = DB::table('finish_repair')
                ->select(
                    'case_id',
                    'count',
                    'company_name',
                    'aging',
                    'customer_name',
                    'case_status',
                    'ce_name',
                    'company_city'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING Finish Repair
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $finishrepairQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $finishrepairRows = $finishrepairQuery
                ->orderByDesc('aging')
                ->get();


            $emailMaster = DB::table('email_masters')
                ->where('kode_company', $kodeCompany)
                ->first();


            $companyName = $emailMaster->company_name
                ?? $kodeCompany;

            $pendingBelow5Rows = $pendingCase5dRows->filter(function ($row) {
                return (float) $row->aging < 5;
            });

            $pendingAbove5Rows = $pendingCase5dRows->filter(function ($row) {
                return (float) $row->aging >= 5;
            });
            $pendingAbove14Rows = $pendingCase14dRows;
            $kciRows = $kciRows;


            $footer = DB::table('footer_masters')->where('id', $request->footer_id)->first();
            $body = $template->body;

            $footerHtml = nl2br($footer->footer_html ?? '');

            // 1. Buat pengecekan jenis monitoring (Tidak case-sensitive)
            $jenisMonitoring = strtolower(trim($template->jenis_monitoring ?? ''));

            $isWip = str_contains($jenisMonitoring, 'wip');

            $isPending5d =
                str_contains($jenisMonitoring, '5day') ||
                str_contains($jenisMonitoring, 'tat5');

            $isPending14d =
                str_contains($jenisMonitoring, '14day') ||
                str_contains($jenisMonitoring, 'tat14');
            $iskci = str_contains($jenisMonitoring, 'kci');
            $isfinishrepair =
                str_contains($jenisMonitoring, 'finish repair') ||
                str_contains($jenisMonitoring, 'finishrepair');

            // ★★★ DIUBAH : baris ini dulunya "$html = Blade::render($body, [...])"
            // yang menyebabkan ParseError. Sekarang tabel disisipkan lewat placeholder,
            // deteksi jenis monitoring ($isWip dkk di atas) juga DIPINDAH ke sini
            // (sebelumnya baru dihitung SETELAH Blade::render dipanggil). ★★★
            // 2. Body sekarang HANYA teks/HTML biasa dari Summernote (aman, tanpa kode Blade)
            //    Tabel case digenerate otomatis lewat PHP dan disisipkan lewat placeholder,
            //    BUKAN Blade::render() lagi
            $tablePlaceholders = $this->buildCaseTablePlaceholders(
                $isWip,
                $isPending5d,
                $isPending14d,
                $iskci,
                $isfinishrepair,
                $rows,
                $pendingBelow5Rows,
                $pendingAbove5Rows,
                $pendingAbove14Rows,
                $kciRows,
                $finishrepairRows
            );

            // ★★★ DIUBAH: footer dibungkus style yang sama dengan body (Arial 12px)
            // supaya ukuran fontnya konsisten, sebelumnya footer pakai font default browser
            // (jadi kelihatan lebih besar dari teks body).
            $footerHtmlStyled = '<div style="font-family: Arial, sans-serif; font-size:12px; color:#000; line-height:1.5;">'
                . $footerHtml
                . '</div>';

            $html = $this->injectCaseTables($body, $tablePlaceholders) . $footerHtmlStyled;

            // 3. Siapkan array untuk menampung teks
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
            if ($iskci) {
                $totalArray[] = 'Total Case KCI : ' . $kciRows->count();
            }
            if ($isfinishrepair) {
                $totalArray[] = 'Total Case Finish Repair : ' . $finishrepairRows->count();
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
            $emailMasters = DB::table('email_masters')
                ->where('kode_company', $kodeCompany)
                ->get();

            if ($emailMasters->isEmpty()) {
                continue;
            }

            // Ambil semua email milik company
            $emails = $emailMasters
                ->pluck('email')
                ->map(function ($email) {
                    return trim($email);
                })
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            // Ambil nama company dari baris pertama
            $companyName = $emailMasters->first()->company_name;

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

            // KCI
            $kciQuery = DB::table('kci')
                ->select(
                    'case_id',
                    'count',
                    'company_name',
                    'aging',
                    'customer_name',
                    'case_status',
                    'ce_name',
                    'company_city'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING KCI
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $kciQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $kciRows = $kciQuery
                ->orderByDesc('aging')
                ->get();

            // Finish Repair
            $finishrepairQuery = DB::table('finish_repair')
                ->select(
                    'case_id',
                    'count',
                    'company_name',
                    'aging',
                    'customer_name',
                    'case_status',
                    'ce_name',
                    'company_city'
                )
                ->where(DB::raw('LEFT(case_id,3)'), $kodeCompany)
                ->whereNull('sent_at');

            // FILTER AGING Finish Repair
            if ($request->filled('aging_filter')) {
                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {
                    $finishrepairQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $finishrepairRows = $finishrepairQuery
                ->orderByDesc('aging')
                ->get();

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
            $kciRows = $kciRows;



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

            // ★★★ DIUBAH : dari sini sampai "$html = ..." di bawah adalah perubahan
            // di function send(). Sebelumnya function ini TIDAK PERNAH mendeteksi jenis
            // monitoring sama sekali (blok $jenisMonitoring/$isWip dst di bawah ini seluruhnya
            // BARU) dan langsung memanggil Blade::render($body, [...]) yang sekarang diganti
            // buildCaseTablePlaceholders() + injectCaseTables(). ★★★
            // Deteksi jenis monitoring (sebelumnya function send() tidak melakukan ini sama sekali)
            $jenisMonitoring = strtolower(trim($template->jenis_monitoring ?? ''));

            $isWip = str_contains($jenisMonitoring, 'wip');

            $isPending5d =
                str_contains($jenisMonitoring, '5day') ||
                str_contains($jenisMonitoring, 'tat5');

            $isPending14d =
                str_contains($jenisMonitoring, '14day') ||
                str_contains($jenisMonitoring, 'tat14');
            $iskci = str_contains($jenisMonitoring, 'kci');
            $isfinishrepair =
                str_contains($jenisMonitoring, 'finish repair') ||
                str_contains($jenisMonitoring, 'finishrepair');

            $footerHtml = nl2br($footer->footer_html ?? '');

            // Body sekarang HANYA teks/HTML biasa dari Summernote (aman, tanpa kode Blade)
            // Tabel case digenerate otomatis lewat PHP dan disisipkan lewat placeholder,
            // footer ditambahkan di akhir, BUKAN Blade::render() lagi
            $tablePlaceholders = $this->buildCaseTablePlaceholders(
                $isWip,
                $isPending5d,
                $isPending14d,
                $iskci,
                $isfinishrepair,
                $rows,
                $pendingBelow5Rows,
                $pendingAbove5Rows,
                $pendingAbove14Rows,
                $kciRows,
                $finishrepairRows
            );

            // ★★★ DIUBAH: footer dibungkus style yang sama dengan body (Arial 12px)
            // supaya ukuran fontnya konsisten, sebelumnya footer pakai font default browser
            // (jadi kelihatan lebih besar dari teks body).
            $footerHtmlStyled = '<div style="font-family: Arial, sans-serif; font-size:12px; color:#000; line-height:1.5;">'
                . $footerHtml
                . '</div>';

            $html = $this->injectCaseTables($body, $tablePlaceholders) . $footerHtmlStyled;

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

            Mail::html($html, function ($message) use (
                $emails,
                $ccEmails,
                $subject,
                $attachments,
                $bccEmails
            ) {

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
                        $message->attach(
                            $file->getRealPath(),
                            [
                                'as' => $file->getClientOriginalName()
                            ]
                        );
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

            // UPDATE KCI
            $updatekciQuery = DB::table('kci')
                ->where('case_id', 'like', $kodeCompany . '%')
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {

                    $updatekciQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $updatekciQuery->update([
                'sent_at' => now(),
            ]);

            // UPDATE Finish Repair
            $updatefinishrepairQuery = DB::table('finish_repair')
                ->where('case_id', 'like', $kodeCompany . '%')
                ->whereNull('sent_at');

            if ($request->filled('aging_filter')) {

                $filter = trim($request->aging_filter);

                if (preg_match('/^(>=|<=|>|<|=)\s*(\d+(\.\d+)?)$/', $filter, $match)) {

                    $updatefinishrepairQuery->where(
                        'aging',
                        $match[1],
                        (float) $match[2]
                    );
                }
            }

            $updatefinishrepairQuery->update([
                'sent_at' => now(),
            ]);
            $totalCase = 0;

            if ($isWip) {
                $totalCase = $rows->count();
            } elseif ($isPending5d) {
                $totalCase = $pendingCase5dRows->count();
            } elseif ($isPending14d) {
                $totalCase = $pendingCase14dRows->count();
            } elseif ($iskci) {
                $totalCase = $kciRows->count();
            } elseif ($isfinishrepair) {
                $totalCase = $finishrepairRows->count();
            }

            // 4. Simpan log email
            DB::table('email_logs')->insert([
                'kode_company' => $kodeCompany,
                'template_name' => $template->nama_template,
                'subject' => $subject,
                'company_name' => $companyName,
                'recipient' => implode(', ', $emails),
                'total_case' => $totalCase,
                'aging_filter' => $request->aging_filter,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'Email berhasil dikirim');
    }
}

<?php

namespace App\Imports;

use App\Models\Mpr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MprImport implements ToCollection
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    private function formatDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {

            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return date('Y-m-d', strtotime($value));

        } catch (\Exception $e) {

            return null;
        }
    }

    public function collection(Collection $rows)
    {
        // Header
        $header = $rows->first()
            ->map(function ($item) {
                return preg_replace('/\s+/', ' ', trim(strtolower($item)));
            })
            ->toArray();

        // Data
        $rows->skip(1)->each(function ($row) use ($header) {

            if ($row->filter()->isEmpty()) {
                return;
            }

            $data = [];

            foreach ($header as $index => $name) {
                $data[$name] = isset($row[$index])
                    ? trim((string) $row[$index])
                    : null;
            }

            // Skip jika Case ID kosong
            if (empty($data['case id'])) {
                return;
            }

            Mpr::create([

                'jenis_monitoring' => 'M006-MPR',
                'kode_upload'      => $this->kodeUpload,

                'year_mo_close' => $data['year-mo close'] ?? null,

                'count' => is_numeric($data['count'] ?? null)
                    ? (int) $data['count']
                    : null,

                'case_id' => $data['case id'] ?? null,

                'product_tower' => $data['product tower'] ?? null,
                'product_no'    => $data['product no.'] ?? null,
                'product_name'  => $data['product name'] ?? null,

                'received_date' => $this->formatDate($data['received date'] ?? null),
                'start_repair_date' => $this->formatDate($data['start repair date'] ?? null),
                'finish_repair_date' => $this->formatDate($data['finish repair date'] ?? null),
                'closed_date' => $this->formatDate($data['closed date'] ?? null),

                'tat' => is_numeric($data['tat'] ?? null)
                    ? (float) $data['tat']
                    : null,

                'tat_meet'   => $data['tat meet'] ?? null,
                'delay_code' => $data['delay code'] ?? null,

                'customer_name' => $data['customer name'] ?? null,
                'problem_desc'  => $data['problem desc.'] ?? null,
                'customer_city' => $data['customer city'] ?? null,
                'partner_name'  => $data['partner name'] ?? null,

            ]);
        });
    }
}
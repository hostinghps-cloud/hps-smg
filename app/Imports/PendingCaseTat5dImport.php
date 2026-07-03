<?php

namespace App\Imports;

use App\Models\PendingCaseTat5d;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PendingCaseTat5dImport implements ToCollection
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        $rows->skip(1)->each(function ($row) {

            if (empty($row[0])) {
                return;
            }

            PendingCaseTat5d::create([

                'jenis' => 'W003-TAT5D (Pending case 5 days)',
                'kode_upload' => $this->kodeUpload,

                'case_id' => $row[0] ?? null,
                'received_date' =>
                    !empty($row[1])
                    ? Date::excelToDateTimeObject($row[1])->format('Y-m-d')
                    : null,

             
                'company_name' => $row[2] ?? null,
                'aging' => $row[3] ?? null,
                'case_status' => $row[4] ?? null,
                'ce_name' => $row[5] ?? null,
                'company_city' => $row[6] ?? null,
                'part_request_date' =>
                    !empty($row[7])
                    ? Date::excelToDateTimeObject($row[7])->format('Y-m-d')
                    : null,
                'so_no' => $row[8] ?? null,
                'eta_date' =>
                    !empty($row[9])
                    ? Date::excelToDateTimeObject($row[9])->format('Y-m-d')
                    : null,
                'part_in_date' =>
                    !empty($row[10])
                    ? Date::excelToDateTimeObject($row[10])->format('Y-m-d')
                    : null,
                'product_no' => $row[11] ?? null,
                'product_name' => $row[11] ?? null,
               

            ]);
        });
    }

}
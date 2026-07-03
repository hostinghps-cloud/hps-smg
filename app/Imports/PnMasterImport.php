<?php

namespace App\Imports;

use App\Models\PnMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PnMasterImport implements ToModel, WithStartRow
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    public function startRow(): int
    {
        return 5;
    }

    public function model(array $row)
    {
        // skip row kosong
        if (empty($row[1])) {
            return null;
        }

        return new PnMaster([

            // 🔥 batch
            'kode_upload' => $this->kodeUpload,

            // 🔥 mapping excel
            'case_id'       => $row[1] ?? null,
            'product_no'    => $row[14] ?? null,
            'product_name'  => $row[15] ?? null,

            // 🔥 data part
            'pn_code'       => trim(strtoupper($row[52] ?? null)),
            'kategori'      => strtolower($row[53] ?? null),

            'part_request'  => $row[54] ?? null,
            'so_no'         => $row[55] ?? null,

            'eta_date'      => !empty($row[56])
                ? date('Y-m-d', strtotime($row[56]))
                : null,

            'partin_date'   => !empty($row[57])
                ? date('Y-m-d', strtotime($row[57]))
                : null,
        ]);
    }
}
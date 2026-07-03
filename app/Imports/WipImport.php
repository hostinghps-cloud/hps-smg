<?php

namespace App\Imports;

use App\Models\WipData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class WipImport implements ToCollection
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            if ($index == 0) {
                continue;
            }

            if (empty($row[0])) {
                continue;
            }

            WipData::create([
                'kode_upload' => $this->kodeUpload,
                'jenis_monitoring' => 'W001-WIP',

                'case_id_manual' => $row[0],
                'company_name' => $row[1],

                'finish_date' => !empty($row[2])
                    ? Carbon::parse($row[2])
                    : null,

                'case_status' => $row[3] ?? null,
                'hp_part_no' => $row[4] ?? null,
                'so_no' => $row[5] ?? null,
                'awb_no_part_return' => $row[6] ?? null,

                'part_in_date' => !empty($row[7])
                    ? Carbon::parse($row[7])
                    : null,

                'aging' => $row[9] ?? null,
            ]);
        }
    }
}
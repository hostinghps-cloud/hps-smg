<?php

namespace App\Imports;

use App\Models\RawData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class RawDataImport implements ToModel, WithStartRow
{
    protected $jenisUpload;
    protected $kodeUpload;

    public function __construct($jenisUpload, $kodeUpload)
    {
        $this->jenisUpload = $jenisUpload;
        $this->kodeUpload = $kodeUpload;
    }

   public function startRow(): int
{
    return 2;
}

   public function model(array $row)
{
    // skip row kosong
    if (empty($row[0])) {
        return null;
    }

    return new RawData([

    // 🔥 DATA UPLOAD
    'jenis_upload' => $this->jenisUpload,
    'kode_upload'  => $this->kodeUpload,

    // 🔥 DATA WIP
    'case_id'      => $row[0] ?? null,
    'company_name' => $row[1] ?? null,

    // 🔥 FINISH DATE
    'start_repair_date' => !empty($row[2])
    ? Carbon::createFromFormat(
        'd M Y H:i',
        trim($row[2])
    )->format('Y-m-d H:i:s')
    : null,

    'case_status'  => $row[3] ?? null,

    // 🔥 HP PART NO
    'product_no'      => $row[4] ?? null,

    // 🔥 SO NUMBER
    'so_no'        => $row[5] ?? null,

    'awb_no'       => $row[6] ?? null,

    // 🔥 PART IN
    'part_in_date' => !empty($row[7])
    ? (
        is_numeric($row[7])
            ? Date::excelToDateTimeObject($row[7])
                ->format('Y-m-d H:i:s')
            : Carbon::parse($row[7])
                ->format('Y-m-d H:i:s')
    )
    : null,

    'today_date'   => $row[8] ?? null,

    // 🔥 AGING
    'tat_case' => isset($row[9])
    ? round((float) $row[9], 2)
    : null,

]);
}
}
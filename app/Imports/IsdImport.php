<?php
namespace App\Imports;

use App\Models\RawData;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class IsdImport implements ToCollection
{
    protected $jenisUpload;
    protected $kodeUpload;

    public function __construct($jenisUpload, $kodeUpload)
    {
        $this->jenisUpload = $jenisUpload;
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            if ($index == 0) {
                continue;
            }

            RawData::create([
                'kode_upload' => $this->kodeUpload,
                'data_json'   => json_encode($row),
            ]);
        }
    }
}
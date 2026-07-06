<?php

namespace App\Imports;

use Exception;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetImport implements WithMultipleSheets
{
    protected $jenisUpload;
    protected $kodeUpload;

    public function __construct($jenisUpload, $kodeUpload)
    {
        $this->jenisUpload = $jenisUpload;
        $this->kodeUpload = $kodeUpload;
    }

    public function sheets(): array
    {
        switch ($this->jenisUpload) {

            case 'W001-WIP':
                return [
                    'Parts' => new WipImport($this->kodeUpload),
                ];
            case 'W002-TAT14D (Pending case 14 days)':
                return [
                    'Export' => new PendingCaseTat14dImport(
                        $this->kodeUpload
                    ),
                ];

            case 'W003-TAT5D (Pending case 5 days)':
                return [
                    'Export' => new PendingCaseTat5dImport(
                        $this->kodeUpload
                    ),
                ];
            
            case 'W004-KCI':
                return [
                    'Export' => new KciImport(
                        $this->kodeUpload
                    ),
                ];

            case 'W005-FR (Finish Repair)':
                return [
                    'Sheet1' => new FinishRepairImport(
                        $this->kodeUpload
                    ),
                ];

           

            default:
                throw new Exception(
                    'Jenis monitoring tidak dikenali : ' .
                    $this->jenisUpload
                );
        }
    }
}
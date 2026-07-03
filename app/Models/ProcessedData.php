<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedData extends Model
{
    protected $table = 'processed_data';

    protected $fillable = [
        'case_id',
        'pn',
        'kategori',
        'harga'
    ];
}
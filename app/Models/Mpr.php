<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpr extends Model
{
    use HasFactory;

    protected $table = 'mpr';

    protected $fillable = [
        'jenis_monitoring',
        'kode_upload',

        'year_mo_close',
        'count',
        'case_id',

        'product_tower',
        'product_no',
        'product_name',

        'received_date',
        'start_repair_date',
        'finish_repair_date',
        'closed_date',

        'tat',
        'tat_meet',
        'delay_code',

        'customer_name',
        'problem_desc',
        'customer_city',
        'partner_name',
    ];

    protected $casts = [
        'received_date'      => 'date',
        'start_repair_date'  => 'date',
        'finish_repair_date' => 'date',
        'closed_date'        => 'date',

        'count' => 'integer',
        'tat'   => 'float',
    ];
}
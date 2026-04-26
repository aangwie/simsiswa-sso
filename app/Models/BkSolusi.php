<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkSolusi extends Model
{
    use HasFactory;

    protected $table = 'bk_solusi';

    protected $fillable = [
        'bk_konsultasi_id', 'solusi', 'tindakan', 'status'
    ];

    public function konsultasi()
    {
        return $this->belongsTo(BkKonsultasi::class, 'bk_konsultasi_id');
    }
}

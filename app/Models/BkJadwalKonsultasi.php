<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkJadwalKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'bk_jadwal_konsultasis';

    protected $fillable = [
        'bk_konsultasi_id', 'tanggal', 'jam', 'guru_bk', 'ruang', 'catatan'
    ];

    public function konsultasi()
    {
        return $this->belongsTo(BkKonsultasi::class, 'bk_konsultasi_id');
    }
}

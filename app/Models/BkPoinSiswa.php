<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkPoinSiswa extends Model
{
    use HasFactory;

    protected $table = 'bk_poin_siswas';

    protected $fillable = [
        'student_id', 'bk_pelanggaran_id', 'tanggal', 'keterangan'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function pelanggaran()
    {
        return $this->belongsTo(BkPelanggaran::class, 'bk_pelanggaran_id');
    }
}

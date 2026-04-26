<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'bk_konsultasis';

    protected $fillable = [
        'student_id', 'tanggal_pengajuan', 'jenis_masalah', 'deskripsi', 'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function jadwal()
    {
        return $this->hasOne(BkJadwalKonsultasi::class);
    }

    public function solusi()
    {
        return $this->hasOne(BkSolusi::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'bk_pelanggarans';

    protected $fillable = [
        'nama_pelanggaran', 'poin'
    ];

    public function poinSiswa()
    {
        return $this->hasMany(BkPoinSiswa::class, 'bk_pelanggaran_id');
    }
}

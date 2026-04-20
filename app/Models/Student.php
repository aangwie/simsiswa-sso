<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id', 'name', 'gender', 'nis', 'enrollment_year',
        'is_active', 'nisn', 'tanggal_lahir', 'status_lulus', 'ijazah_file',
        'nama_ayah', 'nama_ibu', 'alamat'
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}

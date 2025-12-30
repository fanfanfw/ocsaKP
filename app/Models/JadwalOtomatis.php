<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalOtomatis extends Model
{
    use HasFactory;

    protected $table = 'jadwal_otomatis';

    protected $fillable = [
        'asset_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'asset_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

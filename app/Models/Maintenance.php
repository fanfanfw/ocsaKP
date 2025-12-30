<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenance';

    protected $fillable = [
        'asset_id',
        'deskripsi',
        'tanggal',
        'part',
        'jenis_kerusakan',
        'tingkat',
        'tindakan',
        'tanggal_selesai',
    ];

    public $timestamps = false;

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedule';

    protected $fillable = [
        'asset_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

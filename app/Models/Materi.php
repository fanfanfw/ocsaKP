<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    protected $fillable = [
        'nama',
    ];

    /**
     * Get the assets associated with this materi.
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_materi');
    }

    /**
     * Get the jadwal associated with this materi.
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}

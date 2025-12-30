<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    protected $fillable = [
        'nama_aset',
        'kategori',
        'status',
        'tahun',
        'harga',
        'jumlah',
    ];

    public $timestamps = false;

    public function parts()
    {
        return $this->hasMany(AssetPart::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function jadwalOtomatis()
    {
        return $this->hasMany(JadwalOtomatis::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(AssetStatusLog::class, 'asset_id');
    }
}

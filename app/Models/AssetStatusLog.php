<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetStatusLog extends Model
{
    use HasFactory;

    protected $table = 'asset_status_log';

    protected $fillable = [
        'asset_id',
        'status',
        'updated_at',
    ];

    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
}

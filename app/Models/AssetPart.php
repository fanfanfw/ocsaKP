<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetPart extends Model
{
    use HasFactory;

    protected $table = 'asset_parts';

    protected $fillable = [
        'asset_id',
        'asset_item_id',
        'nama_part',
        'kondisi',
        'jumlah',
        'keterangan',
    ];

    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function assetItem()
    {
        return $this->belongsTo(AssetItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model
{
    use HasFactory;

    protected $fillable = ['asset_id', 'code', 'condition', 'is_available'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

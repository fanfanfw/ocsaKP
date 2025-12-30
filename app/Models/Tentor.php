<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tentor extends Model
{
    use HasFactory;

    protected $table = 'tentor';

    protected $fillable = [
        'nama',
    ];

    public $timestamps = false;
}

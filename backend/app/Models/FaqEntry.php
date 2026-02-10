<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqEntry extends Model
{
    use HasFactory;

    /**
     * Oszlopok
     *
     * @var array
     */
    protected $fillable = [
        'keywords',
        'answer',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    /**
     * Oszlopok
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'occurs_at',
        'description',
    ];

    /**
     * Típus castolások
     *
     * @var array<string, string>
     */
    protected $casts = [
        'occurs_at' => 'datetime',
    ];

    /**
     * Felhasználó reláció
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

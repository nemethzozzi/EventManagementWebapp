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
     * @var array
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
     * @var array
     */
    protected $casts = [
        'occurs_at' => 'datetime',
    ];

    /**
     * Felhasználó reláció
     *
     * @return BelongsTo<User, Event>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

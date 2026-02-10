<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    /**
     * Oszlopok
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'handoff_requested',
        'assigned_agent_id',
    ];

    /**
     * Típus castolások
     *
     * @var array
     */
    protected $casts = [
        'handoff_requested' => 'boolean',
    ];

    /**
     * Felhasználó reláció
     *
     * @return BelongsTo<User, Conversation>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hozzárendelt agent reláció
     *
     * @return BelongsTo<User, Conversation>
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Üzenetek reláció
     *
     * @return HasMany<Message, Conversation>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}

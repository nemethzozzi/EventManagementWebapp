<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    public const SENDER_USER = 'user';
    public const SENDER_AGENT = 'agent';
    public const SENDER_BOT = 'bot';

    /**
     * Oszlopok
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'content',
    ];

    /**
     * Beszélgetések reláció
     *
     * @return BelongsTo<Conversation, Message>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Felhasználó reláció
     *
     * @return BelongsTo<User, Message>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RechargeOrder extends Model
{
    use HasUuids;

    // status, code_hash and telegram_sent_at are server-controlled, so they are
    // deliberately NOT fillable: the controller sets them with forceFill().
    protected $fillable = [
        'user_id',
        'montant',
        'account_id',
        'platform',
        'full_name',
        'is_repeat',
    ];

    protected $hidden = ['code_hash'];

    protected $casts = [
        'montant' => 'integer',
        'is_repeat' => 'boolean',
        'telegram_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
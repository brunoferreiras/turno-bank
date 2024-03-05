<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $table = 'deposits';

    protected $fillable = ['amount', 'description', 'image', 'user_id', 'status', 'approved_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): ?BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

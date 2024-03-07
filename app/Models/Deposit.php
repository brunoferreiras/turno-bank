<?php

namespace App\Models;

use App\Traits\HasAmount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory, HasAmount;

    protected $table = 'deposits';

    protected $fillable = ['amount', 'description', 'image', 'account_id', 'status', 'approved_by'];

    public function approvedBy(): ?BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

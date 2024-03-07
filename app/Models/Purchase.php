<?php

namespace App\Models;

use App\Traits\HasAmount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory, HasAmount;

    protected $table = 'purchases';

    protected $fillable = ['amount', 'description', 'account_id'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

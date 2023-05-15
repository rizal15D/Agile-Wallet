<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForecastTransactionType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function forecastTransaction(): HasMany
    {
        return $this->hasMany(ForecastTransaction::class);
    }
}

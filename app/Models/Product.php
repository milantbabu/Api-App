<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'amount',
        'status'
    ];

    public function title(): Attribute
    {
        return Attribute::make(
          get: fn (string $value) => ucfirst($value),
          set: fn (string $value) => ucfirst($value),
        );
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'is_filterable',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(
            ProductAttributeValue::class,
            'product_attribute_id'
        )->orderBy('sort_order');
    }
}
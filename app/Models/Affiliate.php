<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affiliate extends Model
{
    protected $fillable = [
        'affiliate_category_id',
        'name',
        'initials',
        'url',
        'blurb',
        'order',
        'is_active',
        'show_in_nav',
        'show_in_nra_nav',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
            'show_in_nav' => 'boolean',
            'show_in_nra_nav' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AffiliateCategory::class, 'affiliate_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function scopeInMainNav(Builder $query): Builder
    {
        return $query->active()->where('show_in_nav', true)->ordered();
    }

    public function scopeInNraNav(Builder $query): Builder
    {
        return $query->active()->where('show_in_nra_nav', true)->ordered();
    }
}

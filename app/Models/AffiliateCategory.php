<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AffiliateCategory extends Model
{
    public const PAGE_PARTNERS = 'partners';

    public const PAGE_NRA = 'nra';

    protected $fillable = [
        'name',
        'slug',
        'page',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (AffiliateCategory $category): void {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class)->orderBy('order')->orderBy('name');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->where('page', $page);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * @return array<string, string>
     */
    public static function pageOptions(): array
    {
        return [
            self::PAGE_PARTNERS => 'Affiliated Services page',
            self::PAGE_NRA => 'NRA Services page',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'menu_group',
        'link_type',
        'link_value',
        'sort_order',
        'show_in_nav',
        'assignable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'show_in_nav' => 'boolean',
            'assignable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @return array<string, string> slug => label
     */
    public static function assignableOptions(): array
    {
        try {
            $options = static::query()
                ->active()
                ->where('assignable', true)
                ->ordered()
                ->pluck('name', 'slug')
                ->all();

            if ($options !== []) {
                return $options;
            }
        } catch (\Throwable) {
            // Table may not exist yet during early migrate.
        }

        return config('service_categories', []);
    }

    /**
     * Grouped options for admin class form select.
     *
     * @return array<string, array<string, string>> group label => [slug => name]
     */
    public static function assignableOptionsGrouped(): array
    {
        try {
            $grouped = [
                'Training & Classes' => [],
                'Security Training' => [],
            ];

            $rows = static::query()
                ->active()
                ->where('assignable', true)
                ->ordered()
                ->get(['name', 'slug', 'menu_group']);

            foreach ($rows as $row) {
                $group = $row->menu_group === 'security' ? 'Security Training' : 'Training & Classes';
                $grouped[$group][$row->slug] = $row->name;
            }

            return array_filter($grouped);
        } catch (\Throwable) {
            return ['Categories' => static::assignableOptions()];
        }
    }

    /**
     * @return list<string>
     */
    public static function assignableSlugs(): array
    {
        return array_keys(static::assignableOptions());
    }

    /**
     * Nav items for Training & Classes / Security Training menus.
     *
     * @return Collection<int, array{name: string, url: string, match: array<string, string>}>
     */
    public static function navItems(string $menuGroup): Collection
    {
        try {
            $categories = static::query()
                ->active()
                ->where('show_in_nav', true)
                ->where('menu_group', $menuGroup)
                ->ordered()
                ->get();
        } catch (\Throwable) {
            return collect();
        }

        return $categories->map(function (self $category) {
            return [
                'name' => $category->name,
                'url' => $category->resolveUrl(),
                'match' => $category->resolveMatch(),
            ];
        });
    }

    public function resolveUrl(): string
    {
        return match ($this->link_type) {
            'slug' => route('class.show', $this->link_value),
            'route' => route($this->link_value),
            default => route('training-classes', ['category' => $this->link_value]),
        };
    }

    /**
     * @return array{type: string, category?: string, slug?: string, route?: string}
     */
    public function resolveMatch(): array
    {
        return match ($this->link_type) {
            'slug' => ['type' => 'slug', 'slug' => $this->link_value],
            'route' => ['type' => 'route', 'route' => $this->link_value],
            default => ['type' => 'training-classes', 'category' => $this->link_value],
        };
    }
}

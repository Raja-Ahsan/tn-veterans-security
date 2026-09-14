<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

class PublicTrainingServiceQuery
{
    /**
     * Services shown on Training & Classes tabs (excludes security training and renewals).
     *
     * @return Builder<Service>
     */
    public static function apply(Builder $query): Builder
    {
        return $query->where(function (Builder $builder): void {
            $builder->whereNull('categories')
                ->orWhere('categories', '[]')
                ->orWhere(function (Builder $inner): void {
                    $inner->whereJsonDoesntContain('categories', 'security_training')
                        ->whereJsonDoesntContain('categories', 'renewals');
                });
        });
    }

    /**
     * Active public classes, optionally filtered by category, search, and delivery format.
     *
     * @return Builder<Service>
     */
    public static function listing(?string $category = null, ?string $subcategory = null, string $q = '', ?string $delivery = null): Builder
    {
        $query = self::apply(Service::query()->where('is_active', true));

        if ($category) {
            $query->whereJsonContains('categories', $category);
        }

        if ($subcategory) {
            $query->where('subcategory', $subcategory);
        }

        if ($q !== '') {
            $query->where('title', 'like', '%'.$q.'%');
        }

        if (Service::isValidDeliveryFormat($delivery)) {
            $query->ofDelivery($delivery);
        }

        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }
}

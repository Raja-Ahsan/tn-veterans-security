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
}

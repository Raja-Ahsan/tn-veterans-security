<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RequirementsComplianceSeeder extends Seeder
{
    /**
     * Apply content/configuration updates required by the project specification.
     */
    public function run(): void
    {
        $this->normalizeRenewalServices();
        $this->seedHandleWithCare();
    }

    private function normalizeRenewalServices(): void
    {
        Service::query()->get()->each(function (Service $service): void {
            $categories = collect($service->categories ?? [])
                ->reject(fn (string $category): bool => $category === 'renewals')
                ->values()
                ->all();

            if ($categories !== ($service->categories ?? [])) {
                $service->update(['categories' => $categories ?: null]);
            }
        });

        $renewalMappings = [
            'Renewal Dallas Law' => ['Dallas Law renewal (2 year)', 'dallas-law-renewal-2-year'],
            'Unarmed Guard Renewal' => ['Unarmed renewal (2 Year)', 'unarmed-guard-renewal'],
            'Armed Guard Renewal' => ['Armed guard renewal (2 Year)', 'armed-guard-renewal'],
            'Renewal Enhanced Armed Guard' => [null, 'renewal-enhanced-armed-guard'],
        ];

        foreach ($renewalMappings as $displayTitle => [$legacyTitle, $slug]) {
            $service = null;

            if ($legacyTitle) {
                $service = Service::query()->where('title', $legacyTitle)->first();
            }

            $service ??= Service::query()->where('slug', $slug)->first();

            if (! $service) {
                $service = Service::query()->create([
                    'title' => $displayTitle,
                    'slug' => $slug,
                    'short_description' => $displayTitle.' renewal training for Tennessee security professionals.',
                    'is_active' => true,
                    'price' => 100.00,
                    'deposit_amount' => 20.00,
                    'class_type' => 'group',
                    'order' => 100,
                    'categories' => ['renewals'],
                ]);

                continue;
            }

            $categories = collect($service->categories ?? [])
                ->push('renewals')
                ->unique()
                ->values()
                ->all();

            $service->update([
                'title' => $displayTitle,
                'slug' => $service->slug ?: Str::slug($displayTitle),
                'categories' => $categories,
                'is_active' => true,
            ]);
        }
    }

    private function seedHandleWithCare(): void
    {
        Service::query()->updateOrCreate(
            ['slug' => 'handle-with-care'],
            [
                'title' => 'Handle With Care',
                'short_description' => 'Specialized training for safely managing individuals with special needs in school and hospital environments.',
                'description' => '<p>Handle With Care training teaches de-escalation and safe physical intervention techniques for caregivers and security professionals working in schools, hospitals, and similar settings.</p>',
                'categories' => ['red_cross'],
                'is_active' => true,
                'price' => 150.00,
                'deposit_amount' => 20.00,
                'class_type' => 'group',
                'order' => 15,
                'what_to_bring' => 'Valid photo ID, comfortable clothing suitable for physical training.',
                'prerequisites' => 'None required.',
            ]
        );
    }
}

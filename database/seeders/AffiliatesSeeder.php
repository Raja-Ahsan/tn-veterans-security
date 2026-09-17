<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\AffiliateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliatesSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'name' => 'Veteran Owned Security Companies',
                'page' => AffiliateCategory::PAGE_PARTNERS,
                'order' => 1,
                'companies' => [
                    ['initials' => 'ES', 'name' => 'Elite Security', 'url' => 'https://www.elitesecuritytn.org', 'show_in_nav' => true, 'order' => 3],
                    ['initials' => 'VST', 'name' => 'Vanguard Security Training LLC', 'url' => 'https://vanguardsecuritytrainingllc.com', 'show_in_nav' => true, 'order' => 9],
                    ['initials' => 'RSG', 'name' => 'Regiment Security Group', 'url' => 'https://www.regimentsecuritygroup.com', 'show_in_nav' => true, 'order' => 20],
                    ['initials' => 'ESS', 'name' => 'Essential Security Services', 'url' => 'https://www.essentialsecurityservices.com', 'order' => 4],
                ],
            ],
            [
                'name' => 'Non Veteran Owned Security Companies',
                'page' => AffiliateCategory::PAGE_PARTNERS,
                'order' => 2,
                'companies' => [
                    ['initials' => 'STN', 'name' => 'SafetyTN Security Solutions', 'url' => 'https://safetytennessee.com', 'show_in_nav' => true, 'order' => 6],
                    ['initials' => 'JSC', 'name' => 'JS Security Consulting', 'url' => 'https://www.jssecurityconsulting.com', 'show_in_nav' => true, 'order' => 5],
                    ['initials' => 'APX', 'name' => 'APEX Security Group', 'url' => 'https://apexsgi.com', 'show_in_nav' => true, 'order' => 1],
                ],
            ],
            [
                'name' => 'Veteran Owned Companies (Non Security)',
                'page' => AffiliateCategory::PAGE_PARTNERS,
                'order' => 3,
                'companies' => [
                    ['initials' => 'G+L', 'name' => 'Guns & Leather', 'url' => 'https://gunsandleather.com', 'show_in_nav' => true, 'order' => 4],
                    ['initials' => 'SGA', 'name' => "Shooter's Nashville", 'url' => 'https://www.shootersnashville.com', 'show_in_nav' => true, 'order' => 7],
                    ['initials' => 'SWC', 'name' => 'South Winds Cattle Company', 'url' => 'https://www.southwindscattleco.com', 'order' => 8],
                ],
            ],
            [
                'name' => 'Non Veteran Owned Companies (Non Security)',
                'page' => AffiliateCategory::PAGE_PARTNERS,
                'order' => 4,
                'companies' => [
                    ['initials' => 'CBP', 'name' => 'Code Blue CPR Services', 'url' => 'https://codebluecprservices.com', 'show_in_nav' => true, 'order' => 2],
                    ['initials' => 'USL', 'name' => 'US Law Shield', 'url' => 'https://members.uslawshield.com/login', 'show_in_nav' => true, 'order' => 8],
                    ['initials' => 'TPT', 'name' => 'TN Professional Training Institute', 'url' => 'https://www.tnpti.com', 'order' => 9],
                ],
            ],
            [
                'name' => 'NRA Resources',
                'page' => AffiliateCategory::PAGE_NRA,
                'order' => 1,
                'companies' => [
                    [
                        'initials' => 'JNRA',
                        'name' => 'Join NRA',
                        'url' => 'https://membership.nra.org/recruiters/Join/XI048340',
                        'blurb' => 'Join the National Rifle Association and support our mission to protect the Second Amendment.',
                        'show_in_nra_nav' => true,
                        'order' => 1,
                    ],
                    [
                        'initials' => 'TNPTI',
                        'name' => 'TNPTI',
                        'url' => 'https://www.tnpti.com/',
                        'blurb' => 'Tennessee Peace Officer Training Institute — police training and certification.',
                        'show_in_nra_nav' => true,
                        'order' => 2,
                    ],
                    [
                        'initials' => 'SSC',
                        'name' => 'SouthwindS Cattle Company',
                        'url' => 'https://www.southwindscattleco.com/',
                        'blurb' => 'Cattle ranching and agriculture in Tennessee.',
                        'show_in_nra_nav' => true,
                        'order' => 3,
                    ],
                    [
                        'initials' => 'R1T',
                        'name' => 'Raven 1 Tactical',
                        'url' => 'https://raven1tactical.com/',
                        'blurb' => 'Tactical training and equipment for law enforcement and military.',
                        'show_in_nra_nav' => true,
                        'order' => 4,
                    ],
                    [
                        'initials' => 'BL',
                        'name' => 'Blue Line Security',
                        'url' => 'https://www.nashvillebluelinesecurity.com/services',
                        'blurb' => 'Security services and training for businesses and individuals.',
                        'show_in_nra_nav' => true,
                        'order' => 5,
                    ],
                    [
                        'initials' => 'TR',
                        'name' => 'Tactical Rifles and Ammo',
                        'url' => 'https://tacticalriflesandammollc.com/',
                        'blurb' => 'Tactical rifles and ammunition for law enforcement and military.',
                        'show_in_nra_nav' => true,
                        'order' => 6,
                    ],
                ],
            ],
        ];

        foreach ($sections as $section) {
            $category = AffiliateCategory::query()->updateOrCreate(
                ['slug' => Str::slug($section['name'])],
                [
                    'name' => $section['name'],
                    'page' => $section['page'],
                    'order' => $section['order'],
                    'is_active' => true,
                ]
            );

            foreach ($section['companies'] as $company) {
                Affiliate::query()->updateOrCreate(
                    [
                        'affiliate_category_id' => $category->id,
                        'url' => $company['url'],
                    ],
                    [
                        'name' => $company['name'],
                        'initials' => $company['initials'],
                        'blurb' => $company['blurb'] ?? null,
                        'order' => $company['order'] ?? 0,
                        'is_active' => true,
                        'show_in_nav' => (bool) ($company['show_in_nav'] ?? false),
                        'show_in_nra_nav' => (bool) ($company['show_in_nra_nav'] ?? false),
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MainCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $mainLabels = [
            'muzik-ekipleri' => 'Muzik Ekipleri',
            'etkinlik-organizasyon' => 'Etkinlik Organizasyon',
            'sahne-dekor-ekipman' => 'Sahne Dekor Ekipman',
            'fotograf-ve-video' => 'Fotograf ve Video',
            'etkinlik-mekanlari' => 'Etkinlik Mekanlari',
            'ikram-ve-catering' => 'Ikram ve Catering',
            'gelin-arabasi-ve-transfer' => 'Gelin Arabasi ve Transfer',
            'guzellik-ve-hazirlik' => 'Guzellik ve Hazirlik',
            'davetiye-hediyelik-cicek' => 'Davetiye Hediyelik ve Cicek',
        ];

        $rows = require database_path('seeders/data/categories_active_ready_v2.php');

        $mainOrder = array_values(array_unique(array_column($rows, 'main_slug')));
        $mainSort = 10;
        foreach ($mainOrder as $mainSlug) {
            MainCategory::query()->updateOrCreate(
                ['slug' => $mainSlug],
                [
                    'name' => $mainLabels[$mainSlug] ?? Str::title(str_replace('-', ' ', $mainSlug)),
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => $mainSort,
                ]
            );
            $mainSort += 10;
        }

        $categorySortByMain = [];
        foreach ($rows as $row) {
            $main = MainCategory::query()->where('slug', $row['main_slug'])->first();
            if (!$main) {
                continue;
            }

            $categorySortByMain[$main->id] = ($categorySortByMain[$main->id] ?? 0) + 10;
            $name = Str::title(str_replace('-', ' ', $row['slug']));

            Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'main_category_id' => $main->id,
                    'name' => $name,
                    'short_description' => null,
                    'description' => null,
                    'cover_image_path' => null,
                    'seo_title' => $name,
                    'seo_description' => null,
                    'is_active' => true,
                    'is_indexable' => true,
                    'sort_order' => $categorySortByMain[$main->id],
                ]
            );
        }
    }
}

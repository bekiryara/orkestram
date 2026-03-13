<?php

use App\Models\CityPage;
use App\Models\Category;
use App\Models\ListingAttributeValue;
use App\Models\Listing;
use App\Models\MainCategory;
use App\Models\Page;
use App\Services\Locations\LocationSnapshotImporter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('content:import {type} {file} {--site=} {--published}', function () {
    $type = (string) $this->argument('type');
    $file = (string) $this->argument('file');
    $site = (string) ($this->option('site') ?: 'orkestram.net');
    $published = (bool) $this->option('published');

    if (!is_file($file)) {
        $this->error("CSV bulunamadi: $file");
        return 1;
    }

    $map = [
        'pages' => Page::class,
        'listings' => Listing::class,
        'city-pages' => CityPage::class,
    ];
    if (!array_key_exists($type, $map)) {
        $this->error("type gecersiz. Kullan: pages | listings | city-pages");
        return 1;
    }

    $rows = array_map('str_getcsv', file($file));
    if (count($rows) < 2) {
        $this->error('CSV bos veya header disinda satir yok.');
        return 1;
    }

    $headers = array_map(static fn ($h) => trim((string) $h), $rows[0]);
    $count = 0;

    for ($i = 1; $i < count($rows); $i++) {
        $r = $rows[$i];
        if (count(array_filter($r, fn ($x) => trim((string) $x) !== '')) === 0) {
            continue;
        }

        $data = [];
        foreach ($headers as $idx => $h) {
            if ($h === '') {
                continue;
            }
            $data[$h] = isset($r[$idx]) ? trim((string) $r[$idx]) : null;
        }

        $data['site'] = $site;
        if ($published) {
            $data['status'] = 'published';
            if (empty($data['published_at'])) {
                $data['published_at'] = now();
            }
        }

        if (empty($data['slug'])) {
            $this->warn("satir $i atlandi: slug bos");
            continue;
        }

        $model = $map[$type];
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model::query()->updateOrCreate(
            ['site' => $site, 'slug' => $data['slug']],
            $data
        );
        $count++;
    }

    $this->info("Import tamam: $count satir");
    return 0;
})->purpose('CSV dosyasindan pages/listings/city-pages import eder');

Artisan::command('listings:repair-title-category {manifest} {--site=} {--dry-run}', function () {
    $manifest = (string) $this->argument('manifest');
    $site = trim((string) ($this->option('site') ?: 'orkestram.net'));
    $dryRun = (bool) $this->option('dry-run');

    if (!is_file($manifest)) {
        $this->error("Manifest bulunamadi: $manifest");
        return 1;
    }

    $rows = array_map('str_getcsv', file($manifest));
    if (count($rows) < 2) {
        $this->error('Manifest bos veya sadece header var.');
        return 1;
    }

    $normalize = static function (?string $value): string {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = mb_strtolower($value, 'UTF-8');
        $value = str_replace([' ', '-', '.'], '_', $value);
        return trim($value, '_');
    };

    $headers = array_map($normalize, $rows[0]);
    $indexMap = array_flip($headers);
    $pick = static function (array $indexMap, array $candidates): ?int {
        foreach ($candidates as $key) {
            if (array_key_exists($key, $indexMap)) {
                return (int) $indexMap[$key];
            }
        }

        return null;
    };

    $slugIdx = $pick($indexMap, ['slug', 'listing_slug', 'ilan_slug']);
    $nameIdx = $pick($indexMap, ['name', 'title', 'listing_name', 'ilan_adi', 'ilan_basligi']);
    $categorySlugIdx = $pick($indexMap, ['category_slug', 'kategori_slug']);

    if ($slugIdx === null || $categorySlugIdx === null) {
        $this->error('Gerekli kolonlar bulunamadi. Beklenen: slug + category_slug (name/title opsiyonel).');
        return 1;
    }

    $stats = [
        'rows' => 0,
        'listing_found' => 0,
        'updated' => 0,
        'unchanged' => 0,
        'missing_listing' => 0,
        'missing_category' => 0,
        'invalid_row' => 0,
    ];

    for ($i = 1; $i < count($rows); $i++) {
        $stats['rows']++;
        $row = $rows[$i];

        $listingSlug = trim((string) ($row[$slugIdx] ?? ''));
        $targetCategorySlug = trim((string) ($row[$categorySlugIdx] ?? ''));
        $targetName = $nameIdx !== null ? trim((string) ($row[$nameIdx] ?? '')) : '';

        if ($listingSlug === '' || $targetCategorySlug === '') {
            $stats['invalid_row']++;
            $this->warn("satir {$i}: slug/category_slug bos, atlandi");
            continue;
        }

        $listing = Listing::query()->where('site', $site)->where('slug', $listingSlug)->first();
        if (!$listing) {
            $stats['missing_listing']++;
            $this->warn("satir {$i}: listing bulunamadi -> {$listingSlug}");
            continue;
        }
        $stats['listing_found']++;

        $category = Category::query()->where('slug', $targetCategorySlug)->first();
        if (!$category) {
            $stats['missing_category']++;
            $this->warn("satir {$i}: category bulunamadi -> {$targetCategorySlug}");
            continue;
        }

        $updates = [];
        if ($listing->category_id !== $category->id) {
            $updates['category_id'] = $category->id;
        }
        if ($targetName !== '' && $listing->name !== $targetName) {
            $updates['name'] = $targetName;
        }

        if ($updates === []) {
            $stats['unchanged']++;
            continue;
        }

        if ($dryRun) {
            $this->line(
                sprintf(
                    '[DRY] %s | name: "%s" -> "%s" | category: "%s" -> "%s"',
                    $listingSlug,
                    $listing->name,
                    $updates['name'] ?? $listing->name,
                    (string) optional($listing->category)->slug,
                    $targetCategorySlug
                )
            );
        } else {
            $listing->fill($updates)->save();
            $this->line("[OK] {$listingSlug} guncellendi");
        }

        $stats['updated']++;
    }

    $mode = $dryRun ? 'DRY-RUN' : 'APPLY';
    $this->info("listings:repair-title-category {$mode} tamamlandi");
    $this->line('rows=' . $stats['rows']);
    $this->line('listing_found=' . $stats['listing_found']);
    $this->line('updated=' . $stats['updated']);
    $this->line('unchanged=' . $stats['unchanged']);
    $this->line('missing_listing=' . $stats['missing_listing']);
    $this->line('missing_category=' . $stats['missing_category']);
    $this->line('invalid_row=' . $stats['invalid_row']);

    return 0;
})->purpose('Manifestteki slug + category_slug (+name/title) verisine gore sadece listing name/category onarir');

Artisan::command('locations:import {--from=} {--truncate}', function (LocationSnapshotImporter $importer) {
    $from = (string) $this->option('from');
    if ($from === '') {
        $repoRoot = dirname(base_path(), 3);
        $from = $repoRoot . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'category-catalog' . DIRECTORY_SEPARATOR . 'ready' . DIRECTORY_SEPARATOR . 'locations_v1';
    }

    $this->line("snapshot: $from");
    try {
        $result = $importer->import($from, (bool) $this->option('truncate'));
    } catch (\Throwable $e) {
        $this->error('locations import FAIL: ' . $e->getMessage());
        return 1;
    }

    $this->info('locations import PASS');
    $this->line('cities=' . $result['cities']);
    $this->line('districts=' . $result['districts']);
    $this->line('neighborhoods=' . $result['neighborhoods']);
    return 0;
})->purpose('Deterministik locations_v1 snapshot dosyasindan city/district/neighborhood import eder');

Artisan::command('smoke:prepare-range-fixture {--site=}', function () {
    $site = trim((string) ($this->option('site') ?: 'orkestram.net'));

    $main = MainCategory::query()->updateOrCreate(
        ['slug' => 'smoke-main-kategori'],
        [
            'name' => 'Smoke Main Kategori',
            'is_active' => true,
            'sort_order' => 9990,
        ]
    );

    $category = Category::query()->updateOrCreate(
        ['slug' => 'smoke-range-kategori'],
        [
            'main_category_id' => $main->id,
            'name' => 'Smoke Range Kategori',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 9991,
        ]
    );

    $attribute = $category->attributes()->updateOrCreate(
        ['key' => 'smoke_ekip_sayisi'],
        [
            'label' => 'Smoke Ekip Sayisi',
            'field_type' => 'number',
            'filter_mode' => 'range',
            'is_required' => false,
            'is_filterable' => true,
            'is_visible_in_card' => false,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 9991,
        ]
    );

    $listingA = Listing::query()->updateOrCreate(
        ['slug' => 'smoke-range-ilan-a', 'site' => $site],
        [
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'name' => 'Smoke Range Ilan A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Yalova',
            'district' => 'Merkez',
        ]
    );

    $listingB = Listing::query()->updateOrCreate(
        ['slug' => 'smoke-range-ilan-b', 'site' => $site],
        [
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'name' => 'Smoke Range Ilan B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Yalova',
            'district' => 'Merkez',
        ]
    );

    ListingAttributeValue::query()->updateOrCreate(
        ['listing_id' => $listingA->id, 'category_attribute_id' => $attribute->id],
        ['value_number' => 4, 'normalized_value' => '4']
    );

    ListingAttributeValue::query()->updateOrCreate(
        ['listing_id' => $listingB->id, 'category_attribute_id' => $attribute->id],
        ['value_number' => 8, 'normalized_value' => '8']
    );

    $this->line('category_slug=smoke-range-kategori');
    $this->line('attr_key=attr_smoke_ekip_sayisi');
    $this->line('listing_a=Smoke Range Ilan A');
    $this->line('listing_b=Smoke Range Ilan B');

    return 0;
})->purpose('Smoke icin deterministik range filtre fixture verisini idempotent hazirlar');

Artisan::command('smoke:prepare-bando-fixture {--site=}', function () {
    $site = trim((string) ($this->option('site') ?: 'orkestram.net'));

    $main = MainCategory::query()->updateOrCreate(
        ['slug' => 'muzik-ekipleri'],
        [
            'name' => 'Muzik Ekipleri',
            'is_active' => true,
            'sort_order' => 10,
        ]
    );

    $category = Category::query()->updateOrCreate(
        ['slug' => 'bando-takimi'],
        [
            'main_category_id' => $main->id,
            'name' => 'Bando Takimi',
            'is_active' => true,
            'is_indexable' => true,
            'sort_order' => 20,
        ]
    );

    $attributes = [
        [
            'key' => 'sure_dk',
            'label' => 'Sure (dk)',
            'field_type' => 'select',
            'filter_mode' => 'exact',
            'options_json' => ['30', '45', '60', '90', '120'],
            'sort_order' => 10,
        ],
        [
            'key' => 'enstruman_sayisi',
            'label' => 'Enstruman Sayisi',
            'field_type' => 'number',
            'filter_mode' => 'range',
            'options_json' => null,
            'sort_order' => 20,
        ],
        [
            'key' => 'solist_sayisi',
            'label' => 'Solist Sayisi',
            'field_type' => 'number',
            'filter_mode' => 'range',
            'options_json' => null,
            'sort_order' => 30,
        ],
        [
            'key' => 'solist_cinsiyeti',
            'label' => 'Solist Cinsiyeti',
            'field_type' => 'select',
            'filter_mode' => 'exact',
            'options_json' => ['Kadin', 'Erkek', 'Karma', 'Belirtilmemis'],
            'sort_order' => 40,
        ],
        [
            'key' => 'enstrumanlar',
            'label' => 'Enstrumanlar',
            'field_type' => 'multiselect',
            'filter_mode' => 'exact',
            'options_json' => ['Trampet', 'Davul', 'Trompet', 'Trombon', 'Saksafon', 'Klarnet', 'Tuba', 'Zil'],
            'sort_order' => 50,
        ],
        [
            'key' => 'sahne_aldigi_yerler',
            'label' => 'Sahne Aldigi Yerler',
            'field_type' => 'multiselect',
            'filter_mode' => 'exact',
            'options_json' => ['Dugun', 'Nisan', 'Kina', 'Festival', 'Kurumsal Etkinlik', 'Sokak Gosterisi', 'Acik Hava'],
            'sort_order' => 60,
        ],
        [
            'key' => 'kostum',
            'label' => 'Kostum',
            'field_type' => 'multiselect',
            'filter_mode' => 'exact',
            'options_json' => ['Aski', 'Sapka', 'Papyon'],
            'sort_order' => 70,
        ],
    ];

    $attributeMap = [];
    foreach ($attributes as $row) {
        $existing = $category->attributes()->where('key', $row['key'])->first();
        if ($existing) {
            // Preserve current visibility flags set by admin; update only non-visibility fields.
            $existing->update([
                'label' => $row['label'],
                'field_type' => $row['field_type'],
                'filter_mode' => $row['filter_mode'],
                'options_json' => $row['options_json'],
                'is_required' => false,
                'is_filterable' => true,
                'is_active' => true,
                'sort_order' => $row['sort_order'],
            ]);
            $attribute = $existing;
        } else {
            $attribute = $category->attributes()->create([
                'key' => $row['key'],
                'label' => $row['label'],
                'field_type' => $row['field_type'],
                'filter_mode' => $row['filter_mode'],
                'options_json' => $row['options_json'],
                'is_required' => false,
                'is_filterable' => true,
                'is_visible_in_card' => false,
                'is_visible_in_detail' => true,
                'is_active' => true,
                'sort_order' => $row['sort_order'],
            ]);
        }
        $attributeMap[$row['key']] = $attribute;
    }

    $listingA = Listing::query()->updateOrCreate(
        ['slug' => 'test-bando-a', 'site' => $site],
        [
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'name' => 'TEST Bando Senaryo A',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'summary' => 'Smoke fixture A bando senaryosu',
            'content' => 'Smoke fixture A bando senaryosu icerik metni filtre testleri icin deterministik olarak tutulur.',
            'price_label' => '1000 TL',
        ]
    );

    $listingB = Listing::query()->updateOrCreate(
        ['slug' => 'test-bando-b', 'site' => $site],
        [
            'site_scope' => 'single',
            'coverage_mode' => 'location_only',
            'name' => 'TEST Bando Senaryo B',
            'status' => 'published',
            'category_id' => $category->id,
            'city' => 'Izmir',
            'district' => 'Konak',
            'summary' => 'Smoke fixture B bando senaryosu',
            'content' => 'Smoke fixture B bando senaryosu icerik metni filtre testleri icin deterministik olarak tutulur.',
            'price_label' => '2000 TL',
        ]
    );

    $write = function (Listing $listing, string $key, ?string $valueText, ?float $valueNumber, ?bool $valueBool, ?array $valueJson, ?string $normalized = null) use ($attributeMap): void {
        $attr = $attributeMap[$key] ?? null;
        if (!$attr) {
            return;
        }

        ListingAttributeValue::query()->updateOrCreate(
            ['listing_id' => $listing->id, 'category_attribute_id' => $attr->id],
            [
                'value_text' => $valueText,
                'value_number' => $valueNumber,
                'value_bool' => $valueBool,
                'value_json' => $valueJson,
                'normalized_value' => $normalized,
            ]
        );
    };

    // Listing A values
    $write($listingA, 'sure_dk', '60', null, null, null, '60');
    $write($listingA, 'enstruman_sayisi', null, 5.0, null, null, '5');
    $write($listingA, 'solist_sayisi', null, 1.0, null, null, '1');
    $write($listingA, 'solist_cinsiyeti', 'Kadin', null, null, null, 'kadin');
    $write($listingA, 'enstrumanlar', 'Trompet, Davul', null, null, ['trompet', 'davul'], null);
    $write($listingA, 'sahne_aldigi_yerler', 'Dugun', null, null, ['dugun'], null);
    $write($listingA, 'kostum', 'Sapka', null, null, ['sapka'], null);

    // Listing B values
    $write($listingB, 'sure_dk', '120', null, null, null, '120');
    $write($listingB, 'enstruman_sayisi', null, 9.0, null, null, '9');
    $write($listingB, 'solist_sayisi', null, 2.0, null, null, '2');
    $write($listingB, 'solist_cinsiyeti', 'Erkek', null, null, null, 'erkek');
    $write($listingB, 'enstrumanlar', 'Saksafon, Klarnet', null, null, ['saksafon', 'klarnet'], null);
    $write($listingB, 'sahne_aldigi_yerler', 'Festival, Kurumsal Etkinlik', null, null, ['festival', 'kurumsal etkinlik'], null);
    $write($listingB, 'kostum', 'Aski, Papyon', null, null, ['aski', 'papyon'], null);

    $this->line('category_slug=bando-takimi');
    $this->line('listing_a=TEST Bando Senaryo A');
    $this->line('listing_b=TEST Bando Senaryo B');
    $this->line('slug_a=test-bando-a');
    $this->line('slug_b=test-bando-b');

    return 0;
})->purpose('Smoke icin bando-takimi kategori filtre senaryosunu deterministik test ilanlariyla hazirlar');

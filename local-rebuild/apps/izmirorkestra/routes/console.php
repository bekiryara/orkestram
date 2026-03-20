<?php

use App\Models\CityPage;
use App\Models\Category;
use App\Models\ListingAttributeValue;
use App\Models\Listing;
use App\Models\MainCategory;
use App\Models\Page;
use App\Services\Locations\LocationSnapshotImporter;
use Database\Seeders\LocalAccountFixtureSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

$ensureBandoFixtureCatalog = function (): array {
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

    return [$category, $attributeMap];
};

$writeFixtureAttributeValues = function (Listing $listing, array $attributeMap, array $values): void {
    foreach ($values as $key => $payload) {
        $attribute = $attributeMap[$key] ?? null;
        if (!$attribute) {
            continue;
        }

        ListingAttributeValue::query()->updateOrCreate(
            ['listing_id' => $listing->id, 'category_attribute_id' => $attribute->id],
            [
                'value_text' => $payload['value_text'] ?? null,
                'value_number' => $payload['value_number'] ?? null,
                'value_bool' => $payload['value_bool'] ?? null,
                'value_json' => $payload['value_json'] ?? null,
                'normalized_value' => $payload['normalized_value'] ?? null,
            ]
        );
    }
};

$syncReviewDemoMedia = function (array $fixture): array {
    $slug = (string) ($fixture['slug'] ?? '');
    if ($slug === '') {
        throw new RuntimeException('review demo fixture slug bos');
    }

    $sourceDir = base_path('database/seeders/data/review_demo_media/' . $slug);
    if (!is_dir($sourceDir)) {
        throw new RuntimeException('review demo media source bulunamadi: ' . $sourceDir);
    }

    $disk = Storage::disk('public');
    $paths = array_merge([
        (string) ($fixture['cover_image_path'] ?? ''),
    ], array_map(static fn ($path) => (string) $path, (array) ($fixture['gallery_json'] ?? [])));

    foreach ($paths as $path) {
        $normalized = ltrim(trim($path), '/');
        if ($normalized === '' || !str_starts_with($normalized, 'storage/')) {
            continue;
        }

        $relative = substr($normalized, strlen('storage/'));
        $targetName = basename($relative);
        $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $targetName;
        if (!is_file($sourcePath)) {
            throw new RuntimeException('review demo media file eksik: ' . $sourcePath);
        }

        $directory = dirname($relative);
        if ($directory !== '' && $directory !== '.') {
            $disk->makeDirectory($directory);
        }

        $sourceHash = hash_file('sha256', $sourcePath);
        $needsCopy = true;
        if ($disk->exists($relative)) {
            $existingHash = hash('sha256', (string) $disk->get($relative));
            $needsCopy = !hash_equals($existingHash, $sourceHash);
        }

        if ($needsCopy) {
            $stream = fopen($sourcePath, 'rb');
            if ($stream === false) {
                throw new RuntimeException('review demo media acilamadi: ' . $sourcePath);
            }

            try {
                $disk->put($relative, $stream);
            } finally {
                fclose($stream);
            }
        }
    }

    return $fixture;
};

Artisan::command('local:prepare-account-fixture', function () {
    $result = app(LocalAccountFixtureSeeder::class)->run();

    foreach ($result as $row) {
        $this->line('account=' . $row['username'] . ':' . $row['role']);
    }

    $this->info('local account fixture PASS');
    return 0;
})->purpose('Deterministic local admin/owner/customer/support hesap fixture katmanini idempotent olarak hazirlar');

Artisan::command('local:prepare-reset-recovery {--with-locations} {--with-smoke} {--with-review-demo}', function () {
    $sites = ['orkestram.net', 'izmirorkestra.net'];

    app(LocalAccountFixtureSeeder::class)->run();
    $this->line('accounts=prepared');

    if ((bool) $this->option('with-locations')) {
        Artisan::call('locations:import');
        $this->output->write(Artisan::output());
    }

    if ((bool) $this->option('with-smoke')) {
        foreach ($sites as $site) {
            Artisan::call('smoke:prepare-range-fixture', ['--site' => $site]);
            $this->output->write(Artisan::output());
            Artisan::call('smoke:prepare-bando-fixture', ['--site' => $site]);
            $this->output->write(Artisan::output());
        }
    }

    if ((bool) $this->option('with-review-demo')) {
        foreach ($sites as $site) {
            Artisan::call('demo:prepare-bando-review-fixture', ['--site' => $site]);
            $this->output->write(Artisan::output());
        }
    }

    $this->info('local reset recovery PASS');
    return 0;
})->purpose('DB reset sonrasi local hesap, smoke ve review fixture katmanlarini resmi sirayla tekrar hazirlar');

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

Artisan::command('smoke:prepare-bando-fixture {--site=}', function () use ($ensureBandoFixtureCatalog, $writeFixtureAttributeValues) {
    $site = trim((string) ($this->option('site') ?: 'orkestram.net'));
    [$category, $attributeMap] = $ensureBandoFixtureCatalog();

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
            'meta_json' => [
                'fixture_layer' => 'smoke',
                'fixture_key' => 'smoke-bando-a',
            ],
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
            'meta_json' => [
                'fixture_layer' => 'smoke',
                'fixture_key' => 'smoke-bando-b',
            ],
        ]
    );

    $writeFixtureAttributeValues($listingA, $attributeMap, [
        'sure_dk' => ['value_text' => '60', 'normalized_value' => '60'],
        'enstruman_sayisi' => ['value_number' => 5.0, 'normalized_value' => '5'],
        'solist_sayisi' => ['value_number' => 1.0, 'normalized_value' => '1'],
        'solist_cinsiyeti' => ['value_text' => 'Kadin', 'normalized_value' => 'kadin'],
        'enstrumanlar' => ['value_text' => 'Trompet, Davul', 'value_json' => ['trompet', 'davul']],
        'sahne_aldigi_yerler' => ['value_text' => 'Dugun', 'value_json' => ['dugun']],
        'kostum' => ['value_text' => 'Sapka', 'value_json' => ['sapka']],
    ]);

    $writeFixtureAttributeValues($listingB, $attributeMap, [
        'sure_dk' => ['value_text' => '120', 'normalized_value' => '120'],
        'enstruman_sayisi' => ['value_number' => 9.0, 'normalized_value' => '9'],
        'solist_sayisi' => ['value_number' => 2.0, 'normalized_value' => '2'],
        'solist_cinsiyeti' => ['value_text' => 'Erkek', 'normalized_value' => 'erkek'],
        'enstrumanlar' => ['value_text' => 'Saksafon, Klarnet', 'value_json' => ['saksafon', 'klarnet']],
        'sahne_aldigi_yerler' => ['value_text' => 'Festival, Kurumsal Etkinlik', 'value_json' => ['festival', 'kurumsal etkinlik']],
        'kostum' => ['value_text' => 'Aski, Papyon', 'value_json' => ['aski', 'papyon']],
    ]);

    $this->line('category_slug=bando-takimi');
    $this->line('listing_a=TEST Bando Senaryo A');
    $this->line('listing_b=TEST Bando Senaryo B');
    $this->line('slug_a=test-bando-a');
    $this->line('slug_b=test-bando-b');

    return 0;
})->purpose('Smoke icin bando-takimi kategori filtre senaryosunu deterministik test ilanlariyla hazirlar');

Artisan::command('demo:prepare-bando-review-fixture {--site=}', function () use ($ensureBandoFixtureCatalog, $writeFixtureAttributeValues, $syncReviewDemoMedia) {
    $requestedSite = trim((string) ($this->option('site') ?: ''));
    $fixturesBySite = [
        'orkestram.net' => [
            [
                'slug' => 'demo-bando-sahil-seremonisi',
                'name' => 'Demo Bando Sahil Seremonisi',
                'city' => 'Izmir',
                'district' => 'Konak',
                'summary' => 'Review demo fixture: sahil seremonisi akisina uygun bando listingi.',
                'content' => 'Review demo fixture icerigi, design-preview tarafinda editorial listing detail kontrolu icin repo ici whitelist veri olarak korunur.',
                'price_label' => '18.500 TL',
                'phone' => '05320000001',
                'whatsapp' => '905320000001',
                'cover_image_path' => 'storage/uploads/listings/demo-bando-sahil-seremonisi/cover.jpg',
                'gallery_json' => [
                    'storage/uploads/listings/demo-bando-sahil-seremonisi/gallery-1.jpg',
                    'storage/uploads/listings/demo-bando-sahil-seremonisi/gallery-2.jpg',
                    'storage/uploads/listings/demo-bando-sahil-seremonisi/gallery-3.jpg',
                    'storage/uploads/listings/demo-bando-sahil-seremonisi/gallery-4.jpg',
                ],
                'features_json' => ['Karsilama seremonisi', 'Kortej girisi', 'Acilis fanfari'],
                'meta_json' => [
                    'fixture_layer' => 'review_demo',
                    'fixture_key' => 'demo-bando-sahil-seremonisi',
                    'fixture_media_source' => 'repo-canonical',
                ],
                'attributes' => [
                    'sure_dk' => ['value_text' => '90', 'normalized_value' => '90'],
                    'enstruman_sayisi' => ['value_number' => 8.0, 'normalized_value' => '8'],
                    'solist_sayisi' => ['value_number' => 2.0, 'normalized_value' => '2'],
                    'solist_cinsiyeti' => ['value_text' => 'Karma', 'normalized_value' => 'karma'],
                    'enstrumanlar' => ['value_text' => 'Trompet, Trombon, Saksafon', 'value_json' => ['trompet', 'trombon', 'saksafon']],
                    'sahne_aldigi_yerler' => ['value_text' => 'Dugun, Acik Hava', 'value_json' => ['dugun', 'acik hava']],
                    'kostum' => ['value_text' => 'Sapka, Papyon', 'value_json' => ['sapka', 'papyon']],
                ],
            ],
        ],
        'izmirorkestra.net' => [
            [
                'slug' => 'demo-bando-kordon-alayi',
                'name' => 'Demo Bando Kordon Alayi',
                'city' => 'Izmir',
                'district' => 'Konak',
                'summary' => 'Review demo fixture: kordon alayi akisi icin whitelist listing.',
                'content' => 'Review demo fixture icerigi, design-preview tarafinda ikinci app parity kontrolu icin repo ici whitelist veri olarak korunur.',
                'price_label' => '22.000 TL',
                'phone' => '05320000002',
                'whatsapp' => '905320000002',
                'cover_image_path' => 'storage/uploads/listings/demo-bando-kordon-alayi/cover.jpg',
                'gallery_json' => [
                    'storage/uploads/listings/demo-bando-kordon-alayi/gallery-1.jpg',
                    'storage/uploads/listings/demo-bando-kordon-alayi/gallery-2.jpg',
                    'storage/uploads/listings/demo-bando-kordon-alayi/gallery-3.jpg',
                    'storage/uploads/listings/demo-bando-kordon-alayi/gallery-4.jpg',
                ],
                'features_json' => ['Kordon girisi', 'Ritim seti', 'Rota uyumlu kortej'],
                'meta_json' => [
                    'fixture_layer' => 'review_demo',
                    'fixture_key' => 'demo-bando-kordon-alayi',
                    'fixture_media_source' => 'repo-canonical',
                ],
                'attributes' => [
                    'sure_dk' => ['value_text' => '120', 'normalized_value' => '120'],
                    'enstruman_sayisi' => ['value_number' => 10.0, 'normalized_value' => '10'],
                    'solist_sayisi' => ['value_number' => 1.0, 'normalized_value' => '1'],
                    'solist_cinsiyeti' => ['value_text' => 'Erkek', 'normalized_value' => 'erkek'],
                    'enstrumanlar' => ['value_text' => 'Davul, Trompet, Klarnet', 'value_json' => ['davul', 'trompet', 'klarnet']],
                    'sahne_aldigi_yerler' => ['value_text' => 'Festival, Sokak Gosterisi', 'value_json' => ['festival', 'sokak gosterisi']],
                    'kostum' => ['value_text' => 'Aski, Sapka', 'value_json' => ['aski', 'sapka']],
                ],
            ],
        ],
    ];

    $sites = $requestedSite !== '' ? [$requestedSite] : array_keys($fixturesBySite);
    [$category, $attributeMap] = $ensureBandoFixtureCatalog();

    foreach ($sites as $site) {
        $siteFixtures = $fixturesBySite[$site] ?? null;
        if ($siteFixtures === null) {
            $this->error("site gecersiz: $site");
            return 1;
        }

        foreach ($siteFixtures as $fixture) {
            $fixture = $syncReviewDemoMedia($fixture);

            $listing = Listing::query()->updateOrCreate(
                ['slug' => $fixture['slug'], 'site' => $site],
                [
                    'site_scope' => 'single',
                    'coverage_mode' => 'location_only',
                    'name' => $fixture['name'],
                    'status' => 'published',
                    'category_id' => $category->id,
                    'city' => $fixture['city'],
                    'district' => $fixture['district'],
                    'summary' => $fixture['summary'],
                    'content' => $fixture['content'],
                    'price_label' => $fixture['price_label'],
                    'phone' => $fixture['phone'],
                    'whatsapp' => $fixture['whatsapp'],
                    'cover_image_path' => $fixture['cover_image_path'],
                    'gallery_json' => $fixture['gallery_json'],
                    'features_json' => $fixture['features_json'],
                    'meta_json' => $fixture['meta_json'],
                ]
            );

            $writeFixtureAttributeValues($listing, $attributeMap, $fixture['attributes']);
            $this->line("review_demo_site=$site");
            $this->line('review_demo_slug=' . $fixture['slug']);
            $this->line('review_demo_media_sync=ok');
        }
    }

    return 0;
})->purpose('Design-preview review icin whitelist bando demo fixture verisini smoke katmanindan ayri hazirlar');


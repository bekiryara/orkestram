<?php

namespace Database\Seeders;

use App\Models\CityPage;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $sites = [
            'orkestram.net' => [
                'title' => 'Orkestram ile Etkinligine Uygun Muzik',
                'excerpt' => 'Dugun, nisan, kurumsal etkinlikler ve ozel geceler icin profesyonel ekipler.',
                'listings' => [
                    ['slug' => 'beste-muzik', 'name' => 'Beste Muzik', 'city' => 'Izmir', 'district' => 'Konak', 'service_type' => 'Dugun Orkestrasi', 'price_label' => 'Orta Segment'],
                    ['slug' => 'grup-moda', 'name' => 'Grup Moda', 'city' => 'Izmir', 'district' => 'Karsiyaka', 'service_type' => 'Canli Muzik', 'price_label' => 'Premium'],
                    ['slug' => 'gelin-alma-bandosu', 'name' => 'Gelin Alma Bandosu', 'city' => 'Izmir', 'district' => 'Bornova', 'service_type' => 'Bando Takimi', 'price_label' => 'Baslangic'],
                ],
                'cities' => [
                    ['slug' => 'izmir-dugun-orkestrasi', 'title' => 'Izmir Dugun Orkestrasi', 'city' => 'Izmir', 'district' => null, 'service_slug' => 'dugun-orkestrasi'],
                    ['slug' => 'karsiyaka-canli-muzik', 'title' => 'Karsiyaka Canli Muzik', 'city' => 'Izmir', 'district' => 'Karsiyaka', 'service_slug' => 'canli-muzik'],
                ],
            ],
            'izmirorkestra.net' => [
                'title' => 'Izmir Orkestra Net ile Sehir Bazli Hizmetler',
                'excerpt' => 'Izmir ilce ve semtlerinde canli muzik, bando ve organizasyon ekipleri.',
                'listings' => [
                    ['slug' => 'izmir-bandosu', 'name' => 'Izmir Bandosu', 'city' => 'Izmir', 'district' => 'Balcova', 'service_type' => 'Bando Takimi', 'price_label' => 'Orta Segment'],
                    ['slug' => 'sunnet-organizasyon-ekibi', 'name' => 'Sunnet Organizasyon Ekibi', 'city' => 'Izmir', 'district' => 'Buca', 'service_type' => 'Sunnet Organizasyon', 'price_label' => 'Orta Segment'],
                    ['slug' => 'nisan-organizasyon-grubu', 'name' => 'Nisan Organizasyon Grubu', 'city' => 'Izmir', 'district' => 'Bayrakli', 'service_type' => 'Nisan Organizasyon', 'price_label' => 'Baslangic'],
                ],
                'cities' => [
                    ['slug' => 'izmir-bando-takimi', 'title' => 'Izmir Bando Takimi', 'city' => 'Izmir', 'district' => null, 'service_slug' => 'bando-takimi'],
                    ['slug' => 'bornova-dugun-orkestrasi', 'title' => 'Bornova Dugun Orkestrasi', 'city' => 'Izmir', 'district' => 'Bornova', 'service_slug' => 'dugun-orkestrasi'],
                ],
            ],
        ];

        foreach ($sites as $site => $cfg) {
            Page::query()->updateOrCreate(
                ['site' => $site, 'slug' => 'ana-sayfa'],
                [
                    'title' => $cfg['title'],
                    'template' => 'home',
                    'status' => 'published',
                    'excerpt' => $cfg['excerpt'],
                    'content' => "Bu icerik local v1 demo amacli uretilmistir.\nYonetim panelinden guncellenebilir.",
                    'seo_title' => $cfg['title'],
                    'seo_description' => $cfg['excerpt'],
                    'published_at' => $now,
                ]
            );

            foreach ($cfg['listings'] as $it) {
                Listing::query()->updateOrCreate(
                    ['site' => $site, 'slug' => $it['slug']],
                    [
                        'site_scope' => 'single',
                        'name' => $it['name'],
                        'status' => 'published',
                        'category_id' => $this->categoryIdForSeedSlug($it['slug']),
                        'city' => $it['city'],
                        'district' => $it['district'],
                        'service_type' => $it['service_type'],
                        'price_label' => $it['price_label'],
                        'summary' => $it['name'] . ' icin hizli tanitim metni.',
                        'content' => "Bu ilan yerel test verisidir.\nCanliya gecmeden once gercek icerik ile degistirilecek.",
                        'seo_title' => $it['name'] . ' | ' . $site,
                        'seo_description' => $it['city'] . ' icin ' . $it['service_type'] . ' hizmeti.',
                        'published_at' => $now,
                    ]
                );
            }

            foreach ($cfg['cities'] as $cp) {
                CityPage::query()->updateOrCreate(
                    ['site' => $site, 'slug' => $cp['slug']],
                    [
                        'city' => $cp['city'],
                        'district' => $cp['district'],
                        'service_slug' => $cp['service_slug'],
                        'title' => $cp['title'],
                        'status' => 'published',
                        'content' => "Bu sehir sayfasi local v1 icin olusturuldu.\nGercek landing metni ile guncellenecek.",
                        'seo_title' => $cp['title'] . ' | ' . $site,
                        'seo_description' => $cp['city'] . ' bolgesi icin hizmet landing sayfasi.',
                        'published_at' => $now,
                    ]
                );
            }
        }
    }

    private function categoryIdForSeedSlug(string $slug): ?int
    {
        $map = [
            'beste-muzik' => 'dugun-orkestrasi',
            'grup-moda' => 'canli-dugun-muzigi',
            'gelin-alma-bandosu' => 'gelin-alma-bandosu',
            'izmir-bandosu' => 'bando-takimi',
            'sunnet-organizasyon-ekibi' => 'sunnet-organizasyon',
            'nisan-organizasyon-grubu' => 'nisan-organizasyon',
        ];

        $categorySlug = $map[$slug] ?? null;
        if ($categorySlug === null) {
            return null;
        }

        return Category::query()->where('slug', $categorySlug)->value('id');
    }
}

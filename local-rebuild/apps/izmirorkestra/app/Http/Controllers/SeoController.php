<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CityPage;
use App\Models\Listing;
use App\Models\ListingServiceArea;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SeoController extends Controller
{
    public function robots(Request $request): Response
    {
        $base = rtrim($request->getSchemeAndHttpHost(), '/');
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Sitemap: {$base}/sitemap.xml\n";

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(Request $request): Response
    {
        $site = $this->siteFromRequest($request);
        $base = rtrim($request->getSchemeAndHttpHost(), '/');

        $urls = [];
        $urls[] = ['loc' => $base . '/', 'lastmod' => Carbon::now()->toDateString()];

        Page::query()->where('site', $site)->where('status', 'published')->get()->each(function (Page $p) use (&$urls, $base) {
            if ($p->slug === 'ana-sayfa') {
                return;
            }
            $urls[] = [
                'loc' => $base . '/sayfa/' . ltrim($p->slug, '/'),
                'lastmod' => optional($p->updated_at)->toDateString() ?: Carbon::now()->toDateString(),
            ];
        });

        Listing::query()->where('site', $site)->where('status', 'published')->get()->each(function (Listing $p) use (&$urls, $base) {
            $urls[] = [
                'loc' => $base . '/ilan/' . ltrim($p->slug, '/'),
                'lastmod' => optional($p->updated_at)->toDateString() ?: Carbon::now()->toDateString(),
            ];
        });

        Category::query()
            ->where('is_active', true)
            ->where('is_indexable', true)
            ->whereHas('listings', function ($query) use ($site) {
                $query->visibleForSite($site)->where('status', 'published');
            })
            ->get()
            ->each(function (Category $category) use (&$urls, $base, $site) {
                $urls[] = [
                    'loc' => $base . '/hizmet/' . ltrim($category->slug, '/'),
                    'lastmod' => optional($category->updated_at)->toDateString() ?: Carbon::now()->toDateString(),
                ];

                $cities = $this->categoryCityOptions($site, (int) $category->id);
                foreach ($cities as $city) {
                    $urls[] = [
                        'loc' => $base . '/hizmet/' . ltrim($category->slug, '/') . '/' . Str::slug($city),
                        'lastmod' => optional($category->updated_at)->toDateString() ?: Carbon::now()->toDateString(),
                    ];

                    $districts = $this->categoryDistrictOptions($site, (int) $category->id, $city);
                    foreach ($districts as $district) {
                        $urls[] = [
                            'loc' => $base . '/hizmet/' . ltrim($category->slug, '/') . '/' . Str::slug($city) . '/' . Str::slug($district),
                            'lastmod' => optional($category->updated_at)->toDateString() ?: Carbon::now()->toDateString(),
                        ];
                    }
                }
            });

        CityPage::query()->where('site', $site)->where('status', 'published')->get()->each(function (CityPage $p) use (&$urls, $base) {
            $urls[] = [
                'loc' => $base . '/sehir/' . ltrim($p->slug, '/'),
                'lastmod' => optional($p->updated_at)->toDateString() ?: Carbon::now()->toDateString(),
            ];
        });

        $xml = view('seo.sitemap', ['urls' => $urls])->render();
        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function siteFromRequest(Request $request): string
    {
        $host = strtolower($request->getHost());
        $httpHost = strtolower($request->getHttpHost());
        if (str_contains($httpHost, ':8181') || str_contains($host, 'izmirorkestra')) {
            return 'izmirorkestra.net';
        }
        return 'orkestram.net';
    }

    /**
     * @return array<int, string>
     */
    private function categoryCityOptions(string $site, int $categoryId): array
    {
        $baseQuery = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $categoryId);

        $locationCities = (clone $baseQuery)
            ->whereIn('coverage_mode', ['location_only', 'hybrid'])
            ->pluck('city');

        $serviceAreaListingIds = (clone $baseQuery)
            ->whereIn('coverage_mode', ['service_area_only', 'hybrid'])
            ->pluck('id');

        $serviceAreaCities = ListingServiceArea::query()
            ->whereIn('listing_id', $serviceAreaListingIds)
            ->pluck('city');

        return $locationCities
            ->merge($serviceAreaCities)
            ->filter(fn($v) => !empty($v))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function categoryDistrictOptions(string $site, int $categoryId, string $city): array
    {
        $baseQuery = Listing::query()
            ->visibleForSite($site)
            ->where('status', 'published')
            ->where('category_id', $categoryId);

        $locationDistricts = (clone $baseQuery)
            ->whereIn('coverage_mode', ['location_only', 'hybrid'])
            ->where('city', $city)
            ->pluck('district');

        $serviceAreaListingIds = (clone $baseQuery)
            ->whereIn('coverage_mode', ['service_area_only', 'hybrid'])
            ->pluck('id');

        $serviceAreaDistricts = ListingServiceArea::query()
            ->whereIn('listing_id', $serviceAreaListingIds)
            ->where('city', $city)
            ->pluck('district');

        return $locationDistricts
            ->merge($serviceAreaDistricts)
            ->filter(fn($v) => !empty($v))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}

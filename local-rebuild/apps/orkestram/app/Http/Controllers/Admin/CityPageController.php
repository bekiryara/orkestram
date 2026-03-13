<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CityPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityPageController extends Controller
{
    public function index(Request $request): View
    {
        $site = (string) $request->query('site', 'orkestram.net');
        $rows = CityPage::query()
            ->where('site', $site)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.city-pages.index', compact('rows', 'site'));
    }

    public function create(Request $request): View
    {
        $site = (string) $request->query('site', 'orkestram.net');
        $item = new CityPage(['site' => $site, 'status' => 'draft']);
        return view('admin.city-pages.form', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        CityPage::create($data);

        return redirect()
            ->route('admin.city-pages.index', ['site' => $data['site']])
            ->with('ok', 'Sehir sayfasi olusturuldu.');
    }

    public function edit(CityPage $city_page): View
    {
        $item = $city_page;
        return view('admin.city-pages.form', compact('item'));
    }

    public function update(Request $request, CityPage $city_page): RedirectResponse
    {
        $data = $this->validated($request, $city_page->id);
        $city_page->update($data);

        return redirect()
            ->route('admin.city-pages.index', ['site' => $data['site']])
            ->with('ok', 'Sehir sayfasi guncellendi.');
    }

    public function destroy(CityPage $city_page): RedirectResponse
    {
        $site = $city_page->site;
        $city_page->delete();

        return redirect()
            ->route('admin.city-pages.index', ['site' => $site])
            ->with('ok', 'Sehir sayfasi silindi.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'site' => ['required', 'string', 'max:64'],
            'slug' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'district' => ['nullable', 'string', 'max:120'],
            'service_slug' => ['nullable', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'content' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'published_at' => ['nullable', 'date'],
        ]);

        $exists = CityPage::query()
            ->where('site', $data['site'])
            ->where('slug', $data['slug']);

        if ($id !== null) {
            $exists->whereKeyNot($id);
        }

        if ($exists->exists()) {
            abort(422, 'Bu site ve slug kombinasyonu zaten var.');
        }

        return $data;
    }
}

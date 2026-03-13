<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $site = (string) $request->query('site', 'orkestram.net');
        $pages = Page::query()
            ->where('site', $site)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.index', compact('pages', 'site'));
    }

    public function create(Request $request): View
    {
        $site = (string) $request->query('site', 'orkestram.net');
        $page = new Page(['site' => $site, 'status' => 'draft', 'template' => 'page']);
        return view('admin.pages.form', compact('page'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Page::create($data);

        return redirect()
            ->route('admin.pages.index', ['site' => $data['site']])
            ->with('ok', 'Sayfa olusturuldu.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request, $page->id);
        $page->update($data);

        return redirect()
            ->route('admin.pages.index', ['site' => $data['site']])
            ->with('ok', 'Sayfa guncellendi.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $site = $page->site;
        $page->delete();

        return redirect()
            ->route('admin.pages.index', ['site' => $site])
            ->with('ok', 'Sayfa silindi.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'site' => ['required', 'string', 'max:64'],
            'slug' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'template' => ['nullable', 'string', 'max:64'],
            'status' => ['required', 'in:draft,published,archived'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:512'],
            'published_at' => ['nullable', 'date'],
        ]);

        $exists = Page::query()
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

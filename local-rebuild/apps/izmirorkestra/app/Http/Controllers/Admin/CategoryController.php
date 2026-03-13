<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MainCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $mainCategoryId = (int) $request->query('main_category_id', 0);
        $search = trim((string) $request->query('q', ''));
        $allowedSites = ['orkestram.net', 'izmirorkestra.net'];
        $site = (string) $request->query('site', 'orkestram.net');
        if (!in_array($site, $allowedSites, true)) {
            $site = 'orkestram.net';
        }
        $withListings = $request->boolean('with_listings');

        $rows = Category::query()
            ->with('mainCategory:id,name,slug')
            ->withCount([
                'listings as total_listings_count' => function ($q) use ($site) {
                    $q->where(function ($qq) use ($site) {
                        $qq->where('site', $site)->orWhere('site_scope', 'both');
                    });
                },
                'listings as published_listings_count' => function ($q) use ($site) {
                    $q->where('status', 'published')
                        ->where(function ($qq) use ($site) {
                            $qq->where('site', $site)->orWhere('site_scope', 'both');
                        });
                },
            ])
            ->when($withListings, function ($q) use ($site) {
                $q->whereHas('listings', function ($qq) use ($site) {
                    $qq->where(function ($sq) use ($site) {
                        $sq->where('site', $site)->orWhere('site_scope', 'both');
                    });
                });
            })
            ->when($mainCategoryId > 0, fn($q) => $q->where('main_category_id', $mainCategoryId))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('main_category_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        $mainCategories = MainCategory::query()
            ->where('is_active', true)
            ->withCount('categories')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.categories.index', compact('rows', 'mainCategories', 'mainCategoryId', 'search', 'site', 'withListings'));
    }

    public function create(): View
    {
        $item = new Category(['is_active' => true, 'is_indexable' => true, 'sort_order' => 100]);
        $mainCategories = MainCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.categories.form', compact('item', 'mainCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data = $this->applyCoverImage($request, $data);
        Category::query()->create($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Kategori olusturuldu.');
    }

    public function edit(Category $category): View
    {
        $item = $category;
        $mainCategories = MainCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.categories.form', compact('item', 'mainCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category->id);
        $data = $this->applyCoverImage($request, $data, $category);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Kategori guncellendi.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Hard delete yerine pasife cekme kullaniliyor.
        $category->update(['is_active' => false, 'is_indexable' => false]);

        return redirect()->route('admin.categories.index')->with('ok', 'Kategori pasife cekildi.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer'],
            'ids_csv' => ['nullable', 'string'],
            'bulk_action' => ['required', 'in:activate,passive,indexable,noindex'],
        ]);

        $ids = [];
        if (!empty($data['ids']) && is_array($data['ids'])) {
            $ids = array_merge($ids, array_map('intval', $data['ids']));
        }
        if (!empty($data['ids_csv'])) {
            $parts = array_filter(array_map('trim', explode(',', (string) $data['ids_csv'])));
            $ids = array_merge($ids, array_map('intval', $parts));
        }
        $ids = array_values(array_unique(array_filter($ids, fn($id) => $id > 0)));

        if (empty($ids)) {
            return redirect()->route('admin.categories.index', $request->only(['site', 'main_category_id', 'q', 'with_listings']))
                ->with('ok', 'Toplu islem icin en az bir kategori secin.');
        }

        $validIds = Category::query()->whereIn('id', $ids)->pluck('id')->all();
        $ids = array_values(array_unique(array_map('intval', $validIds)));
        if (empty($ids)) {
            return redirect()->route('admin.categories.index', $request->only(['site', 'main_category_id', 'q', 'with_listings']))
                ->with('ok', 'Gecerli kategori secimi bulunamadi.');
        }

        $query = Category::query()->whereIn('id', $ids);

        switch ($data['bulk_action']) {
            case 'activate':
                $query->update(['is_active' => true]);
                break;
            case 'passive':
                $query->update(['is_active' => false, 'is_indexable' => false]);
                break;
            case 'indexable':
                $query->update(['is_indexable' => true]);
                break;
            case 'noindex':
                $query->update(['is_indexable' => false]);
                break;
        }

        return redirect()->route('admin.categories.index', $request->only(['site', 'main_category_id', 'q', 'with_listings']))
            ->with('ok', count($ids) . ' kategori icin toplu islem uygulandi.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'main_category_id' => ['required', 'integer', Rule::exists('main_categories', 'id')],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160', Rule::unique('categories', 'slug')->ignore($id)],
            'short_description' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string'],
            'cover_image_path' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'remove_cover_image' => ['nullable', 'in:0,1'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'is_active' => ['nullable', 'in:0,1'],
            'is_indexable' => ['nullable', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $data['is_active'] = ($request->input('is_active') === '1');
        $data['is_indexable'] = ($request->input('is_indexable') === '1');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 100);

        return $data;
    }

    private function applyCoverImage(Request $request, array $data, ?Category $category = null): array
    {
        $coverPath = $category?->cover_image_path;
        if (!empty($data['cover_image_path'])) {
            $coverPath = $data['cover_image_path'];
        }

        if ($request->input('remove_cover_image') === '1') {
            $coverPath = null;
        }

        if ($request->hasFile('cover_image') && $request->file('cover_image') instanceof UploadedFile) {
            $coverPath = $this->moveUpload($request->file('cover_image'), (string) ($data['slug'] ?? 'category'));
        }

        $data['cover_image_path'] = $coverPath;
        unset($data['cover_image'], $data['remove_cover_image']);

        return $data;
    }

    private function moveUpload(UploadedFile $file, string $slug): string
    {
        $safeSlug = Str::slug($slug ?: 'category');
        $dir = public_path('uploads/categories/' . $safeSlug);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw ValidationException::withMessages([
                'cover_image' => 'Kategori gorsel klasoru olusturulamadi. Dizin izinlerini kontrol edin.',
            ]);
        }

        $filename = now()->format('YmdHis') . '-' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $filename);

        return 'uploads/categories/' . $safeSlug . '/' . $filename;
    }
}

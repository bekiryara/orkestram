<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryAttributeController extends Controller
{
    public function index(Category $category): View
    {
        $rows = $category->attributes()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(50)
            ->withQueryString();

        return view('admin.category-attributes.index', compact('category', 'rows'));
    }

    public function create(Category $category): View
    {
        $item = new CategoryAttribute([
            'field_type' => 'text',
            'filter_mode' => 'exact',
            'is_required' => false,
            'is_filterable' => false,
            'is_visible_in_card' => false,
            'is_visible_in_detail' => true,
            'is_active' => true,
            'sort_order' => 100,
        ]);

        return view('admin.category-attributes.form', compact('category', 'item'));
    }

    public function store(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);
        $category->attributes()->create($data);

        return redirect()->route('admin.category-attributes.index', $category)->with('ok', 'Kategori ozelligi olusturuldu.');
    }

    public function edit(Category $category, CategoryAttribute $attribute): View
    {
        $this->assertBelongsToCategory($category, $attribute);
        $item = $attribute;

        return view('admin.category-attributes.form', compact('category', 'item'));
    }

    public function update(Request $request, Category $category, CategoryAttribute $attribute): RedirectResponse
    {
        $this->assertBelongsToCategory($category, $attribute);
        $data = $this->validated($request, $category, (int) $attribute->id);
        $attribute->update($data);

        return redirect()->route('admin.category-attributes.index', $category)->with('ok', 'Kategori ozelligi guncellendi.');
    }

    public function destroy(Category $category, CategoryAttribute $attribute): RedirectResponse
    {
        $this->assertBelongsToCategory($category, $attribute);
        $attribute->delete();

        return redirect()->route('admin.category-attributes.index', $category)->with('ok', 'Kategori ozelligi silindi.');
    }

    private function validated(Request $request, Category $category, ?int $attributeId = null): array
    {
        $data = $request->validate([
            'key' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('category_attributes', 'key')
                    ->where(fn($q) => $q->where('category_id', (int) $category->id))
                    ->ignore($attributeId),
            ],
            'label' => ['required', 'string', 'max:120'],
            'field_type' => ['required', Rule::in(['text', 'number', 'select', 'multiselect', 'boolean'])],
            'filter_mode' => ['required', Rule::in(['exact', 'range'])],
            'options_text' => ['nullable', 'string', 'max:4000'],
            'is_required' => ['nullable', 'in:0,1'],
            'is_filterable' => ['nullable', 'in:0,1'],
            'is_visible_in_card' => ['nullable', 'in:0,1'],
            'is_visible_in_detail' => ['nullable', 'in:0,1'],
            'is_active' => ['nullable', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $data['options_json'] = $this->optionsFromText((string) $request->input('options_text', ''));
        if (($data['field_type'] ?? 'text') !== 'number') {
            $data['filter_mode'] = 'exact';
        }
        $data['is_required'] = ($request->input('is_required') === '1');
        $data['is_filterable'] = ($request->input('is_filterable') === '1');
        $data['is_visible_in_card'] = ($request->input('is_visible_in_card') === '1');
        $data['is_visible_in_detail'] = ($request->input('is_visible_in_detail', '1') === '1');
        $data['is_active'] = ($request->input('is_active', '1') === '1');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 100);

        unset($data['options_text']);

        return $data;
    }

    /**
     * @return array<int, string>|null
     */
    private function optionsFromText(string $text): ?array
    {
        $rows = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $values = [];
        foreach ($rows as $row) {
            $item = trim($row);
            if ($item !== '') {
                $values[] = $item;
            }
        }

        $values = array_values(array_unique($values));

        return $values === [] ? null : $values;
    }

    private function assertBelongsToCategory(Category $category, CategoryAttribute $attribute): void
    {
        abort_if((int) $attribute->category_id !== (int) $category->id, 404);
    }
}

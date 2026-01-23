<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q ?? $request->search;   // support q atau search
        $type   = $request->type;

        $serviceOptions = [
            'konsultasi' => 'Konsultasi',
            'pengaduan'  => 'Pengaduan',
            'permohonan' => 'Permohonan Informasi',
        ];

        $categories = Category::query()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($x) use ($search) {
                    $x->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('type', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.kategori', compact('categories', 'serviceOptions', 'type', 'search'));
    }

    public function store(CategoryStoreRequest $request)
    {
        Category::create([
            'type' => $request->type, // <- masuk sesuai layanan dipilih
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.categories.kategori', ['type' => $request->type])
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'serviceOptions' => [
                'konsultasi' => 'Konsultasi',
                'pengaduan' => 'Pengaduan',
                'permohonan' => 'Permohonan Informasi',
            ],
        ]);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update([
            'type' => $request->type,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()
            ->route('admin.categories.kategori', ['type' => $request->type])
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $type = $category->type;
        $category->delete();

        return redirect()
            ->route('admin.categories.kategori', ['type' => $type])
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

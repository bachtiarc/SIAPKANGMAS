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
        // support q atau search
        $q = $request->q ?? $request->search;

        // default tab
        $type = $request->type ?? 'konsultasi';

        // default aktor
        $userType = $request->user_type ?? 'masyarakat_umum';

        $serviceOptions = [
            'konsultasi' => 'Konsultasi',
            'pengaduan'  => 'Pengaduan',
            'permohonan' => 'Permohonan Informasi',
        ];

        $actorOptions = [
            'pegawai' => 'Pegawai',
            'masyarakat_umum' => 'Masyarakat Umum',
        ];

        $categories = Category::query()
            ->when($type, fn ($x) => $x->where('type', $type))
            ->when($userType, fn ($x) => $x->where('user_type', $userType))
            ->when($q, function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('description', 'LIKE', "%{$q}%")
                      ->orWhere('type', 'LIKE', "%{$q}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.kategori', compact(
            'categories',
            'serviceOptions',
            'actorOptions',
            'type',
            'userType',
            'q'
        ));
    }

    public function store(CategoryStoreRequest $request)
    {
        Category::create([
            'type'        => $request->type,
            'user_type'   => $request->user_type,
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()
            ->route('admin.categories.kategori', [
                'type' => $request->type,
                'user_type' => $request->user_type,
            ])
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
            'actorOptions' => [
                'pegawai' => 'Pegawai',
                'masyarakat_umum' => 'Masyarakat Umum',
            ],
        ]);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update([
            'type'        => $request->type,
            'user_type'   => $request->user_type,
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => (bool) $request->is_active,
        ]);

        return redirect()
            ->route('admin.categories.kategori', [
                'type' => $request->type,
                'user_type' => $request->user_type,
            ])
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $type = $category->type;
        $userType = $category->user_type;

        $category->delete();

        return redirect()
            ->route('admin.categories.kategori', [
                'type' => $type,
                'user_type' => $userType,
            ])
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id ?? null;

        return [
            'user_type' => ['required', Rule::in(['pegawai', 'masyarakat_umum'])],

            'type' => ['required', Rule::in(['konsultasi', 'pengaduan', 'permohonan'])],

            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('categories')
                    ->ignore($categoryId)
                    ->where(fn ($q) => $q
                        ->where('type', $this->type)
                        ->where('user_type', $this->user_type)
                    ),
            ],

            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_type.required' => 'Aktor wajib dipilih.',
            'type.required' => 'Nama layanan wajib dipilih.',
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan layanan dan aktor yang sama sudah ada.',
            'is_active.required' => 'Status aktif wajib dipilih.',
        ];
    }
}
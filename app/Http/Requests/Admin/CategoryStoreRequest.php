<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['konsultasi', 'pengaduan', 'permohonan'])],
            'name' => [
                'required', 'string', 'max:120',
                // unik per type
                Rule::unique('categories')->where(fn ($q) => $q->where('type', $this->type)),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Nama layanan wajib dipilih.',
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan layanan yang sama sudah ada.',
        ];
    }
}

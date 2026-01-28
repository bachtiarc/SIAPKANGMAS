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
            'user_type' => ['required', Rule::in(['pegawai', 'masyarakat_umum'])],

            'type' => ['required', Rule::in(['konsultasi', 'pengaduan', 'permohonan'])],

            'name' => [
                'required', 'string', 'max:120',
                // unik per type + user_type
                Rule::unique('categories')->where(fn ($q) => $q
                    ->where('type', $this->type)
                    ->where('user_type', $this->user_type)
                ),
            ],

            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_type.required' => 'Aktor wajib dipilih.',
            'type.required' => 'Nama layanan wajib dipilih.',
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan layanan dan aktor yang sama sudah ada.',
        ];
    }
}
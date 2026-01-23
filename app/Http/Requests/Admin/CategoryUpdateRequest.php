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
            'type' => ['required', Rule::in(['konsultasi', 'pengaduan', 'permohonan'])],
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('categories')->ignore($categoryId)->where(fn ($q) => $q->where('type', $this->type)),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\EventCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var EventCategory|null $category */
        $category = $this->route('eventCategory');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('event_categories', 'name')->ignore($category?->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori ini sudah ada.',
        ];
    }
}

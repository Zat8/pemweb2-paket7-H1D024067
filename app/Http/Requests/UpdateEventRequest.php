<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'event_category_id' => 'required|exists:event_categories,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'speaker' => 'nullable|string|max:150',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1|max:1000',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:draft,published,closed,finished',
        ];
    }
}
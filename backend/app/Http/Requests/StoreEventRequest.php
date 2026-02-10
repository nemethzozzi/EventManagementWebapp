<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    /**
     * Szabályok
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'occurs_at' => 'required|date',
            'description' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:80'],
            'summary' => ['nullable', 'string'],
            'highlights' => ['nullable', 'array'],
            'highlights.*' => ['string', 'max:280'],
            'started_on' => ['nullable', 'date'],
            'ended_on' => ['nullable', 'date'],
            'is_current' => ['sometimes', 'boolean'],
            'company_url' => ['nullable', 'url'],
            'company_logo' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:100000'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}

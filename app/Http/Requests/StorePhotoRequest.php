<?php

namespace App\Http\Requests;

use App\Enums\PhotoCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Every logged-in user may upload; the route already requires a login
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', Rule::enum(PhotoCategory::class)],
            // max is in kilobytes: 2048 KB = 2 MB, PHP's default upload limit
            'image' => ['required', 'image', 'max:2048'],
        ];
    }
}

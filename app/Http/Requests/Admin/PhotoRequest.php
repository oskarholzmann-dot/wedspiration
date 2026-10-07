<?php

namespace App\Http\Requests\Admin;

use App\Enums\PhotoCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The admin middleware on the route group already checks this
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        return [
            // The owner is only chosen when creating a photo
            'user_id' => $isCreating ? ['required', 'exists:users,id'] : ['prohibited'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', Rule::enum(PhotoCategory::class)],
            'image' => [$isCreating ? 'required' : 'nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * Get custom names for the fields in error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['user_id' => 'owner'];
    }
}

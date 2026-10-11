<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class StoreActivityRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('submit_action')) {
            $this->merge(['status' => $this->input('submit_action')]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->is_active === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(PublicationStatus::class)],
            'published_at' => ['prohibited'],
            'category' => ['prohibited'],
            'images' => ['array', 'max:10'],
            'images.*.file' => [
                'required',
                File::types(['jpg', 'jpeg', 'png', 'webp'])->max(5 * 1024),
            ],
            'existing_images' => ['array'],
            'existing_images.*.position' => ['required', 'integer', 'min:0', 'max:9'],
            'existing_images.*.remove' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $activity = $this->route('activity');
                $submitted = collect($this->input('existing_images', []));
                $retained = $activity
                    ? $activity->images()->whereNotIn('id', $submitted->filter(fn ($image) => (bool) ($image['remove'] ?? false))->keys())->count()
                    : 0;
                $uploads = count($this->file('images', []));

                if ($retained + $uploads > 10) {
                    $validator->errors()->add('images', 'An activity can contain no more than 10 images.');
                }
            },
        ];
    }
}

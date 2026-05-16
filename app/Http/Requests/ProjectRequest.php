<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['planning', 'active', 'completed', 'on_hold'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
        ];

        if ($this->isMethod('POST')) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du projet est obligatoire.',
            'title.min' => 'Le titre doit contenir au moins 3 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
            'end_date.required' => 'La date de fin est obligatoire.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'status.in' => 'Le statut sélectionné est invalide.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'Format accepté: jpeg, png, jpg, gif.',
            'image.max' => 'L\'image ne doit pas dépasser 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim($this->title),
            'description' => trim($this->description),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->start_date && strtotime($this->start_date) > strtotime('+1 year')) {
                $validator->errors()->add('start_date', 'La date de début ne peut pas dépasser 1 an.');
            }
        });
    }
}
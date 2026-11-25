<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'     => ['nullable', 'string', 'max:30'],
            'address'   => ['nullable', 'string', 'max:255'],
            'shop_name' => ['nullable', 'string', 'max:120'],
            'avatar'    => ['nullable', 'image', 'max:2048'],
            'shop_logo' => ['nullable', 'image', 'max:2048'],
            'shop_description' => ['nullable', 'string', 'max:1000'],
            'shop_banner' => ['nullable', 'image', 'max:3072'],
            'use_hours' => ['nullable'],
            'hours' => ['nullable', 'array'],
            'hours.*.open' => ['nullable', 'date_format:H:i'],
            'hours.*.close' => ['nullable', 'date_format:H:i'],
            'vacation_mode' => ['nullable'],
            'vacation_until' => ['nullable', 'date', 'after_or_equal:today'],
            'vacation_message' => ['nullable', 'string', 'max:255'],
            'address_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'address_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}

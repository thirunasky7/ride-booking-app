<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'support_phone' => ['nullable', 'string', 'max:20'],
            'support_email' => ['nullable', 'email', 'max:120'],
            'slot_gap_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'booking_open_time' => ['nullable', 'date_format:H:i'],
            'booking_close_time' => ['nullable', 'date_format:H:i'],
            'commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'custom_route_price' => ['required', 'numeric', 'min:0'],
            'razorpay_enabled' => ['nullable', 'boolean'],
            'razorpay_key_id' => ['nullable', 'string', 'max:120'],
            'razorpay_key_secret' => ['nullable', 'string', 'max:255'],
            'razorpay_webhook_secret' => ['nullable', 'string', 'max:255'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_location' => ['required', 'string'],
            'drop_location' => ['required', 'string'],
            'pickup_address' => ['nullable', 'string', 'max:500'],
            'drop_address' => ['nullable', 'string', 'max:500'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'slot_time' => ['required_without:time_slot_id', 'nullable'],
            'time_slot_id' => ['nullable', 'exists:time_slots,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_location.required' => 'Please select a pickup location.',
            'drop_location.required' => 'Please select a drop location.',
        ];
    }
}

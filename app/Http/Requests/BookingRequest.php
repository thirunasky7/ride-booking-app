<?php

namespace App\Http\Requests;

use App\Services\BookingService;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->input('apartment_id') === 'other') {
            $data['apartment_id'] = null;
        }

        if ($this->input('bus_stand_id') === 'other') {
            $data['bus_stand_id'] = null;
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $rules = [
            'trip_type' => ['required', 'in:apartment_to_busstand,busstand_to_apartment,others'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'slot_time' => ['required_without:time_slot_id', 'nullable'],
            'time_slot_id' => ['nullable', 'exists:time_slots,id'],
            'pickup_address' => ['nullable', 'string', 'max:500'],
            'drop_address' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->input('trip_type') === BookingService::TRIP_OTHERS) {
            $rules['pickup_address'] = ['required', 'string', 'max:500'];
            $rules['drop_address'] = ['required', 'string', 'max:500'];
            $rules['apartment_id'] = ['nullable'];
            $rules['bus_stand_id'] = ['nullable'];
        } else {
            $rules['apartment_id'] = ['nullable', 'exists:apartments,id'];
            $rules['bus_stand_id'] = ['nullable', 'exists:bus_stands,id'];

            if (!$this->input('apartment_id')) {
                $rules['pickup_address'] = ['required', 'string', 'max:500'];
            }

            if (!$this->input('bus_stand_id')) {
                $rules['drop_address'] = ['required', 'string', 'max:500'];
            }

            if ($this->input('apartment_id') && $this->input('bus_stand_id')) {
                $rules['pickup_address'] = ['nullable', 'string', 'max:500'];
                $rules['drop_address'] = ['nullable', 'string', 'max:500'];
            }
        }

        return $rules;
    }
}

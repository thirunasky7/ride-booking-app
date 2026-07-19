<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverEarning;
use RuntimeException;

class DriverService
{
    public function getDriverBooking(Driver $driver, int $bookingId): Booking
    {
        $booking = Booking::with(['customer', 'apartment', 'busStand', 'vehicle'])
            ->whereHas('vehicle', fn ($q) => $q->where('driver_id', $driver->id))
            ->find($bookingId);

        if (!$booking) {
            throw new RuntimeException('Booking not found or not assigned to you.');
        }

        return $booking;
    }

    public function startTrip(Driver $driver, int $bookingId): Booking
    {
        $booking = $this->getDriverBooking($driver, $bookingId);

        if (!in_array($booking->status, ['confirmed', 'pending'], true)) {
            throw new RuntimeException('Trip cannot be started in its current status.');
        }

        $booking->update(['status' => 'started']);

        return $booking;
    }

    public function completeTrip(Driver $driver, int $bookingId): Booking
    {
        $booking = $this->getDriverBooking($driver, $bookingId);

        if ($booking->status !== 'started') {
            throw new RuntimeException('Trip must be started before completion.');
        }

        $booking->update(['status' => 'completed']);

        DriverEarning::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'driver_id' => $driver->id,
                'commission_amount' => $booking->commission_amount ?? 0,
                'driver_amount' => $booking->driver_amount ?? $booking->price,
            ]
        );

        return $booking;
    }
}

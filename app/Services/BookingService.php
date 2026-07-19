<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingService
{
    public const ACTIVE_STATUSES = ['pending', 'confirmed', 'started'];
    public const TYPE_INSTANT = 'instant';
    public const TYPE_SCHEDULED = 'scheduled';
    public const TRIP_OTHERS = 'others';

    public function __construct(
        protected PricingService $pricingService,
        protected SubscriptionService $subscriptionService,
        protected LocationService $locationService,
    ) {}

    public function create(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $data = $this->resolveLocations($data);
            $bookingType = $this->resolveBookingType($data['booking_date']);

            $this->subscriptionService->assertCanBook($user);

            $vehicle = $this->findAvailableVehicle($data['booking_date'], $data['slot_time']);
            if (!$vehicle) {
                throw new RuntimeException('No vehicles available for the selected slot.');
            }

            $isCustomRoute = ($data['trip_type'] ?? '') === self::TRIP_OTHERS
                || empty($data['apartment_id'])
                || empty($data['bus_stand_id']);

            $pricing = $isCustomRoute
                ? $this->pricingService->calculateCustom($data['slot_time'], $data['booking_date'])
                : $this->pricingService->calculate(
                    (int) $data['apartment_id'],
                    (int) $data['bus_stand_id'],
                    $data['slot_time'],
                    $data['booking_date']
                );

            $booking = Booking::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'apartment_id' => $data['apartment_id'],
                'bus_stand_id' => $data['bus_stand_id'],
                'time_slot_id' => $data['time_slot_id'] ?? null,
                'pickup_address' => $data['pickup_address'] ?? null,
                'pickup_lat' => $data['pickup_lat'] ?? null,
                'pickup_lng' => $data['pickup_lng'] ?? null,
                'drop_address' => $data['drop_address'] ?? null,
                'drop_lat' => $data['drop_lat'] ?? null,
                'drop_lng' => $data['drop_lng'] ?? null,
                'booking_date' => $data['booking_date'],
                'slot_time' => $data['slot_time'],
                'trip_type' => $data['trip_type'],
                'booking_type' => $bookingType,
                'status' => $bookingType === self::TYPE_INSTANT ? 'confirmed' : 'pending',
                'payment_status' => 'unpaid',
                'price' => $pricing['price'],
                'commission_amount' => $pricing['commission_amount'],
                'driver_amount' => $pricing['driver_amount'],
            ]);

            $this->subscriptionService->deductRide($user);

            return $booking->load(['vehicle', 'apartment', 'busStand']);
        });
    }

    public function resolveBookingType(string $bookingDate): string
    {
        return Carbon::parse($bookingDate)->isToday()
            ? self::TYPE_INSTANT
            : self::TYPE_SCHEDULED;
    }

    public function resolveLocations(array $data): array
    {
        if (($data['apartment_id'] ?? '') === 'other') {
            $data['apartment_id'] = null;
        }

        if (($data['bus_stand_id'] ?? '') === 'other') {
            $data['bus_stand_id'] = null;
        }

        $isCustomRoute = ($data['trip_type'] ?? '') === self::TRIP_OTHERS
            || empty($data['apartment_id'])
            || empty($data['bus_stand_id']);

        if (($data['trip_type'] ?? '') === self::TRIP_OTHERS) {
            if (empty($data['pickup_address']) || empty($data['drop_address'])) {
                throw new RuntimeException('Please enter both pickup and drop addresses.');
            }

            $data['apartment_id'] = null;
            $data['bus_stand_id'] = null;

            return $this->resolveSlotTime($data);
        }

        if (empty($data['apartment_id']) && empty($data['pickup_address'])) {
            throw new RuntimeException('Please select an apartment or enter a pickup address.');
        }

        if (empty($data['bus_stand_id']) && empty($data['drop_address'])) {
            throw new RuntimeException('Please select a bus stand or enter a drop address.');
        }

        if ($isCustomRoute) {
            return $this->resolveSlotTime($data);
        }

        if (!empty($data['pickup_lat']) && !empty($data['pickup_lng'])) {
            $apartment = $this->locationService->findNearestApartment(
                (float) $data['pickup_lat'],
                (float) $data['pickup_lng']
            );
            if ($apartment) {
                $data['apartment_id'] = $apartment->id;
            }
        }

        if (!empty($data['drop_lat']) && !empty($data['drop_lng'])) {
            $busStand = $this->locationService->findNearestBusStand(
                (float) $data['drop_lat'],
                (float) $data['drop_lng']
            );
            if ($busStand) {
                $data['bus_stand_id'] = $busStand->id;
            }
        }

        if (empty($data['apartment_id']) || empty($data['bus_stand_id'])) {
            throw new RuntimeException('Please select both apartment and bus stand.');
        }

        return $this->resolveSlotTime($data);
    }

    protected function resolveSlotTime(array $data): array
    {
        if (!empty($data['time_slot_id']) && empty($data['slot_time'])) {
            $slot = TimeSlot::find($data['time_slot_id']);
            if ($slot) {
                $data['slot_time'] = $slot->slot_time;
            }
        }

        return $data;
    }

    public function cancel(Booking $booking, User $user): Booking
    {
        if ($booking->user_id !== $user->id) {
            throw new RuntimeException('Unauthorized booking access.');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'], true)) {
            throw new RuntimeException('This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return $booking;
    }

    public function updatePaymentStatus(Booking $booking, User $user, string $paymentStatus, ?string $paymentMethod = null): Booking
    {
        if ($booking->user_id !== $user->id) {
            throw new RuntimeException('Unauthorized booking access.');
        }

        if ($booking->status === 'cancelled') {
            throw new RuntimeException('Cannot update payment for a cancelled booking.');
        }

        if (!in_array($paymentStatus, ['unpaid', 'paid'], true)) {
            throw new RuntimeException('Invalid payment status.');
        }

        if ($paymentStatus === 'paid') {
            if (!in_array($paymentMethod, ['cash', 'upi'], true)) {
                throw new RuntimeException('Select a payment method: cash or upi.');
            }

            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
            ]);
        } else {
            $booking->update([
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'paid_at' => null,
            ]);
        }

        return $booking->fresh(['vehicle', 'apartment', 'busStand']);
    }

    public function modify(Booking $booking, User $user, array $data): Booking
    {
        if ($booking->user_id !== $user->id) {
            throw new RuntimeException('Unauthorized booking access.');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'], true)) {
            throw new RuntimeException('This booking cannot be modified.');
        }

        $data = $this->resolveLocations($data);

        $vehicle = $this->findAvailableVehicle(
            $data['booking_date'],
            $data['slot_time'],
            $booking->id
        );

        if (!$vehicle) {
            throw new RuntimeException('No vehicles available for the new slot.');
        }

        $isCustomRoute = ($data['trip_type'] ?? '') === self::TRIP_OTHERS
            || empty($data['apartment_id'])
            || empty($data['bus_stand_id']);

        $pricing = $isCustomRoute
            ? $this->pricingService->calculateCustom($data['slot_time'], $data['booking_date'])
            : $this->pricingService->calculate(
                (int) $data['apartment_id'],
                (int) $data['bus_stand_id'],
                $data['slot_time'],
                $data['booking_date']
            );

        $booking->update([
            'vehicle_id' => $vehicle->id,
            'apartment_id' => $data['apartment_id'],
            'bus_stand_id' => $data['bus_stand_id'],
            'time_slot_id' => $data['time_slot_id'] ?? null,
            'pickup_address' => $data['pickup_address'] ?? $booking->pickup_address,
            'pickup_lat' => $data['pickup_lat'] ?? $booking->pickup_lat,
            'pickup_lng' => $data['pickup_lng'] ?? $booking->pickup_lng,
            'drop_address' => $data['drop_address'] ?? $booking->drop_address,
            'drop_lat' => $data['drop_lat'] ?? $booking->drop_lat,
            'drop_lng' => $data['drop_lng'] ?? $booking->drop_lng,
            'booking_date' => $data['booking_date'],
            'slot_time' => $data['slot_time'],
            'trip_type' => $data['trip_type'],
            'booking_type' => $this->resolveBookingType($data['booking_date']),
            'price' => $pricing['price'],
            'commission_amount' => $pricing['commission_amount'],
            'driver_amount' => $pricing['driver_amount'],
        ]);

        return $booking->fresh(['vehicle', 'apartment', 'busStand']);
    }

    public function findAvailableVehicle(string $date, string $slotTime, ?int $excludeBookingId = null): ?Vehicle
    {
        return Vehicle::where('status', 1)
            ->orderBy('id')
            ->get()
            ->first(function (Vehicle $vehicle) use ($date, $slotTime, $excludeBookingId) {
                $query = Booking::where('vehicle_id', $vehicle->id)
                    ->where('booking_date', $date)
                    ->where('slot_time', $slotTime)
                    ->whereIn('status', self::ACTIVE_STATUSES);

                if ($excludeBookingId) {
                    $query->where('id', '!=', $excludeBookingId);
                }

                return !$query->exists();
            });
    }
}

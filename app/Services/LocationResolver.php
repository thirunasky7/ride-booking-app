<?php

namespace App\Services;

use App\Models\Apartment;
use App\Models\BusStand;
use RuntimeException;

class LocationResolver
{
    /**
     * Parse location value like "apartment:5", "busstand:3", or "other".
     *
     * @return array{type: string, id: int|null}
     */
    public function parse(string $value): array
    {
        if ($value === 'other') {
            return ['type' => 'other', 'id' => null];
        }

        if (str_contains($value, ':')) {
            [$type, $id] = explode(':', $value, 2);

            return [
                'type' => $type,
                'id' => (int) $id,
            ];
        }

        throw new RuntimeException('Invalid location selected.');
    }

    /**
     * Resolve pickup/drop dropdown values into booking fields.
     */
    public function resolveBookingLocations(array $data): array
    {
        $pickup = $this->parse($data['pickup_location']);
        $drop = $this->parse($data['drop_location']);

        $data['apartment_id'] = null;
        $data['bus_stand_id'] = null;
        $data['trip_type'] = 'others';

        if ($pickup['type'] === 'apartment' && $drop['type'] === 'busstand') {
            $data['apartment_id'] = $pickup['id'];
            $data['bus_stand_id'] = $drop['id'];
            $data['trip_type'] = 'apartment_to_busstand';
        } elseif ($pickup['type'] === 'busstand' && $drop['type'] === 'apartment') {
            $data['apartment_id'] = $drop['id'];
            $data['bus_stand_id'] = $pickup['id'];
            $data['trip_type'] = 'busstand_to_apartment';
        } elseif ($pickup['type'] === 'other' || $drop['type'] === 'other') {
            $data['trip_type'] = 'others';

            if ($pickup['type'] === 'other' && empty($data['pickup_address'])) {
                throw new RuntimeException('Please enter a pickup address.');
            }

            if ($drop['type'] === 'other' && empty($data['drop_address'])) {
                throw new RuntimeException('Please enter a drop address.');
            }

            if ($pickup['type'] !== 'other') {
                $data['pickup_address'] = $data['pickup_address'] ?? $this->locationLabel($pickup);
            }

            if ($drop['type'] !== 'other') {
                $data['drop_address'] = $data['drop_address'] ?? $this->locationLabel($drop);
            }
        } elseif ($pickup['type'] === $drop['type'] && $pickup['id'] === $drop['id']) {
            throw new RuntimeException('Pickup and drop cannot be the same location.');
        } else {
            $data['trip_type'] = 'others';
            $data['pickup_address'] = $data['pickup_address'] ?? $this->locationLabel($pickup);
            $data['drop_address'] = $data['drop_address'] ?? $this->locationLabel($drop);
        }

        if ($data['trip_type'] !== 'others') {
            if ($pickup['type'] === 'other') {
                if (empty($data['pickup_address'])) {
                    throw new RuntimeException('Please enter a pickup address.');
                }
            } else {
                $data['pickup_address'] = null;
            }

            if ($drop['type'] === 'other') {
                if (empty($data['drop_address'])) {
                    throw new RuntimeException('Please enter a drop address.');
                }
            } else {
                $data['drop_address'] = null;
            }
        }

        return $data;
    }

    public function labelForValue(string $value, array $apartments, array $busStands): string
    {
        if ($value === 'other') {
            return 'Custom address';
        }

        $parsed = $this->parse($value);

        if ($parsed['type'] === 'apartment') {
            $apt = collect($apartments)->firstWhere('id', $parsed['id']);

            return $apt?->name ?? 'Apartment';
        }

        if ($parsed['type'] === 'busstand') {
            $stand = collect($busStands)->firstWhere('id', $parsed['id']);

            return $stand?->name ?? 'Bus Stand';
        }

        return '—';
    }

    protected function locationLabel(array $parsed): string
    {
        if ($parsed['type'] === 'apartment') {
            return Apartment::find($parsed['id'])?->name ?? 'Apartment';
        }

        if ($parsed['type'] === 'busstand') {
            return BusStand::find($parsed['id'])?->name ?? 'Bus Stand';
        }

        return 'Custom location';
    }
}

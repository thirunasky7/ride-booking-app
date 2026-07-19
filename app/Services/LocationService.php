<?php

namespace App\Services;

use App\Models\Apartment;
use App\Models\BusStand;
use Illuminate\Database\Eloquent\Model;

class LocationService
{
    public function findNearestApartment(float $lat, float $lng): ?Apartment
    {
        return $this->findNearest(Apartment::where('status', 1)->get(), $lat, $lng);
    }

    public function findNearestBusStand(float $lat, float $lng): ?BusStand
    {
        return $this->findNearest(BusStand::where('status', 1)->get(), $lat, $lng);
    }

    protected function findNearest($collection, float $lat, float $lng): ?Model
    {
        return $collection
            ->filter(fn ($m) => $m->latitude && $m->longitude)
            ->sortBy(fn ($m) => $this->haversine($lat, $lng, (float) $m->latitude, (float) $m->longitude))
            ->first();
    }

    public function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

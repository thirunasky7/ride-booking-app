<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\BusStand;
use App\Traits\ApiResponse;

class CatalogApiController extends Controller
{
    use ApiResponse;

    public function apartments()
    {
        $apartments = Apartment::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'latitude', 'longitude']);

        return $this->success(['apartments' => $apartments]);
    }

    public function busStands()
    {
        $busStands = BusStand::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'latitude', 'longitude']);

        return $this->success(['bus_stands' => $busStands]);
    }
}

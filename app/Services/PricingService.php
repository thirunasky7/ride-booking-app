<?php

namespace App\Services;

use App\Models\RoutePrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class PricingService
{
    public function __construct(
        protected SettingsService $settingsService,
    ) {}
    public function calculate(
        int $apartmentId,
        int $busStandId,
        string $slotTime,
        string $bookingDate
    ): array {
        $routePrice = $this->getRoutePrice($apartmentId, $busStandId);

        if (!$routePrice) {
            throw new RuntimeException('Route price is not configured for this route.');
        }

        $price = (float) $routePrice->base_price;
        $date = Carbon::parse($bookingDate);

        if ($routePrice->holiday_price && $this->isHoliday($date)) {
            $price = (float) $routePrice->holiday_price;
        } elseif ($this->isPeakSlot($routePrice, $slotTime)) {
            $price = (float) ($routePrice->peak_price ?? $routePrice->base_price);
        }

        return $this->buildPricingBreakdown($price);
    }

    public function calculateCustom(string $slotTime, string $bookingDate): array
    {
        $settings = $this->getSettings();
        $price = (float) ($settings->custom_route_price ?? 150);

        return $this->buildPricingBreakdown($price);
    }

    protected function buildPricingBreakdown(float $price): array
    {
        $commissionPercent = (float) ($this->getSettings()->commission_percent ?? 10);
        $commissionAmount = round($price * $commissionPercent / 100, 2);
        $driverAmount = round($price - $commissionAmount, 2);

        return [
            'price' => $price,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'driver_amount' => $driverAmount,
        ];
    }

    public function getRoutePrice(int $apartmentId, int $busStandId): ?RoutePrice
    {
        return Cache::remember(
            "route_price:{$apartmentId}:{$busStandId}",
            now()->addHours(6),
            fn () => RoutePrice::where('apartment_id', $apartmentId)
                ->where('bus_stand_id', $busStandId)
                ->where('status', 1)
                ->first()
        );
    }

    public function clearRoutePriceCache(int $apartmentId, int $busStandId): void
    {
        Cache::forget("route_price:{$apartmentId}:{$busStandId}");
    }

    protected function isPeakSlot(RoutePrice $routePrice, string $slotTime): bool
    {
        if (!$routePrice->peak_from || !$routePrice->peak_to) {
            return false;
        }

        return $slotTime >= $routePrice->peak_from && $slotTime <= $routePrice->peak_to;
    }

    protected function isHoliday(Carbon $date): bool
    {
        return $date->isWeekend();
    }

    protected function getSettings()
    {
        return $this->settingsService->get();
    }
}

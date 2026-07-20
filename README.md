# Apartment Shuttle — SaaS Bike Taxi Platform

Production-ready Laravel 10 shuttle booking SaaS with admin panel, customer web portal, REST API, subscriptions, and pre-booking.

## Tech Stack

- **Backend:** PHP 8.1+, Laravel 10, Sanctum
- **Frontend:** Bootstrap 5 (npm), Bootstrap Icons, Vite, SCSS
- **Database:** MySQL
- **Auth:** Breeze (admin), OTP (customers), Sanctum tokens (API)

## Roles

| Role | Access |
|------|--------|
| **Super Admin** | `/admin/*` — full platform management |
| **Customer** | `/customer/*` — book rides, subscriptions, pre-booking |
| **Driver** | `/api/driver/*` — trips, earnings (mobile app) |

## Quick Start

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

### Default Credentials

| Portal | URL | Login |
|--------|-----|-------|
| Admin | `/login` → `/admin/dashboard` | `admin@gmail.com` / `12345678` |
| Customer | `/customer/login` | Mobile OTP (SMS integration required) |
| Driver API | `POST /api/driver/login` | `9876543210` / `driver123` |

## Architecture

```
app/
├── Services/
│   ├── BookingService.php      # Vehicle assignment, booking CRUD
│   ├── PricingService.php      # Base/peak/holiday + commission
│   ├── SubscriptionService.php # Plans, ride limits, expiry
│   ├── DriverService.php       # Trip ownership validation
│   └── OtpService.php          # Secure OTP with rate limits
├── Http/Requests/              # Form request validation
└── Traits/ApiResponse.php      # Standardized API format
```

## API Response Format

```json
{
  "status": true,
  "message": "Success message",
  "data": {}
}
```

## API Endpoints

### Auth
- `POST /api/send-otp` — Send OTP (rate limited)
- `POST /api/verify-otp` — Verify & get Sanctum token

### Booking
- `GET /api/available-slots?booking_date=YYYY-MM-DD`
- `POST /api/create-booking` (auth)
- `PUT /api/modify-booking/{id}` (auth)
- `POST /api/cancel-booking/{id}` (auth)
- `GET /api/booking-history` (auth)

### Pre-Booking
- `GET /api/pre-bookings` (auth)
- `POST /api/pre-bookings` (auth)
- `POST /api/pre-bookings/{id}/confirm` (auth)

### Subscription
- `GET /api/subscription-plans`
- `GET /api/my-subscription` (auth)
- `POST /api/purchase-subscription` (auth)

### Driver
- `POST /api/driver/login`
- `GET /api/driver/dashboard` (auth)
- `GET /api/driver/today-trips` (auth)
- `GET /api/driver/earnings` (auth)
- `POST /api/driver/start-trip/{id}` (auth)
- `POST /api/driver/complete-trip/{id}` (auth)

## Postman Collection

Import `postman/Apartment-Shuttle-API.postman_collection.json` into Postman.

Set variables:
- `base_url` = `http://localhost:8000/api`
- `customer_token` = token from verify-otp
- `driver_token` = token from driver login

## Scheduled Tasks

```bash
php artisan subscriptions:expire  # Also runs daily via scheduler
```

Add to crontab: `* * * * * php /path/to/artisan schedule:run`

## Testing

```bash
php artisan test
```

Tests cover booking flow, subscription logic, and driver authorization.

## Business Model

1. **Subscriptions** — Monthly plans (Starter / Commuter / Unlimited)
2. **Daily Booking** — Pay-per-ride with peak/holiday pricing
3. **Pre-Booking** — Schedule future rides in advance
4. **Commission** — Admin earns configurable % per completed ride

## Marketing Pages

- `/` — Home
- `/about` — About
- `/services` — Services
- `/pricing` — Subscription plans
- `/contact` — Contact
- `/driver-register` — Driver onboarding
- `/privacy-policy` — Privacy Policy (Play Console)
- `/terms` — Terms of Service
- `/account-deletion` — Account deletion instructions (Play Console Data safety)

## Security Features

- OTP never returned in API responses
- OTP expiry + retry limits + send rate limiting
- Random secure passwords for OTP-created users
- Driver can only act on assigned vehicle bookings
- Sanctum token authentication
- Middleware aliases for `admin` and `customer` roles

## Development

```bash
npm run dev     # Vite HMR
php artisan serve
```

## Mobile Apps (Android)

Two separate Flutter apps live under `mobile/`:

| App | Path |
|-----|------|
| Customer | `mobile/customer_app` |
| Driver | `mobile/driver_app` |

See [mobile/README.md](mobile/README.md) for setup, API URL config, and run commands.

### Customer payment API

```
POST /api/bookings/{id}/payment-status
Authorization: Bearer <token>

{ "payment_status": "paid", "payment_method": "upi" }  // or "cash"
```

## License

MIT

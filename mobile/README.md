# Mobile Apps (Android)

Two separate Flutter apps that talk to the Apartment Shuttle Laravel API.

| App | Path | Purpose |
|-----|------|---------|
| **Customer** | `mobile/customer_app` | OTP login, book rides, bookings, subscriptions |
| **Driver** | `mobile/driver_app` | Password login, dashboard, trips, earnings |

Target: **Android only**.

## Prerequisites

- Flutter 3.x (`flutter doctor`)
- Android Studio / emulator or a physical Android device
- Laravel backend running (`php artisan serve`)

## API base URL

Both apps use:

```dart
// lib/config/api_config.dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

| Environment | Base URL |
|-------------|----------|
| Android emulator | `http://10.0.2.2:8000/api` |
| Physical device | `http://YOUR_PC_LAN_IP:8000/api` |

`10.0.2.2` is the emulator alias for your PC’s `localhost`.

Cleartext HTTP is enabled for local development in both AndroidManifests.

## Run Laravel API

From the repo root:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

New catalog endpoints used by the customer app:

- `GET /api/apartments`
- `GET /api/bus-stands`

## Run Customer app

```bash
cd mobile/customer_app
flutter pub get
flutter run
```

**Login:** mobile OTP (`POST /api/send-otp` → `POST /api/verify-otp`)

## Run Driver app

```bash
cd mobile/driver_app
flutter pub get
flutter run
```

**Seeded driver (from README):**

- Mobile: `9876543210`
- Password: `driver123`

### Customer payment

Customers can mark a booking as paid with method **UPI** or **Cash**:

```
POST /api/bookings/{id}/payment-status
{ "payment_status": "paid", "payment_method": "upi" }
```

### Default OTP

OTP is fixed to **1234** by default (`OTP_FIXED_CODE=1234` in `.env`).  
To use random OTPs later, set `OTP_FIXED_CODE=` empty and integrate SMS.

### Play Store policy pages

Use these URLs in Play Console:

- Privacy: `https://book.zennexs.com/privacy-policy`
- Terms: `https://book.zennexs.com/terms`
- Account deletion: `https://book.zennexs.com/account-deletion`

In-app: Profile → Privacy / Terms / Delete account.

Run migration after pull:

```bash
php artisan migrate
```


### Driver
- Mobile/password login
- Online/offline toggle
- Today’s trip stats + earnings total
- Today’s trips: Start / Complete
- Earnings list

## Project layout

```
mobile/
├── customer_app/
│   └── lib/
│       ├── config/api_config.dart
│       ├── models/
│       ├── providers/
│       ├── screens/
│       ├── services/
│       └── theme/
└── driver_app/
    └── lib/
        ├── config/api_config.dart
        ├── models/
        ├── providers/
        ├── screens/
        ├── services/
        └── theme/
```

## Build release APK

```bash
cd mobile/customer_app
flutter build apk --release

cd ../driver_app
flutter build apk --release
```

APKs are written to `build/app/outputs/flutter-apk/app-release.apk` inside each app folder.

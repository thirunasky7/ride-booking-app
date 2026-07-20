# Azhai Driver (Android)

Flutter driver app for Apartment Shuttle. **Android only** — do not run on Chrome/web.

Package name in `pubspec.yaml`: `driver_app`.

## Run

```bash
cd mobile/driver_app
flutter pub get
flutter devices
flutter run -d <android-device-id>
```

Or, with a single Android emulator/device connected:

```bash
flutter run
```

### Seeded driver login

- Mobile: `9876543210`
- Password: `driver123`

See [../README.md](../README.md) for API base URL and signing notes.

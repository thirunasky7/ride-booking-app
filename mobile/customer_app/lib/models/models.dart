double? parseDouble(dynamic value) {
  if (value == null) return null;
  if (value is num) return value.toDouble();
  return double.tryParse(value.toString());
}

int? parseInt(dynamic value) {
  if (value == null) return null;
  if (value is int) return value;
  if (value is num) return value.toInt();
  return int.tryParse(value.toString());
}

String parseString(dynamic value) {
  if (value == null) return '';
  return value.toString();
}

class UserModel {
  UserModel({
    required this.id,
    required this.name,
    required this.mobile,
    this.email,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: parseInt(json['id']) ?? 0,
      name: parseString(json['name']),
      mobile: parseString(json['mobile']),
      email: json['email']?.toString(),
    );
  }

  final int id;
  final String name;
  final String mobile;
  final String? email;
}

class ApartmentModel {
  ApartmentModel({required this.id, required this.name, this.address});

  factory ApartmentModel.fromJson(Map<String, dynamic> json) {
    return ApartmentModel(
      id: parseInt(json['id']) ?? 0,
      name: parseString(json['name']),
      address: json['address']?.toString(),
    );
  }

  final int id;
  final String name;
  final String? address;
}

class BusStandModel {
  BusStandModel({required this.id, required this.name, this.address});

  factory BusStandModel.fromJson(Map<String, dynamic> json) {
    return BusStandModel(
      id: parseInt(json['id']) ?? 0,
      name: parseString(json['name']),
      address: json['address']?.toString(),
    );
  }

  final int id;
  final String name;
  final String? address;
}

class SlotModel {
  SlotModel({
    required this.timeSlotId,
    required this.slotTime,
    required this.isAvailable,
    required this.availableVehicles,
  });

  factory SlotModel.fromJson(Map<String, dynamic> json) {
    return SlotModel(
      timeSlotId: parseInt(json['time_slot_id']) ?? 0,
      slotTime: parseString(json['slot_time']),
      isAvailable: json['is_available'] == true,
      availableVehicles: parseInt(json['available_vehicles']) ?? 0,
    );
  }

  final int timeSlotId;
  final String slotTime;
  final bool isAvailable;
  final int availableVehicles;
}

class BookingModel {
  BookingModel({
    required this.id,
    required this.bookingDate,
    required this.slotTime,
    required this.tripType,
    required this.status,
    required this.bookingType,
    this.paymentStatus = 'unpaid',
    this.paymentMethod,
    this.paidAt,
    this.price,
    this.pickupAddress,
    this.dropAddress,
    this.apartmentName,
    this.busStandName,
  });

  factory BookingModel.fromJson(Map<String, dynamic> json) {
    final apartment = json['apartment'] as Map<String, dynamic>?;
    final busStand = json['bus_stand'] as Map<String, dynamic>?;
    return BookingModel(
      id: parseInt(json['id']) ?? 0,
      bookingDate: parseString(json['booking_date']),
      slotTime: parseString(json['slot_time']),
      tripType: parseString(json['trip_type']),
      status: parseString(json['status']),
      bookingType: parseString(json['booking_type']),
      paymentStatus: parseString(json['payment_status']).isEmpty
          ? 'unpaid'
          : parseString(json['payment_status']),
      paymentMethod: json['payment_method']?.toString(),
      paidAt: json['paid_at']?.toString(),
      price: parseDouble(json['price']),
      pickupAddress: json['pickup_address']?.toString(),
      dropAddress: json['drop_address']?.toString(),
      apartmentName: apartment?['name']?.toString(),
      busStandName: busStand?['name']?.toString(),
    );
  }

  final int id;
  final String bookingDate;
  final String slotTime;
  final String tripType;
  final String status;
  final String bookingType;
  final String paymentStatus;
  final String? paymentMethod;
  final String? paidAt;
  final double? price;
  final String? pickupAddress;
  final String? dropAddress;
  final String? apartmentName;
  final String? busStandName;

  bool get isPaid => paymentStatus == 'paid';

  String get pickupLabel =>
      (pickupAddress != null && pickupAddress!.isNotEmpty)
          ? pickupAddress!
          : (apartmentName ?? '—');

  String get dropLabel =>
      (dropAddress != null && dropAddress!.isNotEmpty)
          ? dropAddress!
          : (busStandName ?? '—');
}

class SubscriptionPlan {
  SubscriptionPlan({
    required this.id,
    required this.name,
    required this.price,
    required this.ridesLimit,
    this.durationDays,
  });

  factory SubscriptionPlan.fromJson(Map<String, dynamic> json) {
    return SubscriptionPlan(
      id: parseInt(json['id']) ?? 0,
      name: parseString(json['name']),
      price: parseDouble(json['price']) ?? 0,
      ridesLimit: parseInt(json['ride_limit']) ?? 0,
      durationDays: parseInt(json['validity_days']),
    );
  }

  final int id;
  final String name;
  final double price;
  final int ridesLimit;
  final int? durationDays;
}

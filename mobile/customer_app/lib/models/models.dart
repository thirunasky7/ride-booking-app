class UserModel {
  UserModel({
    required this.id,
    required this.name,
    required this.mobile,
    this.email,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] as int,
      name: (json['name'] as String?) ?? '',
      mobile: (json['mobile'] as String?) ?? '',
      email: json['email'] as String?,
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
      id: json['id'] as int,
      name: (json['name'] as String?) ?? '',
      address: json['address'] as String?,
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
      id: json['id'] as int,
      name: (json['name'] as String?) ?? '',
      address: json['address'] as String?,
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
      timeSlotId: json['time_slot_id'] as int,
      slotTime: (json['slot_time'] as String?) ?? '',
      isAvailable: json['is_available'] == true,
      availableVehicles: (json['available_vehicles'] as num?)?.toInt() ?? 0,
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
      id: json['id'] as int,
      bookingDate: (json['booking_date'] as String?) ?? '',
      slotTime: (json['slot_time'] as String?) ?? '',
      tripType: (json['trip_type'] as String?) ?? '',
      status: (json['status'] as String?) ?? '',
      bookingType: (json['booking_type'] as String?) ?? '',
      price: (json['price'] as num?)?.toDouble(),
      pickupAddress: json['pickup_address'] as String?,
      dropAddress: json['drop_address'] as String?,
      apartmentName: apartment?['name'] as String?,
      busStandName: busStand?['name'] as String?,
    );
  }

  final int id;
  final String bookingDate;
  final String slotTime;
  final String tripType;
  final String status;
  final String bookingType;
  final double? price;
  final String? pickupAddress;
  final String? dropAddress;
  final String? apartmentName;
  final String? busStandName;

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
      id: json['id'] as int,
      name: (json['name'] as String?) ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0,
      ridesLimit: (json['ride_limit'] as num?)?.toInt() ?? 0,
      durationDays: (json['validity_days'] as num?)?.toInt(),
    );
  }

  final int id;
  final String name;
  final double price;
  final int ridesLimit;
  final int? durationDays;
}

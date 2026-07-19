class DriverModel {
  DriverModel({
    required this.id,
    required this.name,
    required this.mobile,
    this.isOnline = false,
  });

  factory DriverModel.fromJson(Map<String, dynamic> json) {
    return DriverModel(
      id: json['id'] as int,
      name: (json['name'] as String?) ?? '',
      mobile: (json['mobile'] as String?) ?? '',
      isOnline: json['is_online'] == true || json['is_online'] == 1,
    );
  }

  final int id;
  final String name;
  final String mobile;
  final bool isOnline;

  DriverModel copyWith({bool? isOnline}) {
    return DriverModel(
      id: id,
      name: name,
      mobile: mobile,
      isOnline: isOnline ?? this.isOnline,
    );
  }
}

class TripModel {
  TripModel({
    required this.id,
    required this.bookingDate,
    required this.slotTime,
    required this.tripType,
    required this.status,
    this.price,
    this.pickupAddress,
    this.dropAddress,
    this.customerName,
    this.customerMobile,
    this.apartmentName,
    this.busStandName,
  });

  factory TripModel.fromJson(Map<String, dynamic> json) {
    final customer = json['customer'] as Map<String, dynamic>?;
    final apartment = json['apartment'] as Map<String, dynamic>?;
    final busStand = json['bus_stand'] as Map<String, dynamic>?;
    return TripModel(
      id: json['id'] as int,
      bookingDate: (json['booking_date'] as String?) ?? '',
      slotTime: (json['slot_time'] as String?) ?? '',
      tripType: (json['trip_type'] as String?) ?? '',
      status: (json['status'] as String?) ?? '',
      price: (json['price'] as num?)?.toDouble(),
      pickupAddress: json['pickup_address'] as String?,
      dropAddress: json['drop_address'] as String?,
      customerName: customer?['name'] as String?,
      customerMobile: customer?['mobile'] as String?,
      apartmentName: apartment?['name'] as String?,
      busStandName: busStand?['name'] as String?,
    );
  }

  final int id;
  final String bookingDate;
  final String slotTime;
  final String tripType;
  final String status;
  final double? price;
  final String? pickupAddress;
  final String? dropAddress;
  final String? customerName;
  final String? customerMobile;
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

class EarningModel {
  EarningModel({
    required this.id,
    required this.driverAmount,
    this.commissionAmount,
    this.createdAt,
    this.bookingId,
  });

  factory EarningModel.fromJson(Map<String, dynamic> json) {
    return EarningModel(
      id: json['id'] as int,
      driverAmount: (json['driver_amount'] as num?)?.toDouble() ?? 0,
      commissionAmount: (json['commission_amount'] as num?)?.toDouble(),
      createdAt: json['created_at'] as String?,
      bookingId: json['booking_id'] as int?,
    );
  }

  final int id;
  final double driverAmount;
  final double? commissionAmount;
  final String? createdAt;
  final int? bookingId;
}

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

class DriverModel {
  DriverModel({
    required this.id,
    required this.name,
    required this.mobile,
    this.isOnline = false,
  });

  factory DriverModel.fromJson(Map<String, dynamic> json) {
    return DriverModel(
      id: parseInt(json['id']) ?? 0,
      name: parseString(json['name']),
      mobile: parseString(json['mobile']),
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
      id: parseInt(json['id']) ?? 0,
      bookingDate: parseString(json['booking_date']),
      slotTime: parseString(json['slot_time']),
      tripType: parseString(json['trip_type']),
      status: parseString(json['status']),
      price: parseDouble(json['price']),
      pickupAddress: json['pickup_address']?.toString(),
      dropAddress: json['drop_address']?.toString(),
      customerName: customer?['name']?.toString(),
      customerMobile: customer?['mobile']?.toString(),
      apartmentName: apartment?['name']?.toString(),
      busStandName: busStand?['name']?.toString(),
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
      id: parseInt(json['id']) ?? 0,
      driverAmount: parseDouble(json['driver_amount']) ?? 0,
      commissionAmount: parseDouble(json['commission_amount']),
      createdAt: json['created_at']?.toString(),
      bookingId: parseInt(json['booking_id']),
    );
  }

  final int id;
  final double driverAmount;
  final double? commissionAmount;
  final String? createdAt;
  final int? bookingId;
}

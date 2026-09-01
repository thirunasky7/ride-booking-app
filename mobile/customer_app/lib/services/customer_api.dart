import '../models/models.dart';
import 'api_client.dart';

class CreateBookingResult {
  CreateBookingResult({required this.booking, this.payment});

  final BookingModel booking;
  final PaymentOrder? payment;
}

class PaymentOrder {
  PaymentOrder({
    required this.orderId,
    required this.amount,
    required this.currency,
    required this.keyId,
    required this.bookingId,
    this.customerName,
    this.customerMobile,
  });

  factory PaymentOrder.fromJson(Map<String, dynamic> json) {
    return PaymentOrder(
      orderId: parseString(json['order_id']),
      amount: parseInt(json['amount']) ?? 0,
      currency: parseString(json['currency']).isEmpty ? 'INR' : parseString(json['currency']),
      keyId: parseString(json['key_id']),
      bookingId: parseInt(json['booking_id']) ?? 0,
      customerName: json['customer_name']?.toString(),
      customerMobile: json['customer_mobile']?.toString(),
    );
  }

  final String orderId;
  final int amount;
  final String currency;
  final String keyId;
  final int bookingId;
  final String? customerName;
  final String? customerMobile;
}

class PaymentConfig {
  PaymentConfig({
    required this.razorpayEnabled,
    this.razorpayKeyId,
    this.siteName,
  });

  factory PaymentConfig.fromJson(Map<String, dynamic> json) {
    return PaymentConfig(
      razorpayEnabled: json['razorpay_enabled'] == true,
      razorpayKeyId: json['razorpay_key_id']?.toString(),
      siteName: json['site_name']?.toString(),
    );
  }

  final bool razorpayEnabled;
  final String? razorpayKeyId;
  final String? siteName;
}

class CustomerApi {
  CustomerApi(this._api);

  final ApiClient _api;

  Future<List<ApartmentModel>> apartments() async {
    final res = await _api.get('/apartments');
    final list = (res['data'] as Map)['apartments'] as List? ?? [];
    return list
        .map((e) => ApartmentModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<BusStandModel>> busStands() async {
    final res = await _api.get('/bus-stands');
    final list = (res['data'] as Map)['bus_stands'] as List? ?? [];
    return list
        .map((e) => BusStandModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<SlotModel>> availableSlots(String date) async {
    final res = await _api.get('/available-slots', query: {'booking_date': date});
    final list = (res['data'] as Map)['slots'] as List? ?? [];
    return list.map((e) => SlotModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<Map<String, dynamic>> calculatePrice(Map<String, String> query) async {
    final res = await _api.get('/calculate-price', query: query);
    return (res['data'] as Map).cast<String, dynamic>();
  }

  Future<PaymentConfig> paymentConfig() async {
    final res = await _api.get('/payment-config');
    return PaymentConfig.fromJson((res['data'] as Map).cast<String, dynamic>());
  }

  Future<CreateBookingResult> createBooking(Map<String, dynamic> body) async {
    final res = await _api.post('/create-booking', body: body, auth: true);
    final data = (res['data'] as Map).cast<String, dynamic>();
    final paymentJson = data['payment'];
    return CreateBookingResult(
      booking: BookingModel.fromJson(data['booking'] as Map<String, dynamic>),
      payment: paymentJson is Map<String, dynamic>
          ? PaymentOrder.fromJson(paymentJson)
          : null,
    );
  }

  Future<PaymentOrder> createPaymentOrder(int bookingId) async {
    final res = await _api.post('/bookings/$bookingId/create-payment-order', auth: true);
    final data = (res['data'] as Map).cast<String, dynamic>();
    final payment = (data['payment'] as Map).cast<String, dynamic>();
    payment['booking_id'] = data['booking_id'];
    return PaymentOrder.fromJson(payment);
  }

  Future<BookingModel> verifyPayment({
    required int bookingId,
    required String orderId,
    required String paymentId,
    required String signature,
  }) async {
    final res = await _api.post(
      '/verify-payment',
      body: {
        'booking_id': bookingId,
        'razorpay_order_id': orderId,
        'razorpay_payment_id': paymentId,
        'razorpay_signature': signature,
      },
      auth: true,
    );
    return BookingModel.fromJson(
      (res['data'] as Map)['booking'] as Map<String, dynamic>,
    );
  }

  Future<List<BookingModel>> bookingHistory() async {
    final res = await _api.get('/booking-history', auth: true);
    final list = (res['data'] as Map)['bookings'] as List? ?? [];
    return list
        .map((e) => BookingModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<List<BookingModel>> upcomingBookings() async {
    final res = await _api.get('/upcoming-bookings', auth: true);
    final list = (res['data'] as Map)['bookings'] as List? ?? [];
    return list
        .map((e) => BookingModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<void> cancelBooking(int id) async {
    await _api.post('/cancel-booking/$id', auth: true);
  }

  Future<BookingModel> updatePaymentStatus({
    required int bookingId,
    required String paymentStatus,
    String? paymentMethod,
  }) async {
    final body = <String, dynamic>{
      'payment_status': paymentStatus,
    };
    if (paymentMethod != null) {
      body['payment_method'] = paymentMethod;
    }
    final res = await _api.post(
      '/bookings/$bookingId/payment-status',
      body: body,
      auth: true,
    );
    return BookingModel.fromJson(
      (res['data'] as Map)['booking'] as Map<String, dynamic>,
    );
  }

  Future<List<SubscriptionPlan>> plans() async {
    final res = await _api.get('/subscription-plans', auth: true);
    final list = (res['data'] as Map)['plans'] as List? ?? [];
    return list
        .map((e) => SubscriptionPlan.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<Map<String, dynamic>?> mySubscription() async {
    final res = await _api.get('/my-subscription', auth: true);
    final sub = (res['data'] as Map)['subscription'];
    if (sub == null) return null;
    return (sub as Map).cast<String, dynamic>();
  }

  Future<void> submitSubscriptionEnquiry(Map<String, dynamic> body) async {
    await _api.post('/subscription-enquiry', body: body, auth: true);
  }

  Future<void> purchaseSubscription(int subscriptionId) async {
    await _api.post(
      '/purchase-subscription',
      body: {'subscription_id': subscriptionId},
      auth: true,
    );
  }
}

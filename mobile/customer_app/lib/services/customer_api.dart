import '../models/models.dart';
import 'api_client.dart';

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

  Future<BookingModel> createBooking(Map<String, dynamic> body) async {
    final res = await _api.post('/create-booking', body: body, auth: true);
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

  Future<void> purchaseSubscription(int subscriptionId) async {
    await _api.post(
      '/purchase-subscription',
      body: {'subscription_id': subscriptionId},
      auth: true,
    );
  }
}

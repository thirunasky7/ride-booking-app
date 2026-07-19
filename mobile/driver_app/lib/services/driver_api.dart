import '../models/models.dart';
import 'api_client.dart';

class DriverApi {
  DriverApi(this._api);

  final ApiClient _api;

  Future<Map<String, dynamic>> dashboard() async {
    final res = await _api.get('/driver/dashboard');
    return (res['data'] as Map).cast<String, dynamic>();
  }

  Future<List<TripModel>> todayTrips() async {
    final res = await _api.get('/driver/today-trips');
    final list = (res['data'] as Map)['trips'] as List? ?? [];
    return list.map((e) => TripModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<List<EarningModel>> earnings() async {
    final res = await _api.get('/driver/earnings');
    final data = res['data'] as Map;
    final earnings = data['earnings'];
    // Paginated: { data: [...] } or plain list
    List list;
    if (earnings is Map && earnings['data'] is List) {
      list = earnings['data'] as List;
    } else if (earnings is List) {
      list = earnings;
    } else {
      list = [];
    }
    return list.map((e) => EarningModel.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<void> startTrip(int id) async {
    await _api.post('/driver/start-trip/$id');
  }

  Future<void> completeTrip(int id) async {
    await _api.post('/driver/complete-trip/$id');
  }

  Future<bool> toggleOnline(bool isOnline) async {
    final res = await _api.post(
      '/driver/toggle-online',
      body: {'is_online': isOnline},
    );
    return (res['data'] as Map)['is_online'] == true;
  }
}

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

  Future<DriverModel> fetchProfile() async {
    final res = await _api.get('/driver/profile');
    final data = res['data'] as Map<String, dynamic>;
    return DriverModel.fromJson(data['driver'] as Map<String, dynamic>);
  }

  Future<DriverModel> updateProfile({
    required String name,
    String? licenseNumber,
    String? password,
    String? passwordConfirmation,
  }) async {
    final body = <String, dynamic>{
      'name': name,
      'license_number': licenseNumber ?? '',
    };
    if (password != null && password.isNotEmpty) {
      body['password'] = password;
      body['password_confirmation'] = passwordConfirmation ?? password;
    }
    final res = await _api.put('/driver/profile', body: body);
    final data = res['data'] as Map<String, dynamic>;
    return DriverModel.fromJson(data['driver'] as Map<String, dynamic>);
  }

  Future<void> logout() async {
    try {
      await _api.post('/driver/logout');
    } catch (_) {
      // Still clear local session if network fails.
    }
  }

  Future<void> deleteAccount() async {
    await _api.delete('/driver/account');
  }
}

import 'package:flutter/foundation.dart';

import '../models/models.dart';
import '../services/api_client.dart';

class AuthProvider extends ChangeNotifier {
  AuthProvider(this._api);

  final ApiClient _api;

  UserModel? user;
  bool loading = false;
  bool initialized = false;
  String? error;

  bool get isLoggedIn => user != null;

  Future<void> init() async {
    final token = await _api.getToken();
    if (token == null || token.isEmpty) {
      initialized = true;
      notifyListeners();
      return;
    }
    try {
      final res = await _api.get('/user', auth: true);
      final data = res['data'];
      if (data is Map<String, dynamic>) {
        user = UserModel.fromJson(data);
      }
    } catch (_) {
      await _api.clearToken();
      user = null;
    }
    initialized = true;
    notifyListeners();
  }

  Future<bool> sendOtp(String mobile) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      await _api.post('/send-otp', body: {'mobile': mobile});
      loading = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      error = e.message;
      loading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> verifyOtp(String mobile, String otp) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final res = await _api.post('/verify-otp', body: {
        'mobile': mobile,
        'otp': otp,
      });
      final data = res['data'] as Map<String, dynamic>;
      await _api.saveToken(data['token'] as String);
      user = UserModel.fromJson(data['user'] as Map<String, dynamic>);
      loading = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      error = e.message;
      loading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _api.post('/logout', auth: true);
    } catch (_) {
      // Still clear local session if network fails.
    }
    await _api.clearToken();
    user = null;
    notifyListeners();
  }

  Future<bool> deleteAccount() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      await _api.delete('/account', auth: true);
      await _api.clearToken();
      user = null;
      loading = false;
      notifyListeners();
      return true;
    } on ApiException catch (e) {
      error = e.message;
      loading = false;
      notifyListeners();
      return false;
    }
  }
}

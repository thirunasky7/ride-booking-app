import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../models/models.dart';
import '../services/api_client.dart';

class AuthProvider extends ChangeNotifier {
  AuthProvider(this._api);

  final ApiClient _api;
  static const _driverKey = 'driver_profile';

  DriverModel? driver;
  bool loading = false;
  bool initialized = false;
  String? error;

  bool get isLoggedIn => driver != null;

  Future<void> init() async {
    final token = await _api.getToken();
    if (token == null || token.isEmpty) {
      initialized = true;
      notifyListeners();
      return;
    }
    final prefs = await SharedPreferences.getInstance();
    final raw = prefs.getString(_driverKey);
    if (raw != null) {
      try {
        driver = DriverModel.fromJson(jsonDecode(raw) as Map<String, dynamic>);
      } catch (_) {
        await _api.clearToken();
        await prefs.remove(_driverKey);
      }
    } else {
      await _api.clearToken();
    }
    initialized = true;
    notifyListeners();
  }

  Future<bool> login(String mobile, String password) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final res = await _api.post(
        '/driver/login',
        body: {'mobile': mobile, 'password': password},
        auth: false,
      );
      final data = res['data'] as Map<String, dynamic>;
      await _api.saveToken(data['token'] as String);
      final driverJson = data['driver'] as Map<String, dynamic>;
      driver = DriverModel.fromJson(driverJson);
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_driverKey, jsonEncode(driverJson));
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
      await _api.post('/driver/logout');
    } catch (_) {
      // Still clear local session if network fails.
    }
    await _api.clearToken();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_driverKey);
    driver = null;
    notifyListeners();
  }

  Future<bool> updateProfile({
    required String name,
    String? licenseNumber,
    String? password,
    String? passwordConfirmation,
  }) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final res = await _api.put('/driver/profile', body: {
        'name': name,
        'license_number': licenseNumber ?? '',
        if (password != null && password.isNotEmpty) ...{
          'password': password,
          'password_confirmation': passwordConfirmation ?? password,
        },
      });
      final data = res['data'] as Map<String, dynamic>;
      final driverJson = data['driver'] as Map<String, dynamic>;
      driver = DriverModel.fromJson(driverJson);
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_driverKey, jsonEncode(driverJson));
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

  Future<bool> deleteAccount() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      await _api.delete('/driver/account');
      await _api.clearToken();
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_driverKey);
      driver = null;
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

  void updateOnline(bool value) {
    if (driver == null) return;
    driver = driver!.copyWith(isOnline: value);
    notifyListeners();
  }
}

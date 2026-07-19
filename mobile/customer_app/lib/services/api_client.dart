import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../config/api_config.dart';

class ApiException implements Exception {
  ApiException(this.message, {this.statusCode});

  final String message;
  final int? statusCode;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient();

  static const _tokenKey = 'auth_token';

  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
  }

  Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
  }

  Future<Map<String, dynamic>> get(
    String path, {
    Map<String, String>? query,
    bool auth = false,
  }) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$path').replace(queryParameters: query);
    final response = await http.get(uri, headers: await _headers(auth: auth));
    return _parse(response);
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? body,
    bool auth = false,
  }) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$path');
    final response = await http.post(
      uri,
      headers: await _headers(auth: auth),
      body: body == null ? null : jsonEncode(body),
    );
    return _parse(response);
  }

  Future<Map<String, dynamic>> put(
    String path, {
    Map<String, dynamic>? body,
    bool auth = true,
  }) async {
    final uri = Uri.parse('${ApiConfig.baseUrl}$path');
    final response = await http.put(
      uri,
      headers: await _headers(auth: auth),
      body: body == null ? null : jsonEncode(body),
    );
    return _parse(response);
  }

  Future<Map<String, String>> _headers({required bool auth}) async {
    final headers = <String, String>{
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };
    if (auth) {
      final token = await getToken();
      if (token != null && token.isNotEmpty) {
        headers['Authorization'] = 'Bearer $token';
      }
    }
    return headers;
  }

  Map<String, dynamic> _parse(http.Response response) {
    Map<String, dynamic> json;
    try {
      json = jsonDecode(response.body) as Map<String, dynamic>;
    } catch (_) {
      throw ApiException('Invalid server response (${response.statusCode}).',
          statusCode: response.statusCode);
    }

    final ok = json['status'] == true;
    if (!ok || response.statusCode >= 400) {
      throw ApiException(
        _errorMessage(json),
        statusCode: response.statusCode,
      );
    }
    return json;
  }

  String _errorMessage(Map<String, dynamic> json) {
    final errors = json['errors'];
    if (errors is Map && errors.isNotEmpty) {
      final parts = <String>[];
      errors.forEach((_, value) {
        if (value is List && value.isNotEmpty) {
          parts.add(value.first.toString());
        } else if (value != null) {
          parts.add(value.toString());
        }
      });
      if (parts.isNotEmpty) return parts.join('\n');
    }
    return (json['message'] as String?) ?? 'Request failed.';
  }
}

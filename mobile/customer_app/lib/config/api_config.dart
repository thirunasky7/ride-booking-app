/// Production Play Store builds must use HTTPS only.
/// Debug builds may use emulator cleartext via network_security_config debug-overrides.
class ApiConfig {
  static const String baseUrl = 'https://book.zennexs.com/api';

  /// Public policy pages (Play Console privacy / account deletion URLs).
  static const String privacyUrl = 'https://book.zennexs.com/privacy-policy';
  static const String termsUrl = 'https://book.zennexs.com/terms';
  static const String accountDeletionUrl = 'https://book.zennexs.com/account-deletion';
}

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';
import 'home_shell.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _mobileCtrl = TextEditingController();
  final _otpCtrl = TextEditingController();
  bool _otpSent = false;

  @override
  void dispose() {
    _mobileCtrl.dispose();
    _otpCtrl.dispose();
    super.dispose();
  }

  Future<void> _sendOtp() async {
    final mobile = _mobileCtrl.text.trim();
    if (mobile.length < 10) {
      _toast('Enter a valid 10-digit mobile number');
      return;
    }
    final ok = await context.read<AuthProvider>().sendOtp(mobile);
    if (!mounted) return;
    if (ok) {
      setState(() => _otpSent = true);
      _toast('OTP sent successfully');
    } else {
      _toast(context.read<AuthProvider>().error ?? 'Failed to send OTP');
    }
  }

  Future<void> _verifyOtp() async {
    final ok = await context.read<AuthProvider>().verifyOtp(
          _mobileCtrl.text.trim(),
          _otpCtrl.text.trim(),
        );
    if (!mounted) return;
    if (ok) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const HomeShell()),
      );
    } else {
      _toast(context.read<AuthProvider>().error ?? 'Invalid OTP');
    }
  }

  void _toast(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return Scaffold(
      backgroundColor: AppTheme.black,
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              flex: 4,
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 32, 24, 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: AppTheme.yellow,
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Icon(Icons.two_wheeler, size: 36, color: AppTheme.black),
                    ),
                    const SizedBox(height: 28),
                    const Text(
                      'India\'s everyday\nrides, simplified',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 32,
                        fontWeight: FontWeight.w900,
                        height: 1.15,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      'Book apartment shuttles in seconds',
                      style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 15),
                    ),
                  ],
                ),
              ),
            ),
            Expanded(
              flex: 6,
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 28, 24, 24),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
                ),
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Text(
                        _otpSent ? 'Enter OTP' : 'Login with mobile',
                        style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        _otpSent
                            ? 'We sent a code to ${_mobileCtrl.text}'
                            : 'We\'ll text you a one-time password',
                        style: const TextStyle(color: AppTheme.muted),
                      ),
                      const SizedBox(height: 24),
                      TextField(
                        controller: _mobileCtrl,
                        enabled: !_otpSent,
                        keyboardType: TextInputType.phone,
                        maxLength: 10,
                        inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                        decoration: const InputDecoration(
                          labelText: 'Mobile number',
                          prefixIcon: Icon(Icons.phone_android_rounded),
                          counterText: '',
                        ),
                      ),
                      if (_otpSent) ...[
                        const SizedBox(height: 14),
                        TextField(
                          controller: _otpCtrl,
                          keyboardType: TextInputType.number,
                          maxLength: 6,
                          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                          decoration: const InputDecoration(
                            labelText: '6-digit OTP',
                            prefixIcon: Icon(Icons.lock_outline_rounded),
                            counterText: '',
                          ),
                        ),
                      ],
                      const SizedBox(height: 24),
                      FilledButton(
                        onPressed: auth.loading ? null : (_otpSent ? _verifyOtp : _sendOtp),
                        child: auth.loading
                            ? const SizedBox(
                                height: 22,
                                width: 22,
                                child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.black),
                              )
                            : Text(_otpSent ? 'Verify & continue' : 'Get OTP'),
                      ),
                      if (_otpSent)
                        TextButton(
                          onPressed: auth.loading
                              ? null
                              : () => setState(() {
                                    _otpSent = false;
                                    _otpCtrl.clear();
                                  }),
                          child: const Text('Change number'),
                        ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

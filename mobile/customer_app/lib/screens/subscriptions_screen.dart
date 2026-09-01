import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

import '../models/models.dart';
import '../providers/auth_provider.dart';
import '../services/api_client.dart';
import '../services/customer_api.dart';
import '../theme/app_theme.dart';

class SubscriptionsScreen extends StatefulWidget {
  const SubscriptionsScreen({super.key, required this.api});

  final CustomerApi api;

  @override
  State<SubscriptionsScreen> createState() => _SubscriptionsScreenState();
}

class _SubscriptionsScreenState extends State<SubscriptionsScreen> {
  List<SubscriptionPlan> plans = [];
  Map<String, dynamic>? active;
  bool loading = true;
  bool submitting = false;

  final _formKey = GlobalKey<FormState>();
  final _nameCtrl = TextEditingController();
  final _mobileCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _messageCtrl = TextEditingController();
  int? _selectedPlanId;
  DateTime? _preferredStart;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _mobileCtrl.dispose();
    _emailCtrl.dispose();
    _messageCtrl.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    try {
      final user = context.read<AuthProvider>().user;
      final p = await widget.api.plans();
      final s = await widget.api.mySubscription();
      if (!mounted) return;
      setState(() {
        plans = p;
        active = s;
        loading = false;
        _nameCtrl.text = user?.name ?? '';
        _mobileCtrl.text = user?.mobile ?? '';
        _emailCtrl.text = user?.email ?? '';
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loading = false);
      _toast(e.message);
    }
  }

  Future<void> _submitEnquiry() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => submitting = true);
    try {
      await widget.api.submitSubscriptionEnquiry({
        'name': _nameCtrl.text.trim(),
        'mobile': _mobileCtrl.text.trim(),
        if (_emailCtrl.text.trim().isNotEmpty) 'email': _emailCtrl.text.trim(),
        if (_selectedPlanId != null) 'subscription_id': _selectedPlanId,
        if (_preferredStart != null)
          'preferred_start_date': DateFormat('yyyy-MM-dd').format(_preferredStart!),
        if (_messageCtrl.text.trim().isNotEmpty) 'message': _messageCtrl.text.trim(),
      });
      if (!mounted) return;
      _toast('Enquiry submitted! Our team will contact you soon.');
      _messageCtrl.clear();
    } on ApiException catch (e) {
      if (!mounted) return;
      _toast(e.message);
    } finally {
      if (mounted) setState(() => submitting = false);
    }
  }

  void _toast(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        title: const Text('Monthly Plans'),
        backgroundColor: AppTheme.black,
      ),
      body: loading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.yellow))
          : RefreshIndicator(
              color: AppTheme.black,
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(colors: [AppTheme.black, Color(0xFF2A2A2A)]),
                      borderRadius: BorderRadius.circular(22),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.workspace_premium, color: AppTheme.yellow),
                            SizedBox(width: 8),
                            Text(
                              'Monthly Subscription',
                              style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Text(
                          active == null
                              ? 'Save on daily commutes with a monthly ride bundle.'
                              : 'Active · rides left: ${active!['remaining_rides'] ?? '—'}',
                          style: TextStyle(color: Colors.white.withValues(alpha: 0.8)),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 18),
                  const Text('Available plans', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                  const SizedBox(height: 12),
                  ...plans.map((plan) {
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(18),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(18),
                        border: Border.all(
                          color: _selectedPlanId == plan.id ? AppTheme.yellowDark : Colors.grey.shade200,
                          width: _selectedPlanId == plan.id ? 2 : 1,
                        ),
                      ),
                      child: InkWell(
                        onTap: () => setState(() => _selectedPlanId = plan.id),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(plan.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                            const SizedBox(height: 6),
                            Text(
                              '${plan.ridesLimit} rides · ${plan.durationDays ?? '—'} days',
                              style: const TextStyle(color: AppTheme.muted),
                            ),
                            const SizedBox(height: 10),
                            Text(
                              '₹${plan.price.toStringAsFixed(0)}/month',
                              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900),
                            ),
                          ],
                        ),
                      ),
                    );
                  }),
                  const SizedBox(height: 8),
                  const Text('Request a plan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                  const SizedBox(height: 4),
                  const Text(
                    'Fill in your details and our team will contact you with the best offer.',
                    style: TextStyle(color: AppTheme.muted),
                  ),
                  const SizedBox(height: 14),
                  Form(
                    key: _formKey,
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(18),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Column(
                        children: [
                          TextFormField(
                            controller: _nameCtrl,
                            decoration: const InputDecoration(labelText: 'Full name'),
                            validator: (v) => (v == null || v.trim().isEmpty) ? 'Required' : null,
                          ),
                          const SizedBox(height: 12),
                          TextFormField(
                            controller: _mobileCtrl,
                            keyboardType: TextInputType.phone,
                            maxLength: 10,
                            decoration: const InputDecoration(labelText: 'Mobile'),
                            validator: (v) => (v == null || v.length != 10) ? 'Enter 10-digit mobile' : null,
                          ),
                          const SizedBox(height: 12),
                          TextFormField(
                            controller: _emailCtrl,
                            keyboardType: TextInputType.emailAddress,
                            decoration: const InputDecoration(labelText: 'Email (optional)'),
                          ),
                          const SizedBox(height: 12),
                          DropdownButtonFormField<int?>(
                            key: ValueKey('plan-$_selectedPlanId'),
                            initialValue: _selectedPlanId,
                            decoration: const InputDecoration(labelText: 'Interested plan'),
                            items: [
                              const DropdownMenuItem(value: null, child: Text('Any plan / Not sure')),
                              ...plans.map((p) => DropdownMenuItem(
                                    value: p.id,
                                    child: Text('${p.name} — ₹${p.price.toStringAsFixed(0)}'),
                                  )),
                            ],
                            onChanged: (v) => setState(() => _selectedPlanId = v),
                          ),
                          const SizedBox(height: 12),
                          ListTile(
                            contentPadding: EdgeInsets.zero,
                            title: const Text('Preferred start date'),
                            subtitle: Text(
                              _preferredStart == null
                                  ? 'Flexible'
                                  : DateFormat('dd MMM yyyy').format(_preferredStart!),
                            ),
                            trailing: const Icon(Icons.calendar_today_outlined),
                            onTap: () async {
                              final picked = await showDatePicker(
                                context: context,
                                initialDate: DateTime.now(),
                                firstDate: DateTime.now(),
                                lastDate: DateTime.now().add(const Duration(days: 90)),
                              );
                              if (picked != null) setState(() => _preferredStart = picked);
                            },
                          ),
                          const SizedBox(height: 12),
                          TextFormField(
                            controller: _messageCtrl,
                            maxLines: 3,
                            decoration: const InputDecoration(
                              labelText: 'Message (optional)',
                              hintText: 'Daily route, rides per week, etc.',
                            ),
                          ),
                          const SizedBox(height: 18),
                          SizedBox(
                            width: double.infinity,
                            child: FilledButton(
                              onPressed: submitting ? null : _submitEnquiry,
                              child: submitting
                                  ? const SizedBox(
                                      width: 22,
                                      height: 22,
                                      child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.black),
                                    )
                                  : const Text('Submit Enquiry'),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }
}

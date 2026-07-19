import 'package:flutter/material.dart';

import '../models/models.dart';
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
  int? purchasingId;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    try {
      final p = await widget.api.plans();
      final s = await widget.api.mySubscription();
      if (!mounted) return;
      setState(() {
        plans = p;
        active = s;
        loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loading = false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }

  Future<void> _purchase(SubscriptionPlan plan) async {
    setState(() => purchasingId = plan.id);
    try {
      await widget.api.purchaseSubscription(plan.id);
      await _load();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pass activated')),
      );
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => purchasingId = null);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        title: const Text('Ride Pass'),
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
                              'Apartment Shuttle Pass',
                              style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 16),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Text(
                          active == null
                              ? 'No active pass. Pick a plan and save on daily rides.'
                              : 'Active · rides left: ${active!['remaining_rides'] ?? '—'}',
                          style: TextStyle(color: Colors.white.withValues(alpha: 0.8)),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 18),
                  const Text('Choose a plan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                  const SizedBox(height: 12),
                  ...plans.map((plan) {
                    final busy = purchasingId == plan.id;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(18),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(18),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(plan.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                          const SizedBox(height: 6),
                          Text(
                            '${plan.ridesLimit} rides · ${plan.durationDays ?? '—'} days',
                            style: const TextStyle(color: AppTheme.muted),
                          ),
                          const SizedBox(height: 14),
                          Row(
                            children: [
                              Text(
                                '₹${plan.price.toStringAsFixed(0)}',
                                style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w900),
                              ),
                              const Spacer(),
                              FilledButton(
                                onPressed: busy ? null : () => _purchase(plan),
                                child: busy
                                    ? const SizedBox(
                                        width: 18,
                                        height: 18,
                                        child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.black),
                                      )
                                    : const Text('Buy pass'),
                              ),
                            ],
                          ),
                        ],
                      ),
                    );
                  }),
                ],
              ),
            ),
    );
  }
}

import 'package:flutter/material.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/customer_api.dart';

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
        const SnackBar(content: Text('Subscription activated')),
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
    if (loading) return const Center(child: CircularProgressIndicator());

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          if (active != null)
            Card(
              color: const Color(0xFFCCFBF1),
              child: ListTile(
                leading: const Icon(Icons.verified, color: Color(0xFF0F766E)),
                title: Text(
                  (active!['subscription'] is Map)
                      ? ((active!['subscription'] as Map)['name']?.toString() ?? 'Active plan')
                      : 'Active subscription',
                ),
                subtitle: Text(
                  'Rides left: ${active!['rides_remaining'] ?? active!['remaining_rides'] ?? '—'}',
                ),
              ),
            ),
          const SizedBox(height: 8),
          ...plans.map((plan) {
            final busy = purchasingId == plan.id;
            return Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(plan.name, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text('${plan.ridesLimit} rides · ${plan.durationDays ?? '—'} days'),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Text(
                          '₹${plan.price.toStringAsFixed(0)}',
                          style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Color(0xFF0F766E)),
                        ),
                        const Spacer(),
                        FilledButton(
                          onPressed: busy ? null : () => _purchase(plan),
                          child: busy
                              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                              : const Text('Buy'),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            );
          }),
        ],
      ),
    );
  }
}

import 'package:flutter/material.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/driver_api.dart';

class EarningsScreen extends StatefulWidget {
  const EarningsScreen({super.key, required this.api});

  final DriverApi api;

  @override
  State<EarningsScreen> createState() => _EarningsScreenState();
}

class _EarningsScreenState extends State<EarningsScreen> {
  List<EarningModel> earnings = [];
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    try {
      final list = await widget.api.earnings();
      if (!mounted) return;
      setState(() {
        earnings = list;
        loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loading = false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }

  @override
  Widget build(BuildContext context) {
    if (loading) return const Center(child: CircularProgressIndicator());

    if (earnings.isEmpty) {
      return RefreshIndicator(
        onRefresh: _load,
        child: ListView(
          children: const [
            SizedBox(height: 120),
            Center(child: Text('No earnings yet')),
          ],
        ),
      );
    }

    final total = earnings.fold<double>(0, (sum, e) => sum + e.driverAmount);

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Card(
            color: const Color(0xFFDBEAFE),
            child: ListTile(
              leading: const Icon(Icons.account_balance_wallet, color: Color(0xFF1D4ED8)),
              title: const Text('Shown earnings'),
              trailing: Text(
                '₹${total.toStringAsFixed(0)}',
                style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
              ),
            ),
          ),
          const SizedBox(height: 8),
          ...earnings.map(
            (e) => Card(
              child: ListTile(
                title: Text('Booking #${e.bookingId ?? e.id}'),
                subtitle: Text(e.createdAt ?? ''),
                trailing: Text(
                  '₹${e.driverAmount.toStringAsFixed(0)}',
                  style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8)),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

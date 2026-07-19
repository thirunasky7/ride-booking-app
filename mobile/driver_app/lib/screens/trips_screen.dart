import 'package:flutter/material.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/driver_api.dart';

class TripsScreen extends StatefulWidget {
  const TripsScreen({super.key, required this.api});

  final DriverApi api;

  @override
  State<TripsScreen> createState() => _TripsScreenState();
}

class _TripsScreenState extends State<TripsScreen> {
  List<TripModel> trips = [];
  bool loading = true;
  int? actionId;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    try {
      final list = await widget.api.todayTrips();
      if (!mounted) return;
      setState(() {
        trips = list;
        loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loading = false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }

  Future<void> _start(TripModel trip) async {
    setState(() => actionId = trip.id);
    try {
      await widget.api.startTrip(trip.id);
      await _load();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Trip started')));
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => actionId = null);
    }
  }

  Future<void> _complete(TripModel trip) async {
    setState(() => actionId = trip.id);
    try {
      await widget.api.completeTrip(trip.id);
      await _load();
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Trip completed')));
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => actionId = null);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (loading) return const Center(child: CircularProgressIndicator());

    if (trips.isEmpty) {
      return RefreshIndicator(
        onRefresh: _load,
        child: ListView(
          children: const [
            SizedBox(height: 120),
            Center(child: Text('No trips scheduled for today')),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: trips.length,
        separatorBuilder: (_, _) => const SizedBox(height: 8),
        itemBuilder: (context, i) {
          final t = trips[i];
          final busy = actionId == t.id;
          return Card(
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          '#${t.id} · ${t.status.toUpperCase()}',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ),
                      if (t.price != null)
                        Text(
                          '₹${t.price!.toStringAsFixed(0)}',
                          style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF1D4ED8)),
                        ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text('${t.pickupLabel} → ${t.dropLabel}'),
                  Text('Slot: ${t.slotTime}'),
                  if (t.customerName != null || t.customerMobile != null)
                    Text('Customer: ${t.customerName ?? ''} ${t.customerMobile ?? ''}'.trim()),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      if (t.status == 'confirmed' || t.status == 'pending')
                        FilledButton(
                          onPressed: busy ? null : () => _start(t),
                          child: busy
                              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                              : const Text('Start'),
                        ),
                      if (t.status == 'started') ...[
                        const SizedBox(width: 8),
                        FilledButton(
                          onPressed: busy ? null : () => _complete(t),
                          child: busy
                              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                              : const Text('Complete'),
                        ),
                      ],
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

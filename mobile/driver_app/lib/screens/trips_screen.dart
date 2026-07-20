import 'package:flutter/material.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/driver_api.dart';
import '../theme/app_theme.dart';

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

  Color _statusColor(String status) {
    switch (status) {
      case 'started':
        return AppTheme.yellow;
      case 'completed':
        return AppTheme.success;
      case 'cancelled':
        return AppTheme.danger;
      default:
        return Colors.blueGrey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.bg,
      body: Column(
        children: [
          Container(
            width: double.infinity,
            padding: EdgeInsets.fromLTRB(20, MediaQuery.of(context).padding.top + 16, 20, 20),
            decoration: const BoxDecoration(
              color: AppTheme.black,
              borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
            ),
            child: const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Today's trips",
                  style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900),
                ),
                SizedBox(height: 4),
                Text(
                  'Start and complete assigned shuttles',
                  style: TextStyle(color: Colors.white70),
                ),
              ],
            ),
          ),
          Expanded(
            child: loading
                ? const Center(child: CircularProgressIndicator(color: AppTheme.yellow))
                : RefreshIndicator(
                    color: AppTheme.black,
                    onRefresh: _load,
                    child: trips.isEmpty
                        ? ListView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            children: [
                              const SizedBox(height: 80),
                              Icon(Icons.route_rounded, size: 56, color: Colors.grey.shade400),
                              const SizedBox(height: 12),
                              const Center(
                                child: Text(
                                  'No trips scheduled for today',
                                  style: TextStyle(color: AppTheme.muted, fontWeight: FontWeight.w600),
                                ),
                              ),
                            ],
                          )
                        : ListView.separated(
                            physics: const AlwaysScrollableScrollPhysics(),
                            padding: const EdgeInsets.all(16),
                            itemCount: trips.length,
                            separatorBuilder: (_, _) => const SizedBox(height: 12),
                            itemBuilder: (context, i) {
                              final t = trips[i];
                              final busy = actionId == t.id;
                              final statusColor = _statusColor(t.status);

                              return Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(20),
                                  border: Border.all(color: Colors.grey.shade200),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Text(
                                          '#${t.id}',
                                          style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                                        ),
                                        const SizedBox(width: 8),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                          decoration: BoxDecoration(
                                            color: statusColor.withValues(alpha: 0.18),
                                            borderRadius: BorderRadius.circular(20),
                                          ),
                                          child: Text(
                                            t.status.toUpperCase(),
                                            style: TextStyle(
                                              fontSize: 11,
                                              fontWeight: FontWeight.w800,
                                              color: statusColor == AppTheme.yellow ? AppTheme.black : statusColor,
                                            ),
                                          ),
                                        ),
                                        const Spacer(),
                                        if (t.price != null)
                                          Text(
                                            '₹${t.price!.toStringAsFixed(0)}',
                                            style: const TextStyle(
                                              fontWeight: FontWeight.w900,
                                              fontSize: 18,
                                            ),
                                          ),
                                      ],
                                    ),
                                    const SizedBox(height: 14),
                                    _RouteLine(pickup: t.pickupLabel, drop: t.dropLabel),
                                    const SizedBox(height: 12),
                                    Row(
                                      children: [
                                        const Icon(Icons.schedule_rounded, size: 16, color: AppTheme.muted),
                                        const SizedBox(width: 6),
                                        Text('Slot ${t.slotTime}', style: const TextStyle(color: AppTheme.muted)),
                                      ],
                                    ),
                                    if (t.customerName != null || t.customerMobile != null) ...[
                                      const SizedBox(height: 6),
                                      Row(
                                        children: [
                                          const Icon(Icons.person_outline_rounded, size: 16, color: AppTheme.muted),
                                          const SizedBox(width: 6),
                                          Expanded(
                                            child: Text(
                                              '${t.customerName ?? ''} ${t.customerMobile ?? ''}'.trim(),
                                              style: const TextStyle(color: AppTheme.muted),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ],
                                    if (t.status == 'confirmed' || t.status == 'pending' || t.status == 'started') ...[
                                      const SizedBox(height: 14),
                                      SizedBox(
                                        width: double.infinity,
                                        child: FilledButton(
                                          style: t.status == 'started'
                                              ? FilledButton.styleFrom(
                                                  backgroundColor: AppTheme.black,
                                                  foregroundColor: AppTheme.yellow,
                                                )
                                              : null,
                                          onPressed: busy
                                              ? null
                                              : () => t.status == 'started' ? _complete(t) : _start(t),
                                          child: busy
                                              ? SizedBox(
                                                  width: 18,
                                                  height: 18,
                                                  child: CircularProgressIndicator(
                                                    strokeWidth: 2,
                                                    color: t.status == 'started' ? AppTheme.yellow : AppTheme.black,
                                                  ),
                                                )
                                              : Text(t.status == 'started' ? 'Complete trip' : 'Start trip'),
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                              );
                            },
                          ),
                  ),
          ),
        ],
      ),
    );
  }
}

class _RouteLine extends StatelessWidget {
  const _RouteLine({required this.pickup, required this.drop});

  final String pickup;
  final String drop;

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          children: [
            Container(
              width: 10,
              height: 10,
              decoration: const BoxDecoration(color: AppTheme.yellow, shape: BoxShape.circle),
            ),
            Container(width: 2, height: 28, color: Colors.grey.shade300),
            Container(
              width: 10,
              height: 10,
              decoration: const BoxDecoration(color: AppTheme.black, shape: BoxShape.circle),
            ),
          ],
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(pickup, style: const TextStyle(fontWeight: FontWeight.w700)),
              const SizedBox(height: 18),
              Text(drop, style: const TextStyle(fontWeight: FontWeight.w700)),
            ],
          ),
        ),
      ],
    );
  }
}

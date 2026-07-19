import 'package:flutter/material.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/customer_api.dart';
import '../theme/app_theme.dart';

class BookingsScreen extends StatefulWidget {
  const BookingsScreen({super.key, required this.api});

  final CustomerApi api;

  @override
  State<BookingsScreen> createState() => _BookingsScreenState();
}

class _BookingsScreenState extends State<BookingsScreen> with SingleTickerProviderStateMixin {
  late final TabController _tabs = TabController(length: 2, vsync: this);
  List<BookingModel> upcoming = [];
  List<BookingModel> history = [];
  bool loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _tabs.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    try {
      final u = await widget.api.upcomingBookings();
      final h = await widget.api.bookingHistory();
      if (!mounted) return;
      setState(() {
        upcoming = u;
        history = h;
        loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loading = false);
      _toast(e.message);
    }
  }

  void _toast(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  Future<void> _cancel(BookingModel booking) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Cancel booking?'),
        content: Text('Cancel trip #${booking.id}?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('No')),
          FilledButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Yes, cancel')),
        ],
      ),
    );
    if (confirm != true) return;
    try {
      await widget.api.cancelBooking(booking.id);
      await _load();
      if (!mounted) return;
      _toast('Booking cancelled');
    } on ApiException catch (e) {
      if (!mounted) return;
      _toast(e.message);
    }
  }

  Future<void> _markPaid(BookingModel booking) async {
    String method = 'upi';
    final confirmed = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setSheet) {
            return Padding(
              padding: EdgeInsets.fromLTRB(20, 16, 20, MediaQuery.of(ctx).viewInsets.bottom + 24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(4)),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text('Mark payment as paid', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
                  const SizedBox(height: 6),
                  Text(
                    'Trip #${booking.id} · ₹${booking.price?.toStringAsFixed(0) ?? '—'}',
                    style: const TextStyle(color: AppTheme.muted),
                  ),
                  const SizedBox(height: 18),
                  const Text('Payment method', style: TextStyle(fontWeight: FontWeight.w700)),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      Expanded(
                        child: _MethodTile(
                          label: 'UPI',
                          icon: Icons.qr_code_2_rounded,
                          selected: method == 'upi',
                          onTap: () => setSheet(() => method = 'upi'),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: _MethodTile(
                          label: 'Cash',
                          icon: Icons.payments_rounded,
                          selected: method == 'cash',
                          onTap: () => setSheet(() => method = 'cash'),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  FilledButton(
                    onPressed: () => Navigator.pop(ctx, true),
                    child: const Text('Confirm paid'),
                  ),
                ],
              ),
            );
          },
        );
      },
    );

    if (confirmed != true) return;

    try {
      await widget.api.updatePaymentStatus(
        bookingId: booking.id,
        paymentStatus: 'paid',
        paymentMethod: method,
      );
      await _load();
      if (!mounted) return;
      _toast('Payment marked as paid');
    } on ApiException catch (e) {
      if (!mounted) return;
      _toast(e.message);
    }
  }

  Widget _list(List<BookingModel> items, {required bool canAct}) {
    if (loading) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.yellow));
    }
    if (items.isEmpty) {
      return const Center(
        child: Text('No trips yet', style: TextStyle(color: AppTheme.muted, fontWeight: FontWeight.w600)),
      );
    }
    return RefreshIndicator(
      color: AppTheme.black,
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: items.length,
        separatorBuilder: (_, _) => const SizedBox(height: 12),
        itemBuilder: (context, i) {
          final b = items[i];
          return Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text('#${b.id}', style: const TextStyle(fontWeight: FontWeight.w800)),
                    const SizedBox(width: 8),
                    _Pill(text: b.status.toUpperCase(), bg: Colors.grey.shade100, fg: AppTheme.black),
                    const Spacer(),
                    if (b.price != null)
                      Text(
                        '₹${b.price!.toStringAsFixed(0)}',
                        style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 18),
                      ),
                  ],
                ),
                const SizedBox(height: 12),
                _RouteRow(from: b.pickupLabel, to: b.dropLabel),
                const SizedBox(height: 10),
                Text(
                  '${b.bookingDate.split('T').first} · ${b.slotTime}',
                  style: const TextStyle(color: AppTheme.muted, fontSize: 13),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    _Pill(
                      text: b.isPaid
                          ? 'PAID${b.paymentMethod != null ? ' · ${b.paymentMethod!.toUpperCase()}' : ''}'
                          : 'UNPAID',
                      bg: b.isPaid ? const Color(0xFFDCFCE7) : const Color(0xFFFFE4E6),
                      fg: b.isPaid ? AppTheme.success : AppTheme.danger,
                    ),
                    const Spacer(),
                    if (canAct && !b.isPaid && b.status != 'cancelled')
                      TextButton(
                        onPressed: () => _markPaid(b),
                        child: const Text('Mark paid', style: TextStyle(fontWeight: FontWeight.w800)),
                      ),
                    if (canAct && (b.status == 'pending' || b.status == 'confirmed'))
                      TextButton(
                        onPressed: () => _cancel(b),
                        child: const Text('Cancel', style: TextStyle(color: AppTheme.danger)),
                      ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        title: const Text('My trips'),
        backgroundColor: AppTheme.black,
        bottom: TabBar(
          controller: _tabs,
          indicatorColor: AppTheme.yellow,
          labelColor: AppTheme.yellow,
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(text: 'Upcoming'),
            Tab(text: 'History'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabs,
        children: [
          _list(upcoming, canAct: true),
          _list(history, canAct: true),
        ],
      ),
    );
  }
}

class _Pill extends StatelessWidget {
  const _Pill({required this.text, required this.bg, required this.fg});

  final String text;
  final Color bg;
  final Color fg;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(20)),
      child: Text(text, style: TextStyle(color: fg, fontSize: 11, fontWeight: FontWeight.w800)),
    );
  }
}

class _RouteRow extends StatelessWidget {
  const _RouteRow({required this.from, required this.to});

  final String from;
  final String to;

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          children: [
            Container(width: 10, height: 10, decoration: const BoxDecoration(color: AppTheme.yellow, shape: BoxShape.circle)),
            Container(width: 2, height: 22, color: Colors.grey.shade300),
            Container(width: 10, height: 10, decoration: const BoxDecoration(color: AppTheme.black, shape: BoxShape.circle)),
          ],
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(from, style: const TextStyle(fontWeight: FontWeight.w600)),
              const SizedBox(height: 14),
              Text(to, style: const TextStyle(fontWeight: FontWeight.w600)),
            ],
          ),
        ),
      ],
    );
  }
}

class _MethodTile extends StatelessWidget {
  const _MethodTile({
    required this.label,
    required this.icon,
    required this.selected,
    required this.onTap,
  });

  final String label;
  final IconData icon;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(
          color: selected ? AppTheme.yellow.withValues(alpha: 0.35) : Colors.grey.shade50,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: selected ? AppTheme.yellowDark : Colors.grey.shade300, width: 1.4),
        ),
        child: Column(
          children: [
            Icon(icon, color: AppTheme.black),
            const SizedBox(height: 6),
            Text(label, style: const TextStyle(fontWeight: FontWeight.w800)),
          ],
        ),
      ),
    );
  }
}

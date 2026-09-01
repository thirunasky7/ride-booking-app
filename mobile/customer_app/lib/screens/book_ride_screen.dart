import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/customer_api.dart';
import '../services/razorpay_checkout.dart';
import '../theme/app_theme.dart';

class BookRideScreen extends StatefulWidget {
  const BookRideScreen({super.key, required this.api});

  final CustomerApi api;

  @override
  State<BookRideScreen> createState() => _BookRideScreenState();
}

class _BookRideScreenState extends State<BookRideScreen> {
  List<ApartmentModel> apartments = [];
  List<BusStandModel> busStands = [];
  List<SlotModel> slots = [];

  String? pickupLocation;
  String? dropLocation;
  DateTime bookingDate = DateTime.now();
  SlotModel? selectedSlot;
  double? estimatedFare;
  String? fareType;
  bool loadingMeta = true;
  bool submitting = false;
  PaymentConfig? paymentConfig;

  final pickupCtrl = TextEditingController();
  final dropCtrl = TextEditingController();
  late final RazorpayCheckout _checkout = RazorpayCheckout(widget.api);

  bool get isOtherPickup => pickupLocation == 'other';
  bool get isOtherDrop => dropLocation == 'other';
  bool get usesCustomFields => isOtherPickup || isOtherDrop;

  @override
  void initState() {
    super.initState();
    _bootstrap();
  }

  @override
  void dispose() {
    pickupCtrl.dispose();
    dropCtrl.dispose();
    _checkout.dispose();
    super.dispose();
  }

  Future<void> _bootstrap() async {
    try {
      final results = await Future.wait([
        widget.api.apartments(),
        widget.api.busStands(),
        widget.api.paymentConfig(),
      ]);
      if (!mounted) return;
      setState(() {
        apartments = results[0] as List<ApartmentModel>;
        busStands = results[1] as List<BusStandModel>;
        paymentConfig = results[2] as PaymentConfig;
        loadingMeta = false;
      });
      await _loadSlots();
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loadingMeta = false);
      _toast(e.message);
    }
  }

  Future<void> _loadSlots() async {
    final date = DateFormat('yyyy-MM-dd').format(bookingDate);
    try {
      final list = await widget.api.availableSlots(date);
      if (!mounted) return;
      setState(() {
        slots = list.where((s) => s.isAvailable).toList();
        selectedSlot = slots.isNotEmpty ? slots.first : null;
      });
      await _fetchPrice();
    } on ApiException catch (e) {
      if (!mounted) return;
      _toast(e.message);
    }
  }

  Map<String, String> _priceQuery() {
    final query = <String, String>{
      'booking_date': DateFormat('yyyy-MM-dd').format(bookingDate),
      'slot_time': selectedSlot!.slotTime,
      'time_slot_id': '${selectedSlot!.timeSlotId}',
      'pickup_location': pickupLocation!,
      'drop_location': dropLocation!,
    };
    if (isOtherPickup) query['pickup_address'] = pickupCtrl.text.trim();
    if (isOtherDrop) query['drop_address'] = dropCtrl.text.trim();
    return query;
  }

  Map<String, dynamic> _bookingBody() {
    final body = <String, dynamic>{
      'pickup_location': pickupLocation,
      'drop_location': dropLocation,
      'booking_date': DateFormat('yyyy-MM-dd').format(bookingDate),
      'slot_time': selectedSlot!.slotTime,
      'time_slot_id': selectedSlot!.timeSlotId,
    };
    if (isOtherPickup) body['pickup_address'] = pickupCtrl.text.trim();
    if (isOtherDrop) body['drop_address'] = dropCtrl.text.trim();
    return body;
  }

  Future<void> _fetchPrice() async {
    if (selectedSlot == null || pickupLocation == null || dropLocation == null) return;
    if (isOtherPickup && pickupCtrl.text.trim().isEmpty) return;
    if (isOtherDrop && dropCtrl.text.trim().isEmpty) return;

    try {
      final data = await widget.api.calculatePrice(_priceQuery());
      if (!mounted) return;
      setState(() {
        estimatedFare = parseDouble(data['estimated_fare']);
        fareType = data['booking_type']?.toString();
      });
    } catch (_) {}
  }

  bool _validate() {
    if (pickupLocation == null) {
      _toast('Select pickup location');
      return false;
    }
    if (dropLocation == null) {
      _toast('Select drop location');
      return false;
    }
    if (pickupLocation == dropLocation && pickupLocation != 'other') {
      _toast('Pickup and drop cannot be the same');
      return false;
    }
    if (selectedSlot == null) {
      _toast('Select a time slot');
      return false;
    }
    if (isOtherPickup && pickupCtrl.text.trim().isEmpty) {
      _toast('Enter pickup address');
      return false;
    }
    if (isOtherDrop && dropCtrl.text.trim().isEmpty) {
      _toast('Enter drop address');
      return false;
    }
    return true;
  }

  Future<void> _submit() async {
    if (!_validate()) return;

    setState(() => submitting = true);
    try {
      final result = await widget.api.createBooking(_bookingBody());
      if (!mounted) return;

      if (result.payment != null) {
        await _checkout.pay(
          order: result.payment!,
          onSuccess: () {
            if (!mounted) return;
            _toast('Payment successful! Booking confirmed.');
            _resetForm();
          },
          onError: (msg) {
            if (!mounted) return;
            _toast(msg);
          },
        );
      } else {
        _toast('Booking confirmed successfully');
        _resetForm();
      }
    } on ApiException catch (e) {
      if (!mounted) return;
      _toast(e.message);
    } catch (e) {
      if (!mounted) return;
      _toast('Booking failed: $e');
    } finally {
      if (mounted) setState(() => submitting = false);
    }
  }

  void _resetForm() {
    setState(() {
      pickupLocation = null;
      dropLocation = null;
      pickupCtrl.clear();
      dropCtrl.clear();
      estimatedFare = null;
    });
  }

  void _swapLocations() {
    setState(() {
      final tmp = pickupLocation;
      pickupLocation = dropLocation;
      dropLocation = tmp;
      if (usesCustomFields) {
        final tmpText = pickupCtrl.text;
        pickupCtrl.text = dropCtrl.text;
        dropCtrl.text = tmpText;
      }
    });
    _fetchPrice();
  }

  void _toast(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  String _formatSlot(String t) {
    try {
      final p = t.split(':');
      final h = int.parse(p[0]);
      final m = p[1];
      final ampm = h >= 12 ? 'PM' : 'AM';
      final hour = (h % 12 == 0) ? 12 : h % 12;
      return '$hour:$m $ampm';
    } catch (_) {
      return t;
    }
  }

  String _labelFor(String? value) {
    if (value == null) return 'Select location';
    if (value == 'other') return 'Custom address';
    if (value.startsWith('apartment:')) {
      final id = int.tryParse(value.split(':').last);
      return apartments.firstWhere((a) => a.id == id, orElse: () => ApartmentModel(id: 0, name: 'Apartment')).name;
    }
    if (value.startsWith('busstand:')) {
      final id = int.tryParse(value.split(':').last);
      return busStands.firstWhere((b) => b.id == id, orElse: () => BusStandModel(id: 0, name: 'Bus stand')).name;
    }
    return value;
  }

  List<DropdownMenuItem<String>> _locationItems() {
    return [
      ...apartments.map((a) => DropdownMenuItem(value: 'apartment:${a.id}', child: Text('🏢 ${a.name}'))),
      ...busStands.map((b) => DropdownMenuItem(value: 'busstand:${b.id}', child: Text('🚏 ${b.name}'))),
      const DropdownMenuItem(value: 'other', child: Text('📍 Custom address')),
    ];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.bg,
      appBar: AppBar(
        title: const Text('Book a ride'),
        backgroundColor: AppTheme.black,
      ),
      body: loadingMeta
          ? const Center(child: CircularProgressIndicator(color: AppTheme.yellow))
          : ListView(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 120),
              children: [
                const Text('Where are you going?', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                const SizedBox(height: 6),
                const Text('Select pickup and drop from the list below', style: TextStyle(color: AppTheme.muted)),
                const SizedBox(height: 16),
                _whiteCard(
                  child: Column(
                    children: [
                      _LocationDropdown(
                        label: 'Pickup',
                        dotColor: AppTheme.success,
                        value: pickupLocation,
                        items: _locationItems(),
                        onChanged: (v) {
                          setState(() => pickupLocation = v);
                          _fetchPrice();
                        },
                      ),
                      Row(
                        children: [
                          const Expanded(child: Divider()),
                          IconButton(
                            onPressed: _swapLocations,
                            icon: const Icon(Icons.swap_vert_rounded),
                            tooltip: 'Swap',
                          ),
                          const Expanded(child: Divider()),
                        ],
                      ),
                      _LocationDropdown(
                        label: 'Drop',
                        dotColor: AppTheme.black,
                        value: dropLocation,
                        items: _locationItems(),
                        onChanged: (v) {
                          setState(() => dropLocation = v);
                          _fetchPrice();
                        },
                      ),
                    ],
                  ),
                ),
                if (usesCustomFields) ...[
                  const SizedBox(height: 12),
                  _whiteCard(
                    child: Column(
                      children: [
                        if (isOtherPickup)
                          TextField(
                            controller: pickupCtrl,
                            maxLines: 2,
                            decoration: const InputDecoration(
                              labelText: 'Pickup address',
                              border: InputBorder.none,
                            ),
                            onChanged: (_) => _fetchPrice(),
                          ),
                        if (isOtherPickup && isOtherDrop) const Divider(height: 1),
                        if (isOtherDrop)
                          TextField(
                            controller: dropCtrl,
                            maxLines: 2,
                            decoration: const InputDecoration(
                              labelText: 'Drop address',
                              border: InputBorder.none,
                            ),
                            onChanged: (_) => _fetchPrice(),
                          ),
                      ],
                    ),
                  ),
                ],
                if (pickupLocation != null && dropLocation != null) ...[
                  const SizedBox(height: 12),
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: AppTheme.yellow.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.route_rounded, size: 20),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            '${_labelFor(pickupLocation)} → ${_labelFor(dropLocation)}',
                            style: const TextStyle(fontWeight: FontWeight.w700),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 20),
                const Text('When', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                const SizedBox(height: 10),
                _whiteCard(
                  child: Column(
                    children: [
                      ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: const CircleAvatar(
                          backgroundColor: AppTheme.yellow,
                          child: Icon(Icons.calendar_today_rounded, color: AppTheme.black, size: 18),
                        ),
                        title: const Text('Date', style: TextStyle(fontWeight: FontWeight.w600)),
                        subtitle: Text(DateFormat('EEE, dd MMM yyyy').format(bookingDate)),
                        trailing: const Icon(Icons.chevron_right_rounded),
                        onTap: () async {
                          final picked = await showDatePicker(
                            context: context,
                            initialDate: bookingDate,
                            firstDate: DateTime.now(),
                            lastDate: DateTime.now().add(const Duration(days: 60)),
                          );
                          if (picked != null) {
                            setState(() => bookingDate = picked);
                            await _loadSlots();
                          }
                        },
                      ),
                      const Divider(height: 1),
                      DropdownButtonFormField<SlotModel>(
                        key: ValueKey('slot-${selectedSlot?.timeSlotId}'),
                        initialValue: selectedSlot,
                        decoration: const InputDecoration(
                          labelText: 'Time slot',
                          border: InputBorder.none,
                        ),
                        items: slots
                            .map((s) => DropdownMenuItem(value: s, child: Text(_formatSlot(s.slotTime))))
                            .toList(),
                        onChanged: (v) {
                          setState(() => selectedSlot = v);
                          _fetchPrice();
                        },
                      ),
                    ],
                  ),
                ),
                if (paymentConfig?.razorpayEnabled == true) ...[
                  const SizedBox(height: 12),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey.shade200),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.shield_outlined, color: AppTheme.success, size: 18),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text('Secure payment via Razorpay', style: TextStyle(fontSize: 13, color: AppTheme.muted)),
                        ),
                      ],
                    ),
                  ),
                ],
              ],
            ),
      bottomSheet: estimatedFare == null && !submitting
          ? null
          : Container(
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 24),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(color: Colors.black.withValues(alpha: 0.08), blurRadius: 16, offset: const Offset(0, -4)),
                ],
                borderRadius: const BorderRadius.vertical(top: Radius.circular(22)),
              ),
              child: SafeArea(
                top: false,
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Estimated fare', style: TextStyle(color: AppTheme.muted, fontSize: 12)),
                          Text(
                            estimatedFare != null ? '₹${estimatedFare!.toStringAsFixed(0)}' : '—',
                            style: const TextStyle(fontSize: 26, fontWeight: FontWeight.w900),
                          ),
                          if (fareType != null)
                            Text(
                              fareType == 'instant' ? 'Instant · today' : 'Scheduled',
                              style: const TextStyle(fontSize: 12, color: AppTheme.muted),
                            ),
                        ],
                      ),
                    ),
                    SizedBox(
                      width: 170,
                      child: FilledButton(
                        onPressed: submitting ? null : _submit,
                        child: submitting
                            ? const SizedBox(
                                height: 22,
                                width: 22,
                                child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.black),
                              )
                            : Text(paymentConfig?.razorpayEnabled == true ? 'Pay & Book' : 'Confirm'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _whiteCard({required Widget child}) => Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: child,
      );
}

class _LocationDropdown extends StatelessWidget {
  const _LocationDropdown({
    required this.label,
    required this.dotColor,
    required this.value,
    required this.items,
    required this.onChanged,
  });

  final String label;
  final Color dotColor;
  final String? value;
  final List<DropdownMenuItem<String>> items;
  final ValueChanged<String?> onChanged;

  @override
  Widget build(BuildContext context) {
    return DropdownButtonFormField<String>(
      key: ValueKey('$label-$value'),
      initialValue: value,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Padding(
          padding: const EdgeInsets.only(left: 12, right: 8),
          child: Container(
            width: 10,
            height: 10,
            margin: const EdgeInsets.symmetric(vertical: 14),
            decoration: BoxDecoration(color: dotColor, shape: BoxShape.circle),
          ),
        ),
        prefixIconConstraints: const BoxConstraints(minWidth: 30),
        border: InputBorder.none,
      ),
      hint: const Text('Select location'),
      isExpanded: true,
      items: items,
      onChanged: onChanged,
    );
  }
}

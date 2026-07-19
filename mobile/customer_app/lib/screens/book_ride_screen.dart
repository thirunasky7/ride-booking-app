import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/customer_api.dart';
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

  String tripType = 'apartment_to_busstand';
  int? apartmentId;
  int? busStandId;
  bool otherApartment = false;
  bool otherBusStand = false;
  DateTime bookingDate = DateTime.now();
  SlotModel? selectedSlot;
  double? estimatedFare;
  String? fareType;
  bool loadingMeta = true;
  bool submitting = false;

  final pickupCtrl = TextEditingController();
  final dropCtrl = TextEditingController();

  bool get isOthers => tripType == 'others';

  @override
  void initState() {
    super.initState();
    _bootstrap();
  }

  @override
  void dispose() {
    pickupCtrl.dispose();
    dropCtrl.dispose();
    super.dispose();
  }

  Future<void> _bootstrap() async {
    try {
      final a = await widget.api.apartments();
      final b = await widget.api.busStands();
      if (!mounted) return;
      setState(() {
        apartments = a;
        busStands = b;
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

  Future<void> _fetchPrice() async {
    if (selectedSlot == null) return;
    final query = <String, String>{
      'booking_date': DateFormat('yyyy-MM-dd').format(bookingDate),
      'slot_time': selectedSlot!.slotTime,
      'time_slot_id': '${selectedSlot!.timeSlotId}',
      'trip_type': tripType,
    };

    if (isOthers) {
      if (pickupCtrl.text.trim().isEmpty || dropCtrl.text.trim().isEmpty) return;
      query['pickup_address'] = pickupCtrl.text.trim();
      query['drop_address'] = dropCtrl.text.trim();
    } else {
      if (otherApartment) {
        if (pickupCtrl.text.trim().isEmpty) return;
        query['pickup_address'] = pickupCtrl.text.trim();
      } else if (apartmentId != null) {
        query['apartment_id'] = '$apartmentId';
      } else {
        return;
      }

      if (otherBusStand) {
        if (dropCtrl.text.trim().isEmpty) return;
        query['drop_address'] = dropCtrl.text.trim();
      } else if (busStandId != null) {
        query['bus_stand_id'] = '$busStandId';
      } else {
        return;
      }
    }

    try {
      final data = await widget.api.calculatePrice(query);
      if (!mounted) return;
      setState(() {
        estimatedFare = parseDouble(data['estimated_fare']);
        fareType = data['booking_type']?.toString();
      });
    } catch (_) {}
  }

  Future<void> _submit() async {
    if (selectedSlot == null) {
      _toast('Select a time slot');
      return;
    }

    final body = <String, dynamic>{
      'trip_type': tripType,
      'booking_date': DateFormat('yyyy-MM-dd').format(bookingDate),
      'slot_time': selectedSlot!.slotTime,
      'time_slot_id': selectedSlot!.timeSlotId,
    };

    if (isOthers) {
      if (pickupCtrl.text.trim().isEmpty || dropCtrl.text.trim().isEmpty) {
        _toast('Enter pickup and drop addresses');
        return;
      }
      body['pickup_address'] = pickupCtrl.text.trim();
      body['drop_address'] = dropCtrl.text.trim();
    } else {
      if (otherApartment) {
        if (pickupCtrl.text.trim().isEmpty) {
          _toast('Enter pickup address');
          return;
        }
        body['pickup_address'] = pickupCtrl.text.trim();
      } else {
        if (apartmentId == null) {
          _toast('Select an apartment');
          return;
        }
        body['apartment_id'] = apartmentId;
      }
      if (otherBusStand) {
        if (dropCtrl.text.trim().isEmpty) {
          _toast('Enter drop address');
          return;
        }
        body['drop_address'] = dropCtrl.text.trim();
      } else {
        if (busStandId == null) {
          _toast('Select a bus stand');
          return;
        }
        body['bus_stand_id'] = busStandId;
      }
    }

    setState(() => submitting = true);
    try {
      await widget.api.createBooking(body);
      if (!mounted) return;
      _toast('Booking created successfully');
      setState(() {
        pickupCtrl.clear();
        dropCtrl.clear();
        estimatedFare = null;
      });
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
                _sectionTitle('Trip type'),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    _TripChip(
                      label: 'Apt → Bus',
                      selected: tripType == 'apartment_to_busstand',
                      onTap: () {
                        setState(() => tripType = 'apartment_to_busstand');
                        _fetchPrice();
                      },
                    ),
                    _TripChip(
                      label: 'Bus → Apt',
                      selected: tripType == 'busstand_to_apartment',
                      onTap: () {
                        setState(() => tripType = 'busstand_to_apartment');
                        _fetchPrice();
                      },
                    ),
                    _TripChip(
                      label: 'Others',
                      selected: tripType == 'others',
                      onTap: () {
                        setState(() {
                          tripType = 'others';
                          otherApartment = false;
                          otherBusStand = false;
                        });
                        _fetchPrice();
                      },
                    ),
                  ],
                ),
                const SizedBox(height: 20),
                _sectionTitle('Route'),
                const SizedBox(height: 10),
                if (!isOthers) ...[
                  _whiteCard(
                    child: Column(
                      children: [
                        DropdownButtonFormField<int?>(
                          key: ValueKey('apt-${otherApartment ? -1 : apartmentId}'),
                          initialValue: otherApartment ? -1 : apartmentId,
                          decoration: const InputDecoration(
                            labelText: 'Pickup · Apartment',
                            border: InputBorder.none,
                            enabledBorder: InputBorder.none,
                            focusedBorder: InputBorder.none,
                          ),
                          items: [
                            ...apartments.map((a) => DropdownMenuItem(value: a.id, child: Text(a.name))),
                            const DropdownMenuItem(value: -1, child: Text('Other address')),
                          ],
                          onChanged: (v) {
                            setState(() {
                              if (v == -1) {
                                otherApartment = true;
                                apartmentId = null;
                              } else {
                                otherApartment = false;
                                apartmentId = v;
                              }
                            });
                            _fetchPrice();
                          },
                        ),
                        if (otherApartment)
                          TextField(
                            controller: pickupCtrl,
                            decoration: const InputDecoration(
                              hintText: 'Enter pickup address',
                              border: InputBorder.none,
                            ),
                            onChanged: (_) => _fetchPrice(),
                          ),
                        const Divider(height: 1),
                        DropdownButtonFormField<int?>(
                          key: ValueKey('bus-${otherBusStand ? -1 : busStandId}'),
                          initialValue: otherBusStand ? -1 : busStandId,
                          decoration: const InputDecoration(
                            labelText: 'Drop · Bus stand',
                            border: InputBorder.none,
                            enabledBorder: InputBorder.none,
                            focusedBorder: InputBorder.none,
                          ),
                          items: [
                            ...busStands.map((b) => DropdownMenuItem(value: b.id, child: Text(b.name))),
                            const DropdownMenuItem(value: -1, child: Text('Other address')),
                          ],
                          onChanged: (v) {
                            setState(() {
                              if (v == -1) {
                                otherBusStand = true;
                                busStandId = null;
                              } else {
                                otherBusStand = false;
                                busStandId = v;
                              }
                            });
                            _fetchPrice();
                          },
                        ),
                        if (otherBusStand)
                          TextField(
                            controller: dropCtrl,
                            decoration: const InputDecoration(
                              hintText: 'Enter drop address',
                              border: InputBorder.none,
                            ),
                            onChanged: (_) => _fetchPrice(),
                          ),
                      ],
                    ),
                  ),
                ] else ...[
                  _whiteCard(
                    child: Column(
                      children: [
                        TextField(
                          controller: pickupCtrl,
                          maxLines: 2,
                          decoration: const InputDecoration(
                            labelText: 'Pickup address',
                            border: InputBorder.none,
                          ),
                          onChanged: (_) => _fetchPrice(),
                        ),
                        const Divider(height: 1),
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
                const SizedBox(height: 20),
                _sectionTitle('When'),
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
                          enabledBorder: InputBorder.none,
                          focusedBorder: InputBorder.none,
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
                      width: 160,
                      child: FilledButton(
                        onPressed: submitting ? null : _submit,
                        child: submitting
                            ? const SizedBox(
                                height: 22,
                                width: 22,
                                child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.black),
                              )
                            : const Text('Confirm'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _sectionTitle(String text) => Text(
        text,
        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
      );

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

class _TripChip extends StatelessWidget {
  const _TripChip({required this.label, required this.selected, required this.onTap});

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(24),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
        decoration: BoxDecoration(
          color: selected ? AppTheme.yellow : Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: selected ? AppTheme.yellowDark : Colors.grey.shade300),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontWeight: FontWeight.w700,
            color: AppTheme.black,
          ),
        ),
      ),
    );
  }
}

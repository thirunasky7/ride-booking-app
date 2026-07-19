import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../models/models.dart';
import '../services/api_client.dart';
import '../services/customer_api.dart';

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
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
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
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
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
        estimatedFare = (data['estimated_fare'] as num?)?.toDouble();
        fareType = data['booking_type'] as String?;
      });
    } catch (_) {
      // Keep previous fare on soft failures while typing
    }
  }

  Future<void> _submit() async {
    if (selectedSlot == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Select a time slot')),
      );
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
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Enter pickup and drop addresses')),
        );
        return;
      }
      body['pickup_address'] = pickupCtrl.text.trim();
      body['drop_address'] = dropCtrl.text.trim();
    } else {
      if (otherApartment) {
        body['pickup_address'] = pickupCtrl.text.trim();
      } else {
        body['apartment_id'] = apartmentId;
      }
      if (otherBusStand) {
        body['drop_address'] = dropCtrl.text.trim();
      } else {
        body['bus_stand_id'] = busStandId;
      }
    }

    setState(() => submitting = true);
    try {
      await widget.api.createBooking(body);
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Booking created successfully')),
      );
      setState(() {
        pickupCtrl.clear();
        dropCtrl.clear();
        estimatedFare = null;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => submitting = false);
    }
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
    if (loadingMeta) {
      return const Center(child: CircularProgressIndicator());
    }

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        DropdownButtonFormField<String>(
          value: tripType,
          decoration: const InputDecoration(labelText: 'Trip type'),
          items: const [
            DropdownMenuItem(value: 'apartment_to_busstand', child: Text('Apartment → Bus Stand')),
            DropdownMenuItem(value: 'busstand_to_apartment', child: Text('Bus Stand → Apartment')),
            DropdownMenuItem(value: 'others', child: Text('Others (custom addresses)')),
          ],
          onChanged: (v) {
            setState(() {
              tripType = v!;
              if (isOthers) {
                otherApartment = false;
                otherBusStand = false;
              }
            });
            _fetchPrice();
          },
        ),
        const SizedBox(height: 12),
        if (!isOthers) ...[
          DropdownButtonFormField<int?>(
            value: otherApartment ? -1 : apartmentId,
            decoration: const InputDecoration(labelText: 'Apartment'),
            items: [
              ...apartments.map((a) => DropdownMenuItem(value: a.id, child: Text(a.name))),
              const DropdownMenuItem(value: -1, child: Text('Other')),
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
          if (otherApartment) ...[
            const SizedBox(height: 12),
            TextField(
              controller: pickupCtrl,
              decoration: const InputDecoration(labelText: 'Pickup address'),
              onChanged: (_) => _fetchPrice(),
            ),
          ],
          const SizedBox(height: 12),
          DropdownButtonFormField<int?>(
            value: otherBusStand ? -1 : busStandId,
            decoration: const InputDecoration(labelText: 'Bus stand'),
            items: [
              ...busStands.map((b) => DropdownMenuItem(value: b.id, child: Text(b.name))),
              const DropdownMenuItem(value: -1, child: Text('Other')),
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
          if (otherBusStand) ...[
            const SizedBox(height: 12),
            TextField(
              controller: dropCtrl,
              decoration: const InputDecoration(labelText: 'Drop address'),
              onChanged: (_) => _fetchPrice(),
            ),
          ],
        ] else ...[
          TextField(
            controller: pickupCtrl,
            maxLines: 2,
            decoration: const InputDecoration(labelText: 'Pickup address'),
            onChanged: (_) => _fetchPrice(),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: dropCtrl,
            maxLines: 2,
            decoration: const InputDecoration(labelText: 'Drop address'),
            onChanged: (_) => _fetchPrice(),
          ),
        ],
        const SizedBox(height: 12),
        ListTile(
          contentPadding: EdgeInsets.zero,
          title: const Text('Booking date'),
          subtitle: Text(DateFormat('EEE, dd MMM yyyy').format(bookingDate)),
          trailing: const Icon(Icons.calendar_today),
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
        DropdownButtonFormField<SlotModel>(
          value: selectedSlot,
          decoration: const InputDecoration(labelText: 'Time slot'),
          items: slots
              .map((s) => DropdownMenuItem(value: s, child: Text(_formatSlot(s.slotTime))))
              .toList(),
          onChanged: (v) {
            setState(() => selectedSlot = v);
            _fetchPrice();
          },
        ),
        const SizedBox(height: 16),
        if (estimatedFare != null)
          Card(
            color: const Color(0xFFCCFBF1),
            child: ListTile(
              leading: const Icon(Icons.currency_rupee, color: Color(0xFF0F766E)),
              title: Text(
                '₹${estimatedFare!.toStringAsFixed(0)}',
                style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Color(0xFF0F766E)),
              ),
              subtitle: Text(fareType == 'instant' ? 'Instant booking (today)' : 'Scheduled booking'),
            ),
          ),
        const SizedBox(height: 16),
        FilledButton(
          onPressed: submitting ? null : _submit,
          child: submitting
              ? const SizedBox(height: 22, width: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : const Text('Confirm Booking'),
        ),
      ],
    );
  }
}

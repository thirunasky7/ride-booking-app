import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/models.dart';
import '../providers/auth_provider.dart';
import '../services/api_client.dart';
import '../services/driver_api.dart';
import 'earnings_screen.dart';
import 'login_screen.dart';
import 'trips_screen.dart';

class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _index = 0;
  late final DriverApi _api;

  @override
  void initState() {
    super.initState();
    _api = DriverApi(context.read<ApiClient>());
  }

  @override
  Widget build(BuildContext context) {
    final driver = context.watch<AuthProvider>().driver;
    final pages = [
      _DashboardTab(api: _api),
      TripsScreen(api: _api),
      EarningsScreen(api: _api),
    ];

    return Scaffold(
      appBar: AppBar(
        title: Text(driver?.name.isNotEmpty == true ? driver!.name : 'Driver'),
        actions: [
          IconButton(
            tooltip: 'Logout',
            onPressed: () async {
              await context.read<AuthProvider>().logout();
              if (!context.mounted) return;
              Navigator.of(context).pushAndRemoveUntil(
                MaterialPageRoute(builder: (_) => const LoginScreen()),
                (_) => false,
              );
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      body: pages[_index],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.dashboard_outlined), selectedIcon: Icon(Icons.dashboard), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.route_outlined), selectedIcon: Icon(Icons.route), label: 'Trips'),
          NavigationDestination(icon: Icon(Icons.payments_outlined), selectedIcon: Icon(Icons.payments), label: 'Earnings'),
        ],
      ),
    );
  }
}

class _DashboardTab extends StatefulWidget {
  const _DashboardTab({required this.api});

  final DriverApi api;

  @override
  State<_DashboardTab> createState() => _DashboardTabState();
}

class _DashboardTabState extends State<_DashboardTab> {
  int todayTrips = 0;
  int completedTrips = 0;
  double totalEarnings = 0;
  bool loading = true;
  bool toggling = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final data = await widget.api.dashboard();
      if (!mounted) return;
      setState(() {
        todayTrips = parseInt(data['today_trips']) ?? 0;
        completedTrips = parseInt(data['completed_trips']) ?? 0;
        totalEarnings = parseDouble(data['total_earnings']) ?? 0;
        loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => loading = false);
      if (e.statusCode == 401) {
        await context.read<AuthProvider>().logout();
        if (!mounted) return;
        Navigator.of(context).pushAndRemoveUntil(
          MaterialPageRoute(builder: (_) => const LoginScreen()),
          (_) => false,
        );
        return;
      }
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }

  Future<void> _toggleOnline(bool value) async {
    setState(() => toggling = true);
    try {
      final online = await widget.api.toggleOnline(value);
      if (!mounted) return;
      context.read<AuthProvider>().updateOnline(online);
    } on ApiException catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    } finally {
      if (mounted) setState(() => toggling = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final driver = context.watch<AuthProvider>().driver;
    final online = driver?.isOnline ?? false;

    if (loading) return const Center(child: CircularProgressIndicator());

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Card(
            child: SwitchListTile(
              title: Text(online ? 'You are online' : 'You are offline'),
              subtitle: Text(online ? 'Receiving trip assignments' : 'Go online to start receiving trips'),
              value: online,
              onChanged: toggling ? null : _toggleOnline,
              secondary: Icon(
                online ? Icons.wifi : Icons.wifi_off,
                color: online ? Colors.green : Colors.grey,
              ),
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(child: _StatCard(label: "Today's trips", value: '$todayTrips', icon: Icons.today)),
              const SizedBox(width: 12),
              Expanded(child: _StatCard(label: 'Completed', value: '$completedTrips', icon: Icons.check_circle_outline)),
            ],
          ),
          const SizedBox(height: 12),
          _StatCard(
            label: 'Total earnings',
            value: '₹${totalEarnings.toStringAsFixed(0)}',
            icon: Icons.currency_rupee,
            wide: true,
          ),
        ],
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  const _StatCard({
    required this.label,
    required this.value,
    required this.icon,
    this.wide = false,
  });

  final String label;
  final String value;
  final IconData icon;
  final bool wide;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            CircleAvatar(
              backgroundColor: const Color(0xFFDBEAFE),
              child: Icon(icon, color: const Color(0xFF1D4ED8)),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(label, style: TextStyle(color: Colors.grey.shade600)),
                  Text(value, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

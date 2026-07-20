import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/models.dart';
import '../providers/auth_provider.dart';
import '../services/api_client.dart';
import '../services/driver_api.dart';
import '../theme/app_theme.dart';
import 'earnings_screen.dart';
import 'login_screen.dart';
import 'profile_screen.dart';
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
    final pages = [
      _DashboardTab(api: _api, onProfile: () => setState(() => _index = 3)),
      TripsScreen(api: _api),
      EarningsScreen(api: _api),
      const ProfileScreen(),
    ];

    return Scaffold(
      backgroundColor: AppTheme.bg,
      body: pages[_index],
      bottomNavigationBar: NavigationBar(
        selectedIndex: _index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home_rounded),
            label: 'Home',
          ),
          NavigationDestination(
            icon: Icon(Icons.route_outlined),
            selectedIcon: Icon(Icons.route_rounded),
            label: 'Trips',
          ),
          NavigationDestination(
            icon: Icon(Icons.account_balance_wallet_outlined),
            selectedIcon: Icon(Icons.account_balance_wallet_rounded),
            label: 'Earnings',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person_rounded),
            label: 'Profile',
          ),
        ],
      ),
    );
  }
}

class _DashboardTab extends StatefulWidget {
  const _DashboardTab({required this.api, required this.onProfile});

  final DriverApi api;
  final VoidCallback onProfile;

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
    final name = (driver?.name.isNotEmpty == true) ? driver!.name : 'Partner';

    if (loading) {
      return const Scaffold(
        backgroundColor: AppTheme.bg,
        body: Center(child: CircularProgressIndicator(color: AppTheme.yellow)),
      );
    }

    return RefreshIndicator(
      color: AppTheme.black,
      onRefresh: _load,
      child: CustomScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        slivers: [
          SliverToBoxAdapter(
            child: Container(
              padding: EdgeInsets.fromLTRB(20, MediaQuery.of(context).padding.top + 16, 20, 28),
              decoration: const BoxDecoration(
                color: AppTheme.black,
                borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Hi, $name',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 22,
                                fontWeight: FontWeight.w900,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              online ? 'You are online · ready for trips' : 'You are offline',
                              style: TextStyle(color: Colors.white.withValues(alpha: 0.65)),
                            ),
                          ],
                        ),
                      ),
                      IconButton(
                        onPressed: widget.onProfile,
                        icon: const Icon(Icons.person_outline_rounded, color: Colors.white),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: AppTheme.charcoal,
                      borderRadius: BorderRadius.circular(18),
                      border: Border.all(
                        color: online ? AppTheme.online.withValues(alpha: 0.5) : Colors.white12,
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 12,
                          height: 12,
                          decoration: BoxDecoration(
                            color: online ? AppTheme.online : Colors.grey,
                            shape: BoxShape.circle,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            online ? 'Accepting trips' : 'Go online to get trips',
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600),
                          ),
                        ),
                        Switch.adaptive(
                          value: online,
                          activeThumbColor: AppTheme.black,
                          activeTrackColor: AppTheme.yellow,
                          onChanged: toggling ? null : _toggleOnline,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          SliverPadding(
            padding: const EdgeInsets.all(20),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFFFE082), AppTheme.yellow],
                    ),
                    borderRadius: BorderRadius.circular(22),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Total earnings',
                              style: TextStyle(fontWeight: FontWeight.w600, color: AppTheme.black),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              '₹${totalEarnings.toStringAsFixed(0)}',
                              style: const TextStyle(
                                fontSize: 32,
                                fontWeight: FontWeight.w900,
                                color: AppTheme.black,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: AppTheme.black,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: const Icon(Icons.payments_rounded, color: AppTheme.yellow, size: 28),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: _MetricCard(
                        label: "Today's trips",
                        value: '$todayTrips',
                        icon: Icons.today_rounded,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _MetricCard(
                        label: 'Completed',
                        value: '$completedTrips',
                        icon: Icons.check_circle_outline_rounded,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                const Text(
                  'Quick tip',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 10),
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(18),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: const Text(
                    'Stay online during peak slots to get more shuttle assignments. Start trips on time and mark them complete after drop-off.',
                    style: TextStyle(color: AppTheme.muted, height: 1.4),
                  ),
                ),
              ]),
            ),
          ),
        ],
      ),
    );
  }
}

class _MetricCard extends StatelessWidget {
  const _MetricCard({
    required this.label,
    required this.value,
    required this.icon,
  });

  final String label;
  final String value;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
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
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: AppTheme.yellow.withValues(alpha: 0.35),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: AppTheme.black),
          ),
          const SizedBox(height: 14),
          Text(label, style: const TextStyle(color: AppTheme.muted, fontSize: 13)),
          const SizedBox(height: 4),
          Text(value, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
        ],
      ),
    );
  }
}

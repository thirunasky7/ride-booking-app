@extends('layouts.marketing')
@section('title', 'Services')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4"><h1 class="fw-bold mb-0">Our Services</h1></div>
    <div class="row g-4">
        <div class="col-md-6"><div class="card-modern p-4"><h5><i class="bi bi-arrow-left-right text-success me-2"></i>Apartment ↔ Bus Stand</h5><p class="text-muted mb-0">Two-way shuttle service with scheduled time slots.</p></div></div>
        <div class="col-md-6"><div class="card-modern p-4"><h5><i class="bi bi-phone text-success me-2"></i>OTP Login</h5><p class="text-muted mb-0">Passwordless mobile login for customers and drivers.</p></div></div>
        <div class="col-md-6"><div class="card-modern p-4"><h5><i class="bi bi-graph-up text-success me-2"></i>Admin Analytics</h5><p class="text-muted mb-0">Earnings, commission reports, and booking calendar.</p></div></div>
        <div class="col-md-6"><div class="card-modern p-4"><h5><i class="bi bi-app text-success me-2"></i>Mobile API</h5><p class="text-muted mb-0">Full REST API with Sanctum for iOS and Android apps.</p></div></div>
    </div>
</div>
@endsection

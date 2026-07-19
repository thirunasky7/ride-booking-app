@extends('layouts.marketing')
@section('title', 'Contact')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4"><h1 class="fw-bold mb-0">Contact Us</h1></div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card-modern p-4">
                <h5>Get in Touch</h5>
                <p class="text-muted"><i class="bi bi-envelope me-2"></i>support@apartmentshuttle.com</p>
                <p class="text-muted"><i class="bi bi-telephone me-2"></i>+91 1800-SHUTTLE</p>
                <p class="text-muted mb-0"><i class="bi bi-geo-alt me-2"></i>City Operations Center</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-modern p-4">
                <form>
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Message</label><textarea class="form-control" rows="4"></textarea></div>
                    <button type="button" class="btn btn-brand">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

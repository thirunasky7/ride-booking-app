@extends('website.layout')

@section('title', 'Login')

@section('content')

@php $otpSent = session('otp_sent'); @endphp

<div class="login-scene position-relative">
    <div class="login-bg" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
        <span class="orb orb-4"></span>
        <span class="floating-icon fi-1"><i class="bi bi-bus-front"></i></span>
        <span class="floating-icon fi-2"><i class="bi bi-geo-alt"></i></span>
        <span class="floating-icon fi-3"><i class="bi bi-signpost-split"></i></span>
    </div>

    <div class="row justify-content-center position-relative">
        <div class="col-md-5 col-lg-4">

            <div class="text-center mb-4 login-header">
                <div class="login-icon-wrap mx-auto mb-3">
                    <span class="icon-ring ring-1"></span>
                    <span class="icon-ring ring-2"></span>
                    <div class="brand-icon login-icon">
                        <i class="bi bi-phone-vibrate"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 login-title" style="color: var(--brand-text);">Customer Login</h3>
                <p class="text-muted small login-subtitle">Sign in with your mobile number</p>
            </div>

            <div class="login-steps d-flex justify-content-center gap-2 mb-4">
                <div class="login-step {{ $otpSent ? 'done' : 'active' }}" data-step="1">
                    <span class="step-dot">1</span>
                    <span class="step-label">Mobile</span>
                </div>
                <div class="step-line {{ $otpSent ? 'filled' : '' }}"></div>
                <div class="login-step {{ $otpSent ? 'active' : '' }}" data-step="2">
                    <span class="step-dot">2</span>
                    <span class="step-label">Verify</span>
                </div>
            </div>

            <div class="card-modern login-card p-4">

                @if(!$otpSent)
                <div class="login-panel panel-visible" id="mobilePanel">
                    <form action="{{ route('customer.sendOtp') }}" method="POST" id="sendOtpForm">
                        @csrf

                        <div class="mb-3 login-field">
                            <label class="form-label fw-medium small text-muted">Mobile Number</label>
                            <div class="input-group login-input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-telephone input-icon"></i>
                                </span>
                                <input type="text"
                                       name="mobile"
                                       id="mobileInput"
                                       class="form-control border-start-0 @error('mobile') is-invalid @enderror"
                                       placeholder="9876543210"
                                       maxlength="10"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       value="{{ old('mobile', session('mobile')) }}"
                                       autofocus>
                            </div>
                            @error('mobile')
                                <div class="text-danger small mt-1 shake-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-brand w-100 login-btn" id="sendOtpBtn">
                            <span class="btn-text"><i class="bi bi-send me-1"></i> Send OTP</span>
                            <span class="btn-loader d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Sending…
                            </span>
                        </button>
                    </form>
                </div>
                @else
                <form action="{{ route('customer.sendOtp') }}" method="POST" id="sendOtpForm" class="d-none">
                    @csrf
                    <input type="hidden" name="mobile" value="{{ session('mobile') }}">
                </form>

                <div class="login-panel panel-visible panel-slide-in" id="otpPanel">
            <div class="alert login-success-alert border-0 py-2 px-3 small mb-4">
                <i class="bi bi-check-circle-fill me-1"></i>
                OTP sent to <strong>{{ session('mobile') }}</strong>
            </div>

            <form action="{{ route('customer.verifyOtp') }}" method="POST" id="verifyOtpForm">
                @csrf
                <input type="hidden" name="mobile" value="{{ session('mobile') }}">
                        <input type="hidden" name="otp" id="otpHidden" value="">

                        <div class="mb-4 login-field">
                            <label class="form-label fw-medium small text-muted text-center d-block">Enter 4-digit OTP</label>
                            <div class="otp-boxes" id="otpBoxes">
                                @for($i = 0; $i < 4; $i++)
                                <input type="text"
                                       class="otp-digit @error('otp') is-invalid @enderror"
                                       maxlength="1"
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       data-index="{{ $i }}"
                                       autocomplete="one-time-code"
                                       {{ $i === 0 ? 'autofocus' : '' }}>
                                @endfor
                            </div>
                            @error('otp')
                                <div class="text-danger small mt-2 text-center shake-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-brand w-100 login-btn mb-3" id="verifyOtpBtn">
                            <span class="btn-text"><i class="bi bi-shield-check me-1"></i> Verify &amp; Login</span>
                            <span class="btn-loader d-none">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Verifying…
                            </span>
                        </button>

                        <button type="button" class="btn btn-link w-100 small text-decoration-none resend-link" id="resendBtn" disabled>
                            Resend OTP in <span id="resendTimer">30</span>s
                        </button>

                        <a href="{{ route('customer.login') }}" class="btn btn-link w-100 small text-decoration-none mt-1" style="color: var(--brand-muted);">
                            <i class="bi bi-arrow-left me-1"></i> Change number
                        </a>
                    </form>
                </div>
                @endif

            </div>

            <p class="text-center text-muted small mt-3 login-footer-note">
                By continuing, you agree to our shuttle service terms.
            </p>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .login-scene {
        min-height: calc(100vh - 220px);
        display: flex;
        align-items: center;
        padding: 1rem 0 2rem;
        overflow: hidden;
    }

    .login-bg {
        position: absolute;
        inset: -40px;
        pointer-events: none;
        z-index: 0;
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.45;
        animation: orbFloat 12s ease-in-out infinite;
    }

    .orb-1 { width: 280px; height: 280px; background: #14b8a6; top: -5%; left: -8%; animation-delay: 0s; }
    .orb-2 { width: 220px; height: 220px; background: #0f766e; bottom: 10%; right: -5%; animation-delay: -3s; }
    .orb-3 { width: 160px; height: 160px; background: #f59e0b; top: 40%; right: 15%; animation-delay: -6s; opacity: 0.25; }
    .orb-4 { width: 120px; height: 120px; background: #5eead4; bottom: 25%; left: 10%; animation-delay: -9s; opacity: 0.35; }

    @keyframes orbFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33%       { transform: translate(24px, -18px) scale(1.06); }
        66%       { transform: translate(-16px, 14px) scale(0.96); }
    }

    .floating-icon {
        position: absolute;
        color: var(--brand-primary);
        opacity: 0.12;
        font-size: 2rem;
        animation: iconDrift 10s ease-in-out infinite;
    }

    .fi-1 { top: 12%; right: 18%; animation-delay: 0s; }
    .fi-2 { bottom: 20%; left: 12%; animation-delay: -3.5s; font-size: 1.6rem; }
    .fi-3 { top: 55%; left: 20%; animation-delay: -7s; font-size: 1.4rem; }

    @keyframes iconDrift {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50%       { transform: translateY(-14px) rotate(6deg); }
    }

    .login-scene > .row { z-index: 1; width: 100%; }

    /* Header entrance */
    .login-icon-wrap {
        position: relative;
        width: 72px;
        height: 72px;
    }

    .login-icon {
        width: 72px !important;
        height: 72px !important;
        font-size: 1.75rem !important;
        position: relative;
        z-index: 2;
        animation: iconPop 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    .icon-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid var(--brand-primary);
        opacity: 0;
        animation: ringPulse 2.4s ease-out infinite;
    }

    .ring-2 { animation-delay: 1.2s; }

    @keyframes iconPop {
        from { opacity: 0; transform: scale(0.5) rotate(-20deg); }
        to   { opacity: 1; transform: scale(1) rotate(0); }
    }

    @keyframes ringPulse {
        0%   { transform: scale(0.85); opacity: 0.6; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    .login-title  { animation: fadeUp 0.6s 0.15s ease both; }
    .login-subtitle { animation: fadeUp 0.6s 0.25s ease both; }
    .login-steps  { animation: fadeUp 0.6s 0.35s ease both; }
    .login-card   { animation: cardRise 0.7s 0.4s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .login-footer-note { animation: fadeUp 0.6s 0.55s ease both; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes cardRise {
        from { opacity: 0; transform: translateY(28px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Step indicator */
    .login-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        opacity: 0.4;
        transition: opacity 0.4s, transform 0.4s;
    }

    .login-step.active { opacity: 1; }
    .login-step.done   { opacity: 0.7; }

    .step-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .login-step.active .step-dot {
        background: linear-gradient(135deg, var(--brand-primary), #14b8a6);
        color: #fff;
        box-shadow: 0 4px 14px rgba(15, 118, 110, 0.35);
        transform: scale(1.1);
    }

    .login-step.done .step-dot {
        background: #ecfdf5;
        color: var(--brand-primary);
    }

    .login-step.done .step-dot::after { content: '✓'; }
    .login-step.done .step-dot { font-size: 0; }
    .login-step.done .step-dot::after { font-size: 0.85rem; }

    .step-label { font-size: 0.7rem; font-weight: 600; color: var(--brand-muted); }
    .step-line  { width: 48px; height: 2px; background: #e2e8f0; align-self: center; margin-bottom: 18px; position: relative; overflow: hidden; }
    .step-line::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, var(--brand-primary), #14b8a6);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.6s ease;
    }
    .step-line.filled::after { transform: scaleX(1); }

    /* Card & inputs */
    .login-card {
        backdrop-filter: blur(8px);
        background: rgba(255, 255, 255, 0.92) !important;
    }

    .login-input-group {
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid var(--brand-border);
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .login-input-group:focus-within {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
    }

    .login-input-group .input-group-text,
    .login-input-group .form-control {
        border: none !important;
        background: #fff;
    }

    .input-icon { transition: transform 0.3s, color 0.3s; color: #94a3b8; }
    .login-input-group:focus-within .input-icon {
        color: var(--brand-primary);
        transform: rotate(-8deg) scale(1.1);
    }

    .login-btn {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .login-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%);
        transition: none;
    }

    .login-btn:hover::after {
        animation: btnShine 0.6s ease;
    }

    @keyframes btnShine {
        to { transform: translateX(100%); }
    }

    .login-btn:active { transform: scale(0.98); }

    /* OTP panel slide-in */
    .panel-slide-in {
        animation: panelSlide 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes panelSlide {
        from { opacity: 0; transform: translateY(16px); max-height: 0; }
        to   { opacity: 1; transform: translateY(0); max-height: 400px; }
    }

    .login-success-alert {
        background: #ecfdf5;
        color: #047857;
        animation: successPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    @keyframes successPop {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* OTP digit boxes */
    .otp-boxes {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .otp-digit {
        width: 56px;
        height: 64px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        border: 2px solid var(--brand-border);
        border-radius: 12px;
        background: #fff;
        color: var(--brand-text);
        outline: none;
        transition: border-color 0.25s, box-shadow 0.25s, transform 0.2s;
        animation: digitAppear 0.4s ease both;
    }

    .otp-digit:nth-child(1) { animation-delay: 0.1s; }
    .otp-digit:nth-child(2) { animation-delay: 0.2s; }
    .otp-digit:nth-child(3) { animation-delay: 0.3s; }
    .otp-digit:nth-child(4) { animation-delay: 0.4s; }

    @keyframes digitAppear {
        from { opacity: 0; transform: translateY(10px) scale(0.8); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .otp-digit:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
        transform: scale(1.05);
    }

    .otp-digit.filled {
        border-color: var(--brand-primary);
        background: var(--brand-bg);
    }

    .shake-error { animation: shake 0.45s ease; }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }

    .resend-link { color: var(--brand-muted) !important; }
    .resend-link:not(:disabled) { color: var(--brand-primary) !important; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    // Button loading states
    document.querySelectorAll('.login-btn').forEach(function (btn) {
        btn.closest('form')?.addEventListener('submit', function () {
            btn.querySelector('.btn-text')?.classList.add('d-none');
            btn.querySelector('.btn-loader')?.classList.remove('d-none');
            btn.disabled = true;
        });
    });

    // Mobile: digits only
    var mobileInput = document.getElementById('mobileInput');
    if (mobileInput) {
        mobileInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }

    // OTP digit boxes
    var digits = document.querySelectorAll('.otp-digit');
    var hidden  = document.getElementById('otpHidden');
    if (!digits.length || !hidden) return;

    function syncOtp() {
        hidden.value = Array.from(digits).map(function (d) { return d.value; }).join('');
    }

    digits.forEach(function (digit, idx) {
        digit.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(-1);
            this.classList.toggle('filled', this.value !== '');
            syncOtp();
            if (this.value && idx < digits.length - 1) {
                digits[idx + 1].focus();
            }
        });

        digit.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                digits[idx - 1].focus();
                digits[idx - 1].value = '';
                digits[idx - 1].classList.remove('filled');
                syncOtp();
            }
        });

        digit.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 4);
            pasted.split('').forEach(function (ch, i) {
                if (digits[i]) {
                    digits[i].value = ch;
                    digits[i].classList.add('filled');
                }
            });
            syncOtp();
            var focusIdx = Math.min(pasted.length, digits.length - 1);
            digits[focusIdx].focus();
        });
    });

    // Resend countdown
    var resendBtn   = document.getElementById('resendBtn');
    var resendTimer = document.getElementById('resendTimer');
    if (resendBtn && resendTimer) {
        var seconds = 30;
        var interval = setInterval(function () {
            seconds--;
            resendTimer.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Resend OTP';
                resendBtn.addEventListener('click', function () {
                    document.getElementById('sendOtpForm').submit();
                });
            }
        }, 1000);
    }
})();
</script>
@endpush

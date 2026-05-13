@extends('website.layout')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-5">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <h3 class="mb-4 text-center">

                    Customer Login

                </h3>

                <form action=
"{{ route('customer.sendOtp') }}"
                      method="POST">

                    @csrf

                    <div class="mb-3">

                        <label>
                            Mobile Number
                        </label>

                        <input type="text"
                               name="mobile"
                               class="form-control"
                               placeholder="9876543210">

                    </div>

                    <button class="btn btn-primary w-100">

                        Send OTP

                    </button>

                </form>

                @if(session('otp_sent'))

                <hr>

                <div class="alert alert-success">

                    OTP Sent:
                    <strong>
                        {{ session('otp') }}
                    </strong>

                </div>

                <form action=
"{{ route('customer.verifyOtp') }}"
                      method="POST">

                    @csrf

                    <div class="mb-3">

                        <label>
                            Enter OTP
                        </label>

                        <input type="text"
                               name="otp"
                               class="form-control">

                    </div>

                    <button class="btn btn-success w-100">

                        Verify OTP

                    </button>

                </form>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection
@extends('layouts.auth')

@section('title', 'Sign In - Wedding Invitation Dashboard')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .login-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }

    .form-control:focus {
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }

    .wedding-bg {
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff10" points="0,1000 1000,0 1000,1000"/><polygon fill="%23ffffff05" points="0,800 1000,200 1000,1000 0,1000"/></svg>');
        background-size: cover;
        background-repeat: no-repeat;
    }
</style>
@endpush

@section('content')
<section class="min-vh-100 wedding-bg">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-4 col-lg-5 col-md-7">

                <!-- Login Card -->
                <div class="card login-card border-0 shadow-lg">

                    <!-- Header -->
                    <div class="card-header bg-transparent border-0 pb-0 text-center">
                        <div class="text-center mb-4">
                            <div class="icon icon-shape icon-lg bg-gradient-primary shadow mx-auto mb-3">
                                <i class="ni ni-favourite-28 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <h3 class="font-weight-bolder text-primary">Welcome Back!</h3>
                            <p class="text-muted">Sign in to your wedding dashboard</p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body pt-0">

                        <!-- Error/Success Messages -->
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-icon align-middle">
                                <span class="material-icons text-md">error</span>
                            </span>
                            <span class="alert-text">
                                @foreach($errors->all() as $error)
                                <strong>{{ $error }}</strong><br>
                                @endforeach
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-icon align-middle">
                                <span class="material-icons text-md">error</span>
                            </span>
                            <span class="alert-text"><strong>{{ session('error') }}</strong></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span class="alert-icon align-middle">
                                <span class="material-icons text-md">check</span>
                            </span>
                            <span class="alert-text"><strong>{{ session('success') }}</strong></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <!-- Login Form -->
                        <form role="form" method="POST" action="{{ route('login') }}" class="text-start">
                            @csrf

                            <!-- Email Field -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-dark">Email Address</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="ni ni-email-83"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Enter your email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" autofocus>
                                </div>
                                @error('email')
                                <div class="text-danger text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-dark">Password</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="ni ni-lock-circle-open"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter your password" name="password" required
                                        autocomplete="current-password" id="password">
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword()">
                                        <i class="ni ni-eye-17" id="toggleIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                <div class="text-danger text-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check form-switch d-flex align-items-center mb-3">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                <label class="form-check-label mb-0 ms-2" for="rememberMe">Remember me</label>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">
                                    <i class="ni ni-button-power me-2"></i>
                                    Sign In
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-center pt-0 px-lg-2 px-1 bg-transparent border-0">
                        <small class="text-muted">
                            Need help?
                            <a href="mailto:admin@wedding.com" class="text-primary font-weight-bold">Contact Support</a>
                        </small>

                        <div class="mt-3">
                            <small class="text-muted">
                                © {{ date('Y') }} Wedding Invitation System. Made with ❤️
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Features Card -->
                <div class="row mt-3">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-send text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <h6 class="text-white mt-2 mb-0">Send Invites</h6>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <h6 class="text-white mt-2 mb-0">Analytics</h6>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                            <h6 class="text-white mt-2 mb-0">Manage Guests</h6>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.className = 'ni ni-eye-off';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'ni ni-eye-17';
    }
}

// Auto-focus email field if empty
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput && !emailInput.value) {
        emailInput.focus();
    }
});

// Add loading state to submit button
document.querySelector('form').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="ni ni-settings-gear-65 me-2"></i>Signing In...';
    submitBtn.disabled = true;

    // Re-enable if form submission fails (e.g., validation errors)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Enhanced form validation
document.querySelectorAll('.form-control').forEach(function(input) {
    input.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });

    input.addEventListener('input', function() {
        this.classList.remove('is-invalid', 'is-valid');
    });
});
</script>
@endpush
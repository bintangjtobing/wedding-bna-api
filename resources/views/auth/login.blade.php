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

    /* Icon styling */
    .icon-shape {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control {
        border-left: none;
    }

    .form-control:focus {
        border-left: none;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .cursor-pointer:hover {
        background-color: #e9ecef;
    }

    /* Feature icons */
    .feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    /* Loading animation */
    .fa-spin {
        animation: fa-spin 2s infinite linear;
    }

    @keyframes fa-spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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
                            <div class="icon-shape bg-gradient-primary shadow mx-auto mb-3">
                                <i class="fas fa-heart text-white text-lg"></i>
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
                                <i class="fas fa-exclamation-circle text-md"></i>
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
                                <i class="fas fa-exclamation-triangle text-md"></i>
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
                                <i class="fas fa-check-circle text-md"></i>
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
                                <label class="form-label font-weight-bold text-dark">
                                    <i class="fas fa-envelope me-1"></i>Email Address
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Enter your email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" autofocus>
                                </div>
                                @error('email')
                                <div class="text-danger text-sm">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-dark">
                                    <i class="fas fa-lock me-1"></i>Password
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter your password" name="password" required
                                        autocomplete="current-password" id="password">
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                <div class="text-danger text-sm">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check form-switch d-flex align-items-center mb-3">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                <label class="form-check-label mb-0 ms-2" for="rememberMe">
                                    <i class="fas fa-memory me-1"></i>Remember me
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Sign In
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-center pt-0 px-lg-2 px-1 bg-transparent border-0">
                        <small class="text-muted">
                            <i class="fas fa-question-circle me-1"></i>Need help?
                            <a href="mailto:admin@wedding.com" class="text-primary font-weight-bold">
                                <i class="fas fa-envelope me-1"></i>Contact Support
                            </a>
                        </small>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-copyright me-1"></i>{{ date('Y') }} Wedding Invitation System. Made
                                with
                                <i class="fas fa-heart text-danger"></i>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Features Card -->
                <div class="row mt-3">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="feature-icon bg-gradient-primary shadow text-center">
                                <i class="fas fa-paper-plane text-white"></i>
                            </div>
                            <h6 class="text-white mt-2 mb-0">
                                <i class="fas fa-envelope me-1"></i>Send Invites
                            </h6>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="feature-icon bg-gradient-info shadow text-center">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <h6 class="text-white mt-2 mb-0">
                                <i class="fas fa-analytics me-1"></i>Analytics
                            </h6>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="feature-icon bg-gradient-success shadow text-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <h6 class="text-white mt-2 mb-0">
                                <i class="fas fa-user-friends me-1"></i>Manage Guests
                            </h6>
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
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'fas fa-eye';
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

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
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

// Add icon animations on focus
document.querySelectorAll('.form-control').forEach(function(input) {
    input.addEventListener('focus', function() {
        const icon = this.parentElement.querySelector('.input-group-text i');
        if (icon) {
            icon.style.transform = 'scale(1.1)';
            icon.style.transition = 'transform 0.2s ease';
        }
    });

    input.addEventListener('blur', function() {
        const icon = this.parentElement.querySelector('.input-group-text i');
        if (icon) {
            icon.style.transform = 'scale(1)';
        }
    });
});

// Enhanced error handling with icons
document.addEventListener('DOMContentLoaded', function() {
    // Add shake animation to error alerts
    const errorAlerts = document.querySelectorAll('.alert-danger');
    errorAlerts.forEach(function(alert) {
        alert.style.animation = 'shake 0.5s ease-in-out';
    });

    // Add success pulse animation
    const successAlerts = document.querySelectorAll('.alert-success');
    successAlerts.forEach(function(alert) {
        alert.style.animation = 'pulse 0.5s ease-in-out';
    });
});

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    .feature-icon:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }
`;
document.head.appendChild(style);
</script>
@endpush
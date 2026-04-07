@extends('layouts.guest')
@section('title', 'Login')

@section('nav_action')
  <a href="{{ route('register') }}" class="nav-btn">Sign Up</a>
@endsection

@section('form')
  <h1 class="auth-title">Login</h1>
  <p class="auth-sub">Use your email to continue with us</p>

  <form action="{{ route('login.post') }}" method="POST">
    @csrf

    <div class="form-group">
      <label class="form-label">Email address</label>
      <input type="email" name="email" class="form-control"
        value="{{ old('email') }}"
        placeholder="email@example.com"
        autocomplete="email" required />
      @error('email')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label" style="display:flex;justify-content:space-between;">
        <span>Password</span>
        <a href="#" style="color:var(--blue);font-size:.78rem;">Forgot Password?</a>
      </label>
      <div class="input-wrapper">
        <input type="password" name="password" id="passwordField" class="form-control"
          placeholder="••••••••" autocomplete="current-password" required />
        <span class="input-toggle" onclick="togglePw()">
          <i class="ri-eye-off-line" id="pwIcon"></i> Hide
        </span>
      </div>
      @error('password')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    {{-- Mock reCAPTCHA --}}
    <div class="recaptcha-mock">
      <div class="check">
        <div class="recaptcha-check"><i class="ri-check-line"></i></div>
        I'm not a robot
      </div>
      <div class="recaptcha-logo">reCAPTCHA<br>Privacy - Terms</div>
    </div>

    <button type="submit" class="auth-btn">Login</button>
  </form>

  <div class="auth-footer-link">
    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
  </div>
@endsection

@push('scripts')
<script>
function togglePw() {
  const field = document.getElementById('passwordField');
  const icon  = document.getElementById('pwIcon');
  if (field.type === 'password') {
    field.type = 'text';
    icon.className = 'ri-eye-line';
  } else {
    field.type = 'password';
    icon.className = 'ri-eye-off-line';
  }
}
</script>
@endpush

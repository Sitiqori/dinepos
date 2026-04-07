@extends('layouts.guest')
@section('title', 'Register')

@section('nav_action')
  <a href="{{ route('login') }}" class="nav-btn" style="background:#fff;color:var(--navy);border:1.5px solid var(--border);">Login</a>
@endsection

@section('form')
  <h1 class="auth-title">Sign up</h1>
  <p class="auth-sub">Sign up to access</p>

  <form action="{{ route('register.post') }}" method="POST">
    @csrf

    <div class="form-group">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control"
        value="{{ old('name') }}"
        placeholder="Nama lengkap"
        autocomplete="name" required />
      @error('name')<div class="form-error">{{ $message }}</div>@enderror
    </div>

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
      </label>
      <div class="input-wrapper">
        <input type="password" name="password" id="passwordField" class="form-control"
          placeholder="Min. 8 karakter" autocomplete="new-password" required />
        <span class="input-toggle" onclick="togglePw()">
          <i class="ri-eye-off-line" id="pwIcon"></i> Hide
        </span>
      </div>
      <div class="form-hint">Use 8 or more characters with a mix of letters, numbers &amp; symbols</div>
      @error('password')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group" style="margin-bottom:14px;">
      <label class="form-label">Konfirmasi Password</label>
      <input type="password" name="password_confirmation" class="form-control"
        placeholder="Ulangi password" required />
    </div>

    <div class="checkbox-row">
      <input type="checkbox" id="agreeTerms" required />
      <label for="agreeTerms">
        Agree to our <a href="#">Terms of use</a> and <a href="#">Privacy Policy</a>
      </label>
    </div>

    <div class="checkbox-row">
      <input type="checkbox" id="newsletter" checked />
      <label for="newsletter">Subscribe to our monthly newsletter</label>
    </div>

    {{-- Mock reCAPTCHA --}}
    <div class="recaptcha-mock">
      <div class="check">
        <div class="recaptcha-check"><i class="ri-check-line"></i></div>
        I'm not a robot
      </div>
      <div class="recaptcha-logo">reCAPTCHA<br>Privacy - Terms</div>
    </div>

    <button type="submit" class="auth-btn">Sign up</button>
  </form>

  <div class="auth-footer-link">
    Sudah punya akun? <a href="{{ route('login') }}">Login sekarang</a>
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

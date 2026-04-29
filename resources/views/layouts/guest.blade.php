<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Login') — DINE POS</title>
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --navy: #0f1e3c; --navy-mid: #1a2f54; --blue: #1d4ed8;
      --border: #e2e8f0; --text: #1e293b; --text-muted: #64748b;
      --bg: #f4f6fb;
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; -webkit-font-smoothing: antialiased; }
    h1, h2, h3 { font-family: 'Syne', sans-serif; }

    /* Left panel */
    .auth-left {
      flex: 1;
      background: var(--navy);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
      overflow: hidden;
    }
    .auth-left::before {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
      top: -100px; left: -100px;
    }
    .auth-left::after {
      content: '';
      position: absolute;
      width: 300px; height: 300px;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
      bottom: -60px; right: -60px;
    }
    .auth-logo {
      display: flex; align-items: center; gap: 12px;
      position: absolute; top: 28px; left: 28px;
    }
    .auth-logo img { width: 36px; height: 36px; filter: brightness(0) invert(1); }
    .auth-logo span { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: .04em; }
    .auth-illustration {
      width: min(360px, 80%);
      aspect-ratio: 1;
      background: rgba(255,255,255,.08);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 7rem;
      margin-bottom: 16px;
      position: relative; z-index: 1;
    }
    .auth-left-foot {
      position: absolute; bottom: 20px;
      font-size: .75rem; color: rgba(255,255,255,.4);
    }

    /* Right panel */
    .auth-right {
      width: 480px;
      background: #fff;
      display: flex;
      flex-direction: column;
      padding: 0;
      position: relative;
    }
    .auth-right-top {
      padding: 20px 40px;
      display: flex; align-items: center; justify-content: flex-end;
      border-bottom: 1px solid var(--border);
    }
    .auth-right-body {
      flex: 1; display: flex; align-items: center;
      padding: 40px;
    }
    .auth-form-inner { width: 100%; }
    .auth-title { font-size: 1.8rem; color: var(--navy); margin-bottom: 6px; }
    .auth-sub { font-size: .875rem; color: var(--text-muted); margin-bottom: 28px; }

    .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
    .form-label { font-size: .8rem; font-weight: 600; color: var(--text-muted); }
    .form-control {
      padding: 10px 14px; border: 1.5px solid var(--border);
      border-radius: 10px; font-family: inherit; font-size: .9rem;
      color: var(--text); outline: none; width: 100%;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
    .input-wrapper { position: relative; }
    .input-wrapper .form-control { padding-right: 44px; }
    .input-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      color: var(--text-muted); cursor: pointer; font-size: .8rem;
      display: flex; align-items: center; gap: 4px;
    }

    .form-hint { font-size: .75rem; color: var(--text-muted); margin-top: 4px; }
    .form-error { font-size: .75rem; color: #ef4444; margin-top: 4px; }

    .checkbox-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
    .checkbox-row input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--navy); margin-top: 2px; flex-shrink: 0; }
    .checkbox-row label { font-size: .85rem; color: var(--text); line-height: 1.4; }
    .checkbox-row a { color: var(--blue); text-decoration: underline; }

    /* CAPTCHA mock */
    .recaptcha-mock {
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 16px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      margin-bottom: 16px;
      background: var(--bg);
    }
    .recaptcha-mock .check { display: flex; align-items: center; gap: 10px; font-size: .875rem; color: var(--text); }
    .recaptcha-check { width: 18px; height: 18px; border-radius: 4px; background: #22c55e; border: none; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .8rem; }
    .recaptcha-logo { font-size: .7rem; color: var(--text-muted); text-align: right; }

    .auth-btn {
      width: 100%; padding: 13px;
      background: var(--navy); color: #fff;
      border: none; border-radius: 10px;
      font-family: inherit; font-size: .95rem; font-weight: 700;
      cursor: pointer; transition: all .2s;
    }
    .auth-btn:hover { background: var(--navy-mid); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,30,60,.25); }
    .auth-btn:disabled { opacity: .6; pointer-events: none; }

    .auth-footer-link { text-align: center; margin-top: 20px; font-size: .85rem; color: var(--text-muted); }
    .auth-footer-link a { color: var(--blue); font-weight: 600; }

    .nav-btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 18px; border-radius: 8px;
      background: var(--navy); color: #fff;
      font-family: inherit; font-size: .85rem; font-weight: 600;
      border: none; cursor: pointer; text-decoration: none;
    }

    .error-alert {
      background: #fef2f2; color: #991b1b;
      border: 1px solid #fecaca;
      border-radius: 8px; padding: 10px 14px;
      font-size: .85rem; margin-bottom: 16px;
    }

    @media (max-width: 768px) {
      .auth-left { display: none; }
      .auth-right { width: 100%; }
    }
  </style>
</head>
<body>

<div class="auth-left">
  <div class="auth-logo">
    <img src="{{ asset('images/Logo(mini).png') }}" alt="DINE POS" />
    <span>DINE POS</span>
  </div>
  <div class="auth-illustration">
  <img src="{{ asset('images/Logo(XL).png') }}" alt="DINE POS" style="width:150%;height:150%;object-fit:contain;" />
</div>
  <div class="auth-left-foot">
    &copy; Copyright {{ date('Y') }}, All Rights Reserved by DINEPOS
  </div>
</div>

<div class="auth-right">
  <div class="auth-right-top">
    @yield('nav_action')
  </div>
  <div class="auth-right-body">
    <div class="auth-form-inner">
      @if($errors->any())
        <div class="error-alert">
          @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
        </div>
      @endif
      @yield('form')
    </div>
  </div>
</div>

@stack('scripts')
</body>
</html>

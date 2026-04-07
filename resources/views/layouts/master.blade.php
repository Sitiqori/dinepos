<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') — DINE POS</title>

  {{-- Favicon --}}
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png" />

  {{-- Icons --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" />

  {{-- Global styles --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}" />

  {{-- Page-specific styles --}}
  @stack('styles')
</head>
<body>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app-wrapper">

  {{-- ════════════ SIDEBAR ════════════ --}}
  @include('layouts.partials.sidebar')

  {{-- ════════════ MAIN ════════════ --}}
  <div class="main-content">

    {{-- Topbar --}}
    @include('layouts.partials.navbar')

    {{-- Flash messages --}}
    <div style="padding: 0 24px; padding-top: 16px;">
      @if(session('success'))
        <div class="alert alert-success" data-auto-close>
          <i class="ri-checkbox-circle-line"></i>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-error" data-auto-close>
          <i class="ri-error-warning-line"></i>
          {{ session('error') }}
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-error" data-auto-close>
          <i class="ri-error-warning-line"></i>
          <ul style="margin:0;padding-left:16px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    {{-- Page body --}}
    <main class="page-body">
      @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

  </div>{{-- .main-content --}}
</div>{{-- .app-wrapper --}}

{{-- Global scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.3/chart.umd.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>

{{-- Page-specific scripts --}}
@stack('scripts')

</body>
</html>

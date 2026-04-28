@if ($paginator->hasPages())
<nav class="dine-pagination" aria-label="Navigasi halaman">

  {{-- Tombol Previous --}}
  @if ($paginator->onFirstPage())
    <button class="page-btn page-nav" disabled aria-disabled="true">
      <i class="ri-arrow-left-s-line"></i>
    </button>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" class="page-btn page-nav" rel="prev" aria-label="Halaman Sebelumnya">
      <i class="ri-arrow-left-s-line"></i>
    </a>
  @endif

  {{-- Nomor Halaman --}}
  @foreach ($elements as $element)

    {{-- Ellipsis --}}
    @if (is_string($element))
      <span class="page-btn page-dots">···</span>
    @endif

    {{-- Halaman --}}
    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span class="page-btn active" aria-current="page">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
        @endif
      @endforeach
    @endif

  @endforeach

  {{-- Tombol Next --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="page-btn page-nav" rel="next" aria-label="Halaman Berikutnya">
      <i class="ri-arrow-right-s-line"></i>
    </a>
  @else
    <button class="page-btn page-nav" disabled aria-disabled="true">
      <i class="ri-arrow-right-s-line"></i>
    </button>
  @endif

</nav>
@endif
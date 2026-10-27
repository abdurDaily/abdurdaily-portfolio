@props([
    'title',
    'description',
    'canonical' => url()->current(),
    'image' => null,
    'type' => 'website',
    'schema' => [],
])
@php
    $siteName = setting('site.name');
    $image = media_url($image ?? setting('site.og_image'));
    // nav links jump within the home page; from any other page they go back to it
    $home = request()->routeIs('home') ? '' : route('home');
    $email = setting('contact.email');
    $whatsapp = 'https://wa.me/' . setting('contact.whatsapp');
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  <meta name="description" content="{{ $description }}">
  <link rel="canonical" href="{{ $canonical }}">
  <meta name="robots" content="{{ app()->isProduction() ? 'index, follow, max-image-preview:large, max-snippet:-1' : 'noindex, nofollow' }}">
  <meta name="author" content="{{ setting('site.person') }}">
  <meta name="theme-color" content="#f5f4ef">
  @if (setting('site.google_verification'))
  <meta name="google-site-verification" content="{{ setting('site.google_verification') }}">
  @endif

  <meta property="og:site_name" content="{{ $siteName }}">
  <meta property="og:type" content="{{ $type }}">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:url" content="{{ $canonical }}">
  <meta property="og:locale" content="en_US">
  @if ($image)
  <meta property="og:image" content="{{ $image }}">
  <meta property="og:image:alt" content="{{ setting('site.person') }} — {{ setting('site.job_title') }}">
  @endif
  <meta name="twitter:card" content="summary_large_image">
  @if (setting('site.twitter'))
  <meta name="twitter:site" content="{{ setting('site.twitter') }}">
  <meta name="twitter:creator" content="{{ setting('site.twitter') }}">
  @endif
  <meta name="twitter:title" content="{{ $title }}">
  <meta name="twitter:description" content="{{ $description }}">
  @if ($image)
  <meta name="twitter:image" content="{{ $image }}">
  @endif

  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23121210'/%3E%3Ctext x='50%25' y='56%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-weight='700' font-size='30' fill='%23ff5a1f'%3EaD%3C/text%3E%3C/svg%3E">
  @if ($schema)
  <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $schema], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
  @endif
  {{ $head ?? '' }}

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/devicon.min.css" rel="stylesheet">
  <link href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}" rel="stylesheet">
</head>
<body data-timezone="{{ setting('contact.timezone', 'UTC') }}">
  <a class="skip-link" href="#main">Skip to content</a>

  {{ $preloader ?? '' }}

  <!-- Cursor -->
  <div class="cursor-dot" aria-hidden="true"></div>
  <div class="cursor-ring" aria-hidden="true"><span class="cursor-label"></span></div>
  <div class="hover-preview" aria-hidden="true"><img alt=""></div>

  <div class="scroll-progress" aria-hidden="true"></div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top site-nav" aria-label="Main navigation">
    <div class="container">
      <a class="navbar-brand logo" href="{{ $home }}#home" aria-label="{{ $siteName }} home">abdur<span>Daily</span><i class="caret"></i></a>
      <button class="navbar-toggler nav-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span></span><span></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link" href="{{ $home }}#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ $home }}#services">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ $home }}#reviews">Reviews</a></li>
        </ul>
        <a href="{{ $home }}#contact" class="btn btn-ink magnetic"><span>Hire me</span> <i class="bi bi-arrow-up-right"></i></a>
        <div class="nav-mobile-foot mono">
          <a href="mailto:{{ $email }}">{{ $email }}</a>
          <a href="{{ $whatsapp }}" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        </div>
      </div>
    </div>
  </nav>

  <main id="main">
    {{ $slot }}
  </main>

  <!-- ============ FOOTER ============ -->
  <footer class="footer">
    <div class="container">
      <div class="footer__top">
        <p class="footer__cta">{{ setting('footer.cta') }} <a href="{{ $home }}#contact">Let's talk <i class="bi bi-arrow-up-right"></i></a></p>
        <a href="{{ $home }}#home" class="back-top magnetic" aria-label="Back to top"><i class="bi bi-arrow-up"></i></a>
      </div>
      <div class="footer__nav mono">
        <ul class="footer__links list-unstyled">
          <li><a href="{{ $home }}#about">about</a></li>
          <li><a href="{{ $home }}#services">services</a></li>
          <li><a href="{{ $home }}#clients">clients</a></li>
          <li><a href="{{ $home }}#reviews">reviews</a></li>
          <li><a href="{{ $home }}#faq">faq</a></li>
          <li><a href="{{ $home }}#contact">contact</a></li>
          @foreach (setting('footer.links', []) as $link)
          <li><a href="{{ $link['url'] }}" target="_blank" rel="{{ ($link['sponsored'] ?? false) ? 'sponsored noopener' : 'noopener' }}">{{ $link['label'] }} <i class="bi bi-arrow-up-right"></i></a></li>
          @endforeach
        </ul>
        <p class="mb-0">{{ setting('contact.city') }} · <span class="js-clock">--:--</span> local</p>
      </div>
    </div>
    <!-- spotlight follows the cursor (drifts on its own on touch screens) -->
    <div class="footer__brand logo" aria-hidden="true">abdur<span>Daily</span><i class="caret"></i></div>
    <div class="container">
      <div class="footer__bottom mono">
        <p class="mb-0">© {{ now()->year }} {{ setting('site.person') }} · <a href="{{ route('home') }}">{{ $siteName }}</a></p>
        <p class="mb-0">{{ setting('footer.tagline') }} <span class="footer__inf">∞</span></p>
      </div>
    </div>
  </footer>

  <!-- Floating quick-chat (appears after the hero) -->
  <a class="float-chat" href="{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <span class="float-chat__icon" aria-hidden="true"><i class="bi bi-whatsapp"></i><span class="float-chat__dot"></span></span>
    <span class="float-chat__text"><strong>Chat on WhatsApp</strong><small class="mono">usually replies in &lt; {{ setting('contact.reply_time') }}</small></span>
  </a>

  <!-- Media viewer for client photos & video reviews -->
  <div class="modal fade media-modal" id="mediaModal" tabindex="-1" aria-label="Media viewer" aria-hidden="true" data-lenis-prevent>
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <button type="button" class="media-modal__close" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-lg"></i></button>
        <div class="media-modal__stage"></div>
        <p class="media-modal__caption mono"></p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
  <script src="{{ asset('assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}"></script>
</body>
</html>

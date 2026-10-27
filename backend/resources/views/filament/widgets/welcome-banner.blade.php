<x-filament-widgets::widget>
  <section class="ad-hero">
    <div class="ad-hero__glow" aria-hidden="true"></div>
    <div class="ad-hero__text">
      <p class="ad-mono ad-hero__meta">
        {{ $now->format('l, j F') }} · {{ $city }} {{ $now->format('H:i') }}
        <span @class(['ad-status', 'ad-status--off' => ! $available])><i></i>{{ $available ? 'Open for work' : 'Not taking work' }}</span>
      </p>
      <h2 class="ad-hero__title">{{ $greeting }}, <em>{{ $firstName }}</em>.</h2>
      <p class="ad-hero__sub">
        @if ($unread)
          You have <strong>{{ $unread }} new {{ Str::plural('inquiry', $unread) }}</strong> waiting in your inbox.
        @else
          Your inbox is clear. Everything on the site is up to date.
        @endif
      </p>
    </div>
    <nav class="ad-hero__actions" aria-label="Quick actions">
      <a href="{{ $links['inbox'] }}" class="ad-btn ad-btn--accent">
        <x-filament::icon icon="heroicon-o-inbox" class="ad-btn__icon" /> Inbox
        @if ($unread)<span class="ad-btn__count">{{ $unread }}</span>@endif
      </a>
      <a href="{{ $links['content'] }}" class="ad-btn"><x-filament::icon icon="heroicon-o-pencil-square" class="ad-btn__icon" /> Edit site content</a>
      <a href="{{ $links['services'] }}" class="ad-btn"><x-filament::icon icon="heroicon-o-briefcase" class="ad-btn__icon" /> Services</a>
      <a href="{{ $links['site'] }}" target="_blank" rel="noopener" class="ad-btn ad-btn--ghost">View live site <x-filament::icon icon="heroicon-o-arrow-up-right" class="ad-btn__icon" /></a>
    </nav>
  </section>
</x-filament-widgets::widget>

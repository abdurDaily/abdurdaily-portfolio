<x-filament-widgets::widget>
  <x-filament::section class="ad-seo">
    <x-slot name="heading">SEO health</x-slot>
    <x-slot name="description">{{ $passed }} of {{ $total }} checks passing</x-slot>
    <x-slot name="afterHeader">
      <span class="ad-score" style="--score: {{ $score }}" title="{{ $score }}%"><b>{{ $score }}</b></span>
    </x-slot>

    <ul class="ad-checks">
      @foreach ($checks as $check)
      <li @class(['ad-check', 'ad-check--ok' => $check['ok']])>
        <x-filament::icon :icon="$check['ok'] ? 'heroicon-s-check-circle' : 'heroicon-s-exclamation-circle'" class="ad-check__icon" />
        <span class="ad-check__text">
          @if ($check['url'] && ! $check['ok'])
          <a href="{{ $check['url'] }}">{{ $check['label'] }}</a>
          @else
          {{ $check['label'] }}
          @endif
          <small>{{ $check['detail'] }}</small>
        </span>
      </li>
      @endforeach
    </ul>

    <a href="{{ $sitemap }}" target="_blank" rel="noopener" class="ad-seo__sitemap ad-mono">sitemap.xml · {{ $pages }} pages ↗</a>
  </x-filament::section>
</x-filament-widgets::widget>

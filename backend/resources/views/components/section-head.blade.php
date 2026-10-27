@props(['number', 'section', 'tag' => 'h2'])
{{-- Numbered label + heading (+ optional description to the right), from a *_section setting. --}}
@php($hasDesc = filled($section['description'] ?? null))
<div @class(['section-head', 'section-head--split' => $hasDesc])>
  <div>
    <p class="label mono" data-reveal><span>{{ $number }}</span> {{ $section['label'] ?? '' }} <em class="label__cue">{{ $section['cue'] ?? '' }}</em></p>
    <{{ $tag }} class="section-title" data-split>{{ marked($section['title'] ?? '') }}</{{ $tag }}>
  </div>
  @if ($hasDesc)
  <p class="section-desc" data-reveal>{{ $section['description'] }}</p>
  @endif
</div>

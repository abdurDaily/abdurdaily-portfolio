@php
    $whatsapp = 'https://wa.me/' . setting('contact.whatsapp');
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
@endphp
<x-layout :title="$title" :description="$description" :canonical="route('services.show', $service)" :image="$service->image" :schema="$schema">
    <article class="section service-page">
      <div class="container">
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb-trail mono list-unstyled">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('home') }}#services">Services</a></li>
            <li aria-current="page">{{ $service->title }}</li>
          </ol>
        </nav>

        <header class="section-head section-head--split">
          <div>
            <p class="label mono"><span>~</span> {{ setting('services_section.label') }} <em class="label__cue">{{ setting('site.person') }}</em></p>
            <h1 class="section-title">{{ $service->title }}</h1>
          </div>
          <p class="section-desc">{{ $service->summary }}</p>
        </header>

        <div class="service-page__grid">
          <div class="prose">{!! $service->body !!}</div>
          <aside class="service-page__aside">
            @if ($service->image)
            <img class="service-page__img" src="{{ media_url($service->image) }}" alt="{{ $service->title }}" width="900" height="600">
            @endif
            <div class="service-page__cta">
              <p><strong>Need this for your business?</strong> Tell me what you're building — I reply within {{ setting('contact.reply_time') }}.</p>
              <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('home') }}#contact" class="btn btn-ink magnetic"><span>Start a project</span> <i class="bi bi-arrow-up-right"></i></a>
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-line magnetic"><span><i class="bi bi-whatsapp"></i> WhatsApp</span></a>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </article>

    @if ($others->isNotEmpty())
    <section class="section services" aria-labelledby="otherServices">
      <div class="container">
        <h2 class="section-title" id="otherServices">More {{ str(setting('services_section.label'))->lower() }}</h2>
        <ul class="service-list list-unstyled">
          @foreach ($others as $other)
          <li class="service-row" data-img="{{ media_url($other->image) }}">
            <span class="service-row__num mono">{{ $pad($loop->iteration) }}</span>
            <h3><a class="service-row__link" href="{{ route('services.show', $other) }}">{{ $other->title }}</a></h3>
            <p>{{ $other->summary }}</p>
            <span class="service-row__arrow" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
          </li>
          @endforeach
        </ul>
      </div>
    </section>
    @endif
</x-layout>

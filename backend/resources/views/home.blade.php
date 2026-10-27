@php
    $person = setting('site.person');
    $email = setting('contact.email');
    $phone = setting('contact.phone');
    $whatsapp = 'https://wa.me/' . setting('contact.whatsapp');
    $socials = collect(setting('socials', []));
    $featuredStack = $stackGroups->flatMap->items->where('is_featured');
    // fan pose for each industry card (rotation, vertical offset), repeating
    $fan = [['-7deg', '0rem'], ['4deg', '2.5rem'], ['-3deg', '-.5rem'], ['6deg', '2rem']];
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
@endphp
<x-layout :title="setting('site.meta_title')" :description="setting('site.meta_description')" :canonical="route('home')" type="profile" :schema="$schema">
  <x-slot:head>
    <link rel="preload" as="image" href="{{ media_url(setting('hero.image')) }}" fetchpriority="high">
  </x-slot:head>

  <x-slot:preloader>
  <div class="preloader" aria-hidden="true">
    <div class="preloader__logo logo">abdur<span>Daily</span><i class="caret"></i></div>
    <div class="preloader__log mono">
      <span class="js-boot">booting portfolio…</span>
      <span class="preloader__count"><span class="js-count">0</span>%</span>
    </div>
    <div class="preloader__bar"><span></span></div>
  </div>
  </x-slot:preloader>

    <!-- ============ HERO ============ -->
    <section class="hero" id="home">
      <!-- faint code symbols drifting behind the hero (decorative; --x/--y position, --s size, --d phase) -->
      <div class="hero__code" aria-hidden="true">
        <span style="--x:4%;--y:16%;--s:2.8rem;--d:0s">&lt;/&gt;</span>
        <span style="--x:38%;--y:9%;--s:2rem;--d:-4s">{ }</span>
        <span style="--x:90%;--y:44%;--s:2.6rem;--d:-7s">=&gt;</span>
        <span style="--x:24%;--y:84%;--s:2rem;--d:-2s">( )</span>
        <span style="--x:70%;--y:88%;--s:2.3rem;--d:-9s">[ ]</span>
        <span style="--x:96%;--y:12%;--s:1.8rem;--d:-5s">;</span>
        <span style="--x:47%;--y:72%;--s:1.6rem;--d:-11s">&amp;&amp;</span>
        <span style="--x:62%;--y:16%;--s:1.5rem;--d:-3s">#</span>
        <span style="--x:12%;--y:62%;--s:1.7rem;--d:-8s">$</span>
        <span style="--x:80%;--y:68%;--s:1.6rem;--d:-6s">//</span>
        <span style="--x:34%;--y:44%;--s:1.4rem;--d:-10s">!=</span>
      </div>
      <!-- three blocks around the portrait: who & why (left) · trust (centre) · act (right) -->
      <div class="container hero__grid">
        <div class="hero__intro">
          <h1 class="hero__name" data-hero-split><span class="hero__hello">{{ setting('hero.greeting') }}</span> {{ $person }}<span class="hero__dot">.</span></h1>
          <p class="hero__sub">{{ marked(setting('hero.subtitle'), 'strong') }}</p>
          <div class="hero__cta d-flex flex-wrap gap-3">
            <a href="#services" class="btn btn-ink btn-lg magnetic"><span>{{ setting('hero.primary_cta') }}</span> <i class="bi bi-arrow-down-right"></i></a>
            <a href="#contact" class="btn btn-line btn-lg magnetic"><span>{{ setting('hero.secondary_cta') }}</span></a>
          </div>
        </div>

        <div class="hero__portrait">
          <div class="hero__capsule">
            <img class="hero__cutout" src="{{ media_url(setting('hero.image')) }}" alt="{{ setting('hero.image_alt') }}" width="900" height="1081" fetchpriority="high">
          </div>
          @if (setting('contact.available'))
          <p class="hero__status mono"><span class="status__dot"></span> {{ setting('contact.available_text') }}</p>
          @endif
        </div>

        <div class="hero__meta">
          <dl class="hero-stats">
            @foreach (setting('hero.stats', []) as $stat)
            <div><dt>{{ $stat['label'] }}</dt><dd>{{ $stat['value'] }}<span>{{ $stat['suffix'] ?? '' }}</span></dd></div>
            @endforeach
          </dl>
          <ul class="hero-social list-unstyled">
            @foreach ($socials->where('in_hero', true) as $social)
            <li><a href="{{ $social['url'] }}" target="_blank" rel="noopener me" aria-label="{{ $social['label'] }}"><i class="bi {{ $social['icon'] }}"></i></a></li>
            @endforeach
          </ul>
        </div>

        <div class="hero-stack">
          <p class="mono">{{ setting('hero.stack_title') }}</p>
          <ul class="list-unstyled">
            @foreach ($featuredStack as $item)
            <li title="{{ $item->name }}"><i class="{{ $item->icon }}" aria-hidden="true"></i><span class="visually-hidden">{{ $item->name }}</span></li>
            @endforeach
          </ul>
        </div>

        <div class="hero-contact">
          <p class="hero-contact__head">
            <strong>{{ setting('hero.contact_title') }}</strong>
            <span class="mono">Replies within {{ setting('contact.reply_time') }} · {{ setting('contact.city') }} <span class="js-clock">--:--</span></span>
          </p>
          <ul class="list-unstyled">
            <li><a href="mailto:{{ $email }}"><span class="hero-contact__icon" aria-hidden="true"><i class="bi bi-envelope"></i></span><span><small class="mono">Email</small>{{ $email }}</span></a></li>
            <li><a href="tel:+{{ setting('contact.whatsapp') }}"><span class="hero-contact__icon" aria-hidden="true"><i class="bi bi-telephone"></i></span><span><small class="mono">Phone</small>{{ $phone }}</span></a></li>
          </ul>
        </div>

        <p class="hero__sign">{{ marked(setting('hero.sign'), 'em') }}</p>
      </div>
    </section>

    <!-- ============ BRANDS ============ -->
    @if (setting('brands.items'))
    <section class="brands" aria-label="Brands I've worked with">
      <div class="container">
        <p class="brands__title mono">{{ setting('brands.title') }}</p>
      </div>
      <div class="marquee" data-direction="left">
        <div class="marquee__track">
          <div class="marquee__group">
            @foreach (setting('brands.items') as $brand)
            <span class="brand-logo">@if (! empty($brand['icon']))<i class="bi {{ $brand['icon'] }}"></i> @endif{{ $brand['name'] }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </section>
    @endif

    <!-- ============ ABOUT ============ -->
    <section class="section about" id="about">
      <div class="container">
        <x-section-head number="01" :section="setting('about')" />

        <div class="about-grid">
          <div class="about-story" data-reveal>
            <p class="about-story__lead">{{ setting('about.lead') }}</p>
            @foreach (preg_split('/\R{2,}/', (string) setting('about.body')) as $paragraph)
            <p>{{ $paragraph }}</p>
            @endforeach

            <dl class="about-facts">
              @foreach (setting('about.facts', []) as $fact)
              <div><dt>{{ $fact['label'] }}</dt><dd>{{ $fact['value'] }}</dd></div>
              @endforeach
              @if (setting('contact.available'))
              <div><dt>Availability</dt><dd class="about-facts__open"><span class="status__dot"></span> {{ setting('about.availability') }}</dd></div>
              @endif
            </dl>

            <div class="d-flex flex-wrap gap-3">
              <a href="#contact" class="btn btn-ink magnetic"><span>{{ setting('about.primary_cta') }}</span> <i class="bi bi-arrow-up-right"></i></a>
              <a href="#services" class="btn btn-line magnetic"><span>{{ setting('about.secondary_cta') }}</span></a>
            </div>
          </div>

          <div class="about-principles">
            <p class="about-principles__title mono" data-reveal>{{ setting('about.principles_title') }}</p>
            <ol class="principles list-unstyled">
              @foreach (setting('about.principles', []) as $principle)
              <li data-reveal>
                <span class="mono">{{ $pad($loop->iteration) }}</span>
                <h3>{{ $principle['title'] }}</h3>
                <p>{{ $principle['text'] }}</p>
              </li>
              @endforeach
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ SERVICES ============ -->
    <section class="section services" id="services">
      <div class="container">
        <x-section-head number="02" :section="setting('services_section')" />

        <ul class="service-list list-unstyled">
          @foreach ($services as $service)
          <li class="service-row" data-reveal data-img="{{ media_url($service->image) }}">
            <span class="service-row__num mono">{{ $pad($loop->iteration) }}</span>
            <h3><a class="service-row__link" href="{{ route('services.show', $service) }}">{{ $service->title }}</a></h3>
            <p>{{ $service->summary }}</p>
            <span class="service-row__arrow" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
          </li>
          @endforeach
        </ul>
      </div>
    </section>

    <!-- ============ STACK ============ -->
    <section class="section stack" id="stack">
      <div class="container">
        <x-section-head number="03" :section="setting('stack_section')" />

        <div class="stack-cols">
          @foreach ($stackGroups as $group)
          <article class="stack-col" data-reveal>
            <header class="stack-col__head">
              <span class="mono">{{ $pad($loop->iteration) }}</span>
              <h3>{{ $group->title }}</h3>
              <p>{{ $group->subtitle }}</p>
            </header>
            <ul class="stack-list list-unstyled">
              @foreach ($group->items as $item)
              <li><span class="stack-list__icon"><i class="{{ $item->icon }}" aria-hidden="true"></i></span><span><strong>{{ $item->name }}</strong>{{ $item->description }}</span></li>
              @endforeach
            </ul>
          </article>
          @endforeach
        </div>
      </div>
    </section>

    <!-- ============ OUTLINE BAND ============ -->
    @if (setting('outline.words'))
    <section class="outline-band" aria-label="What I build">
      <div class="marquee" data-direction="right">
        <div class="marquee__track">
          <div class="marquee__group">
            @foreach (setting('outline.words') as $word)
            <span class="outline-word">{{ $word['word'] }}</span><span class="outline-sep" aria-hidden="true"></span>
            @endforeach
          </div>
        </div>
      </div>
    </section>
    @endif

    <!-- ============ INDUSTRIES ============ -->
    <section class="section industries" id="industries" aria-labelledby="industriesTitle">
      <div class="container">
        <div class="fan">
          @foreach ($industries as $industry)
          @php([$rotate, $offset] = $fan[$loop->index % count($fan)])
          <a href="#contact" class="fan-card" data-cursor-text="{{ setting('industries_section.card_cta') }}" style="--r:{{ $rotate }}; --ty:{{ $offset }}">
            <img src="{{ media_url($industry->image) }}" alt="" loading="lazy">
            <span class="fan-card__icon" aria-hidden="true"><i class="bi {{ $industry->icon }}"></i></span>
            <span class="fan-card__go" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
            <span class="fan-card__body"><strong>{{ $industry->title }}</strong><span class="mono">{{ $industry->tags }}</span></span>
          </a>
          @endforeach
        </div>
        <h2 class="industries__title" id="industriesTitle" data-split>{{ marked(setting('industries_section.title')) }}</h2>
      </div>
    </section>

    <!-- ============ PROCESS (pipeline) ============ -->
    <section class="section process" id="process">
      <div class="container">
        <x-section-head number="04" :section="setting('process')" />

        <div class="pipeline">
          <div class="pipeline__track" aria-hidden="true"><span></span></div>
          <ol class="pipeline__steps list-unstyled">
            @foreach (setting('process.steps', []) as $step)
            <li class="stage">
              <span class="stage__node"><i class="bi {{ $step['icon'] }}"></i></span>
              <span class="stage__status mono">{{ $step['status'] }}</span>
              <h3>{{ $step['title'] }}</h3>
              <p>{{ $step['text'] }}</p>
            </li>
            @endforeach
          </ol>
        </div>
      </div>
    </section>

    <!-- ============ FIXED-BACKGROUND STATEMENT ============ -->
    <section class="statement" aria-label="Working principles">
      <div class="container">
        <p class="label label--light mono" data-reveal><span>~</span> {{ setting('statement.label') }} <em class="label__cue">{{ setting('statement.cue') }}</em></p>
        <h2 class="statement__title" data-split>{{ marked(setting('statement.title')) }}</h2>
        <div class="row g-4 statement__points">
          @foreach (setting('statement.points', []) as $point)
          <div class="col-md-4" data-reveal><span class="mono">const</span> {{ $point['name'] }} = <em>"{{ $point['value'] }}"</em>;</div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- ============ WITH CLIENTS (photo gallery) ============ -->
    @if ($gallery->isNotEmpty())
    <section class="section moments" id="clients">
      <div class="container">
        <x-section-head number="05" :section="setting('gallery_section')" />

        <ul class="gallery list-unstyled" aria-label="Photo gallery">
          @foreach ($gallery as $photo)
          <li @class(['gallery__item', "gallery__item--{$photo->size}" => $photo->size !== 'standard']) data-reveal>
            <button type="button" class="gallery__media" data-media="image" data-src="{{ media_url($photo->image) }}" data-caption="{{ $photo->caption }}" data-cursor-text="Open">
              <img src="{{ media_url($photo->thumbnail ?: $photo->image) }}" alt="{{ $photo->alt }}" loading="lazy"><span class="gallery__zoom" aria-hidden="true"><i class="bi bi-arrows-angle-expand"></i></span><span class="gallery__cap"><span class="mono">{{ $photo->kicker }}</span>{{ $photo->title }}</span>
            </button>
          </li>
          @endforeach
        </ul>
      </div>
    </section>
    @endif

    <!-- ============ REVIEWS ============ -->
    @if ($testimonials->isNotEmpty())
    @php($total = $pad($testimonials->count()))
    <section class="section reviews" id="reviews">
      <div class="container">
        <x-section-head number="06" :section="setting('reviews_section')" />

        <!-- testimonial slider: autoplays every 7s, pauses on hover/focus and off-screen; arrows, dots, swipe and ← → keys all work -->
        <div class="tslider" data-tslider data-reveal role="region" aria-roledescription="carousel" aria-label="Client reviews">
          <div class="tslider__viewport">
            <div class="tslider__track">
              @foreach ($testimonials as $review)
              <article class="testimonial testimonial--featured tslider__slide" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $loop->count }}">
                <div class="testimonial__body">
                  <span class="testimonial__mark" aria-hidden="true">&ldquo;</span>
                  <blockquote>{{ $review->quote }}</blockquote>
                  <footer class="testimonial__author">
                    @if ($review->avatar)
                    <img class="testimonial__avatar" src="{{ media_url($review->avatar) }}" alt="" width="48" height="48" loading="lazy">
                    @else
                    <span class="testimonial__initials" aria-hidden="true">{{ $review->initials() }}</span>
                    @endif
                    <span><strong>{{ $review->name }}</strong><span class="mono">{{ $review->byline() }}</span></span>
                  </footer>
                </div>
                @if ($review->hasVideo())
                <button type="button" class="testimonial__video" data-media="{{ $review->video_type === 'youtube' ? 'youtube' : 'video' }}" data-src="{{ $review->videoSource() }}" data-caption="{{ $review->name }} — {{ $review->byline() }}" data-cursor-text="Play">
                  @if ($review->poster ?: $review->avatar)
                  <img src="{{ media_url($review->poster ?: $review->avatar) }}" alt="Play {{ $review->name }}'s video review" loading="lazy">
                  @endif
                  <span class="testimonial__play" aria-hidden="true"><i class="bi bi-play-fill"></i></span>
                  <span class="testimonial__vlabel mono"><i class="bi bi-camera-video" aria-hidden="true"></i> Video review</span>
                </button>
                @else
                <div class="testimonial__panel">
                  <span class="testimonial__panel-initials" aria-hidden="true">{{ $review->initials() }}</span>
                  <p><strong>{{ $review->company ?: $review->name }}</strong><span class="mono">{{ collect([$review->project, $review->country])->filter()->implode(' · ') }}</span></p>
                  @if ($review->note)
                  <p class="testimonial__panel-note"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> {{ $review->note }}</p>
                  @endif
                </div>
                @endif
              </article>
              @endforeach
            </div>
          </div>

          <div class="tslider__bar">
            <div class="tslider__dots">
              @foreach ($testimonials as $review)
              <button type="button" class="tslider__dot" aria-label="Show review {{ $loop->iteration }}"></button>
              @endforeach
            </div>
            <p class="tslider__count mono" aria-live="polite"><b>01</b> / {{ $total }}</p>
            <div class="tslider__btns">
              <button type="button" class="slider-btn" data-tslider-prev aria-label="Previous review"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
              <button type="button" class="slider-btn" data-tslider-next aria-label="Next review"><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

    <!-- ============ FAQ ============ -->
    @if ($faqs->isNotEmpty())
    <section class="section faq-section" id="faq">
      <div class="container">
        <div class="row g-5">
          <div class="col-lg-5">
            <p class="label mono" data-reveal><span>07</span> {{ setting('faq_section.label') }} <em class="label__cue">{{ setting('faq_section.cue') }}</em></p>
            <h2 class="section-title" data-split>{{ marked(setting('faq_section.title')) }}</h2>
            <p class="section-desc" data-reveal>{{ setting('faq_section.description') }}</p>
            <a href="#contact" class="btn btn-line mt-4 magnetic" data-reveal><span>{{ setting('faq_section.cta') }}</span> <i class="bi bi-arrow-up-right"></i></a>
          </div>
          <div class="col-lg-7">
            <div class="faq" id="faqList">
              @foreach ($faqs as $faq)
              @php($open = $loop->first)
              <div class="faq-item" data-reveal>
                <h3 class="faq-q"><button type="button" @class(['collapsed' => ! $open]) data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}" aria-expanded="{{ $open ? 'true' : 'false' }}" aria-controls="faq{{ $faq->id }}"><span class="mono">{{ $pad($loop->iteration) }}</span>{{ $faq->question }}<i class="bi bi-plus-lg"></i></button></h3>
                <div id="faq{{ $faq->id }}" @class(['collapse', 'show' => $open]) data-bs-parent="#faqList"><p>{{ $faq->answer }}</p></div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

    <!-- ============ CONTACT ============ -->
    <section class="section contact" id="contact">
      <div class="container">
        <div class="cta-banner" data-reveal>
          <span class="cta-banner__bg" aria-hidden="true"></span>
          <p class="cta-banner__label mono">08 — {{ setting('contact_section.label') }}</p>
          <h2 class="cta-banner__title">{{ marked(setting('contact_section.title'), 'em') }}</h2>
          <p class="cta-banner__sub">{{ setting('contact_section.subtitle') }}</p>
          <div class="d-flex flex-wrap gap-3">
            <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-lg btn-cream magnetic"><span>Chat on WhatsApp</span> <span class="btn-dot" aria-hidden="true"><i class="bi bi-arrow-right"></i></span></a>
            <a href="mailto:{{ $email }}" class="btn btn-lg btn-glass magnetic"><span>Email me</span></a>
          </div>
        </div>

        <div class="row g-5">
          <div class="col-lg-5">
            <p class="section-desc" data-reveal>{{ setting('contact_section.form_intro') }}</p>

            <div class="email-card" data-reveal>
              <span class="mono">email</span>
              <a href="mailto:{{ $email }}" class="email-card__link">{{ $email }}</a>
              <button type="button" class="copy-btn mono" data-copy="{{ $email }}"><i class="bi bi-clipboard"></i> <span>copy</span></button>
            </div>

            <ul class="contact-list list-unstyled" data-reveal>
              <li><i class="bi bi-whatsapp"></i><a href="{{ $whatsapp }}" target="_blank" rel="noopener">{{ $phone }}</a></li>
              <li><i class="bi bi-geo-alt"></i><span>{{ setting('contact.country') }} · Remote worldwide</span></li>
            </ul>
            <div class="socials" data-reveal>
              @foreach ($socials as $social)
              <a href="{{ $social['url'] }}" target="_blank" rel="noopener me" class="magnetic" aria-label="{{ $social['label'] }}"><i class="bi {{ $social['icon'] }}"></i></a>
              @endforeach
            </div>
          </div>

          <div class="col-lg-7">
            <form class="contact-form needs-validation" method="post" action="{{ route('contact.store') }}" novalidate data-reveal>
              @csrf
              <div class="contact-form__head mono"><span>new Project({</span><span class="text-end">// all fields stay private</span></div>
              @if (session('contact_status'))
              <p class="form-note mono" role="status">{{ session('contact_status') }}</p>
              @endif
              {{-- honeypot: real visitors never see or fill this --}}
              <div class="visually-hidden" aria-hidden="true">
                <label for="cWebsite">Website</label>
                <input type="text" id="cWebsite" name="website" tabindex="-1" autocomplete="off">
              </div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="cName" class="form-label mono">name:</label>
                  <input type="text" @class(['form-control', 'is-invalid' => $errors->has('name')]) id="cName" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name" placeholder="Jane Doe">
                  <div class="invalid-feedback">{{ $errors->first('name') ?: 'Please enter your name.' }}</div>
                </div>
                <div class="col-md-6">
                  <label for="cEmail" class="form-label mono">email:</label>
                  <input type="email" @class(['form-control', 'is-invalid' => $errors->has('email')]) id="cEmail" name="email" value="{{ old('email') }}" required maxlength="150" autocomplete="email" placeholder="jane@company.com">
                  <div class="invalid-feedback">{{ $errors->first('email') ?: 'Please enter a valid email.' }}</div>
                </div>
                <div class="col-12">
                  <label for="cPhone" class="form-label mono">phone: <span class="form-optional">// optional, WhatsApp works too</span></label>
                  <input type="tel" @class(['form-control', 'is-invalid' => $errors->has('phone')]) id="cPhone" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" pattern="[0-9+()\-\s]{6,20}" placeholder="+1 555 123 4567">
                  <div class="invalid-feedback">{{ $errors->first('phone') ?: 'Please enter a valid phone number.' }}</div>
                </div>
                <div class="col-12">
                  <label for="cMsg" class="form-label mono">details:</label>
                  <textarea @class(['form-control', 'is-invalid' => $errors->has('message')]) id="cMsg" name="message" rows="5" required minlength="10" maxlength="5000" placeholder="What are you building, and when do you need it?">{{ old('message') }}</textarea>
                  <div class="invalid-feedback">{{ $errors->first('message') ?: 'Please share a few details (10+ characters).' }}</div>
                </div>
                <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3">
                  <span class="mono contact-form__close">});</span>
                  <button type="submit" class="btn btn-accent btn-lg magnetic"><span>Send message</span> <i class="bi bi-send"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
</x-layout>

(() => {
  'use strict';

  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];

  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* ------------------------------------------------------------------
     Works without GSAP: year, live Dhaka clock, contact form, copy email
  ------------------------------------------------------------------ */
  $('.js-year').textContent = new Date().getFullYear();

  const clockFmt = new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Dhaka', hour: '2-digit', minute: '2-digit' });
  const tickClock = () => $$('.js-clock').forEach(el => { el.textContent = clockFmt.format(new Date()); });
  tickClock();
  setInterval(tickClock, 15000);

  let lenis = null;

  initMobileMenu();
  initContactForm();
  initCopyButtons();
  initMediaModal();
  initWorkFilter();
  initGallery();
  initTestimonials();

  if (!window.gsap || !window.ScrollTrigger) {
    $('.preloader')?.remove();
    return;
  }

  gsap.registerPlugin(ScrollTrigger);
  // The intro and the one-shot scroll reveals assume a load at the top. If the browser
  // (or ScrollTrigger's own scroll memory) restores a mid-page position on reload, passed
  // "once" triggers kill themselves inside ScrollTrigger's first refresh and crash it.
  // (behavior 'instant' matters: the CSS `scroll-behavior: smooth` would otherwise animate it)
  ScrollTrigger.clearScrollMemory('manual');
  window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
  gsap.defaults({ ease: 'power3.out', duration: 1 });

  /* ------------------------------------------------------------------
     Smooth scroll (Lenis) synced with ScrollTrigger
  ------------------------------------------------------------------ */
  if (!reduceMotion && window.Lenis) {
    lenis = new Lenis({ duration: 1.15, easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)) });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(time => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
    lenis.stop(); // locked until the preloader finishes
  }

  const navCollapse = $('#mainNav');
  $$('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const id = link.getAttribute('href');
      const target = id.length > 1 && $(id);
      if (!target) return;
      e.preventDefault();
      // close the menu first — it unlocks scrolling, which the scroll below needs
      if (navCollapse.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(navCollapse).hide();
      if (lenis) lenis.scrollTo(target, { offset: id === '#home' ? 0 : -20 });
      else target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  });

  /* ------------------------------------------------------------------
     Word splitting — keeps inline elements like .hl intact
  ------------------------------------------------------------------ */
  function wrapWord(node) {
    const outer = document.createElement('span');
    const inner = document.createElement('span');
    outer.className = 'w';
    inner.className = 'wi';
    inner.append(node);
    outer.append(inner);
    return outer;
  }

  function splitWords(el) {
    const frag = document.createDocumentFragment();
    [...el.childNodes].forEach(node => {
      if (node.nodeType === Node.TEXT_NODE) {
        node.textContent.split(/(\s+)/).forEach(part => {
          if (!part) return;
          frag.append(/^\s+$/.test(part) ? ' ' : wrapWord(document.createTextNode(part)));
        });
      } else {
        frag.append(wrapWord(node.cloneNode(true)));
      }
    });
    el.replaceChildren(frag);
    return $$('.wi', el);
  }

  /* ------------------------------------------------------------------
     Preloader + hero intro
  ------------------------------------------------------------------ */
  const heroWords = splitWords($('[data-hero-split]'));
  gsap.set(heroWords, { yPercent: 115 });
  const heroSides = ['.hero-stack', '.hero-contact', '.hero__meta', '.hero__sign'];
  gsap.set(['.hero__sub', '.hero__cta', ...heroSides], { autoAlpha: 0, y: 24 });
  gsap.set('.hero__portrait', { autoAlpha: 0, y: 30 });
  gsap.set('.hero__cutout', { autoAlpha: 0, yPercent: 12 });
  gsap.set('.hero__status', { autoAlpha: 0, scale: .8 }); // its centring lives in the CSS `translate` property, untouched here

  // calm, formal intro: name rises in, the portrait rises into its capsule and gets its badge, the side blocks follow
  function heroIntro() {
    return gsap.timeline()
      .to(heroWords, { yPercent: 0, duration: 1.1, stagger: .06, ease: 'power4.out' })
      .to('.hero__sub', { autoAlpha: 1, y: 0 }, '-=.8')
      .to('.hero__cta', { autoAlpha: 1, y: 0 }, '-=.85')
      .to('.hero__portrait', { autoAlpha: 1, y: 0, duration: 1.1 }, 0.2)
      .to('.hero__cutout', { autoAlpha: 1, yPercent: 0, duration: 1.4, ease: 'power4.out' }, '<.25')
      .to('.hero__status', { autoAlpha: 1, scale: 1, duration: .7, ease: 'back.out(1.8)' }, '<.9')
      .to(heroSides, { autoAlpha: 1, y: 0, duration: .8, stagger: .12 }, '<')
      .from('.float-chat', { scale: 0, autoAlpha: 0, duration: .8, ease: 'back.out(2.2)', clearProps: 'transform,opacity,visibility' }, '-=.6');
  }

  const bootMsgs = ['booting portfolio…', 'loading assets…', 'compiling styles…', 'ready ✓'];
  const counter = { v: 0 };
  const countEl = $('.js-count');
  const bootEl = $('.js-boot');
  // full boot animation once per visit; returning page loads are near-instant
  let seenBoot = false;
  try {
    seenBoot = sessionStorage.getItem('abdurDaily:booted') === '1';
    sessionStorage.setItem('abdurDaily:booted', '1');
  } catch { /* storage blocked — just play it */ }
  const bootDur = reduceMotion || seenBoot ? .2 : 1.5;
  gsap.timeline()
    .to(counter, {
      v: 100, duration: bootDur, ease: 'power2.inOut',
      onUpdate: () => {
        countEl.textContent = Math.round(counter.v);
        bootEl.textContent = bootMsgs[Math.min(bootMsgs.length - 1, Math.floor(counter.v / 100 * bootMsgs.length))];
      }
    })
    .to('.preloader__bar span', { scaleX: 1, duration: bootDur, ease: 'power2.inOut' }, 0)
    .to('.preloader > *', { autoAlpha: 0, y: -16, duration: seenBoot ? .15 : .35, stagger: .05 })
    .to('.preloader', { clipPath: 'inset(0 0 100% 0)', duration: seenBoot ? .6 : .9, ease: 'power4.inOut' })
    .add(() => {
      $('.preloader').remove();
      lenis?.start();
      ScrollTrigger.refresh();
    })
    .add(heroIntro(), '-=.45');

  /* ------------------------------------------------------------------
     Cursor, magnetic buttons, tilt, service hover previews
  ------------------------------------------------------------------ */
  if (finePointer && !reduceMotion) {
    document.body.classList.add('has-cursor');
    const dot = $('.cursor-dot');
    const ring = $('.cursor-ring');
    const label = $('.cursor-label');
    gsap.set([dot, ring], { xPercent: -50, yPercent: -50, x: innerWidth / 2, y: innerHeight / 2 });

    const dotX = gsap.quickTo(dot, 'x', { duration: .12, ease: 'power3' });
    const dotY = gsap.quickTo(dot, 'y', { duration: .12, ease: 'power3' });
    const ringX = gsap.quickTo(ring, 'x', { duration: .5, ease: 'power3' });
    const ringY = gsap.quickTo(ring, 'y', { duration: .5, ease: 'power3' });

    window.addEventListener('mousemove', e => {
      dotX(e.clientX); dotY(e.clientY);
      ringX(e.clientX); ringY(e.clientY);
    });
    document.addEventListener('mouseleave', () => { dot.classList.add('is-hidden'); ring.classList.add('is-hidden'); });
    document.addEventListener('mouseenter', () => { dot.classList.remove('is-hidden'); ring.classList.remove('is-hidden'); });

    document.addEventListener('mouseover', e => {
      const labelled = e.target.closest('[data-cursor-text]');
      const interactive = e.target.closest('a, button, .brand-logo, .service-row');
      const onDark = !!e.target.closest('.statement, .project--cta, .cta-banner, .footer');
      dot.classList.toggle('on-dark', onDark);
      ring.classList.toggle('on-dark', onDark);
      if (labelled) {
        label.textContent = labelled.dataset.cursorText;
        ring.classList.add('has-label');
        ring.classList.remove('is-hover');
      } else {
        ring.classList.remove('has-label');
        ring.classList.toggle('is-hover', !!interactive);
      }
    });
    document.addEventListener('mousedown', () => gsap.to(ring, { scale: .8, duration: .2 }));
    document.addEventListener('mouseup', () => gsap.to(ring, { scale: 1, duration: .3 }));

    $$('.magnetic').forEach(el => {
      const strength = el.classList.contains('btn') ? .3 : .45;
      const xTo = gsap.quickTo(el, 'x', { duration: .6, ease: 'power3' });
      const yTo = gsap.quickTo(el, 'y', { duration: .6, ease: 'power3' });
      el.addEventListener('mousemove', e => {
        const r = el.getBoundingClientRect();
        xTo((e.clientX - r.left - r.width / 2) * strength);
        yTo((e.clientY - r.top - r.height / 2) * strength);
      });
      el.addEventListener('mouseleave', () => gsap.to(el, { x: 0, y: 0, duration: 1, ease: 'elastic.out(1, .4)' }));
    });

    // image preview that follows the cursor over the services list
    const preview = $('.hover-preview');
    const previewImg = $('img', preview);
    gsap.set(preview, { autoAlpha: 0, scale: .85, rotation: -4 });
    const pX = gsap.quickTo(preview, 'x', { duration: .55, ease: 'power3' });
    const pY = gsap.quickTo(preview, 'y', { duration: .55, ease: 'power3' });
    $$('.service-row').forEach(row => {
      row.addEventListener('mouseenter', () => {
        previewImg.src = row.dataset.img;
        gsap.to(preview, { autoAlpha: 1, scale: 1, rotation: 0, duration: .45, overwrite: 'auto' });
      });
      row.addEventListener('mouseleave', () => gsap.to(preview, { autoAlpha: 0, scale: .85, rotation: -4, duration: .35, overwrite: 'auto' }));
      row.addEventListener('mousemove', e => {
        pX(e.clientX + 30);
        pY(e.clientY - 110);
      });
    });
  }

  /* ------------------------------------------------------------------
     Navbar: pill on scroll, hide on scroll down, active link, progress
  ------------------------------------------------------------------ */
  const nav = $('.site-nav');
  ScrollTrigger.create({
    start: 0,
    end: 'max',
    onUpdate(self) {
      const y = self.scroll();
      nav.classList.toggle('is-scrolled', y > 40);
      const menuOpen = navCollapse.classList.contains('show');
      nav.classList.toggle('is-hidden', !menuOpen && self.direction === 1 && y > 500);
    }
  });

  gsap.to('.scroll-progress', { scaleX: 1, ease: 'none', scrollTrigger: { start: 0, end: 'max', scrub: .3 } });

  $$('.site-nav .nav-link').forEach(link => {
    const section = $(link.getAttribute('href'));
    if (!section) return;
    ScrollTrigger.create({
      trigger: section,
      start: 'top 50%',
      end: 'bottom 50%',
      onToggle: self => link.classList.toggle('active', self.isActive)
    });
  });

  /* ------------------------------------------------------------------
     Scroll reveals
  ------------------------------------------------------------------ */
  $$('[data-split]').forEach(el => {
    const words = splitWords(el);
    const marks = $$('.hl', el).filter(hl => getComputedStyle(hl).backgroundImage !== 'none');
    const tl = gsap.timeline({ scrollTrigger: { trigger: el, start: 'top 85%', once: true } })
      .from(words, { yPercent: 115, duration: 1.1, stagger: .05, ease: 'power4.out' });
    if (marks.length) {
      gsap.set(marks, { backgroundSize: '0% 30%' });
      tl.to(marks, { backgroundSize: '100% 30%', duration: .9, ease: 'power3.inOut' }, '-=.45');
    }
  });

  gsap.set('[data-reveal]', { autoAlpha: 0, y: 40 });
  ScrollTrigger.batch('[data-reveal]', {
    start: 'top 88%',
    once: true,
    onEnter: batch => gsap.to(batch, { autoAlpha: 1, y: 0, duration: .9, stagger: .08, overwrite: true })
  });

  /* Pipeline: the line fills with scroll and each stage "passes" */
  const stages = $$('.stage');
  const pipeline = $('.pipeline');
  const setPipeline = p => {
    pipeline.style.setProperty('--p', p);
    stages.forEach((s, i) => s.classList.toggle('is-done', p > 0 && p >= i / (stages.length - 1) - .02));
  };
  if (reduceMotion) setPipeline(1);
  else {
    ScrollTrigger.create({
      trigger: pipeline,
      start: 'top 70%',
      end: 'bottom 55%',
      scrub: true,
      onUpdate: self => setPipeline(self.progress)
    });
  }

  /* ------------------------------------------------------------------
     Infinite brand marquee — reacts to scroll velocity
  ------------------------------------------------------------------ */
  const marquees = $$('.marquee').map(marquee => {
    const track = $('.marquee__track', marquee);
    const clone = $('.marquee__group', track).cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    track.append(clone);

    const dir = marquee.dataset.direction === 'right' ? 1 : -1;
    const tween = gsap.fromTo(track,
      { xPercent: dir === -1 ? 0 : -50 },
      { xPercent: dir === -1 ? -50 : 0, duration: reduceMotion ? 120 : 30, ease: 'none', repeat: -1 });

    marquee.addEventListener('mouseenter', () => gsap.to(tween, { timeScale: .15, duration: .6 }));
    marquee.addEventListener('mouseleave', () => gsap.to(tween, { timeScale: 1, duration: .6 }));
    return tween;
  });

  if (!reduceMotion) {
    ScrollTrigger.create({
      start: 0,
      end: 'max',
      onUpdate(self) {
        const boost = 1 + Math.min(Math.abs(self.getVelocity()) / 400, 4);
        marquees.forEach(t => gsap.to(t, { timeScale: boost, duration: .2, overwrite: true, onComplete: () => gsap.to(t, { timeScale: 1, duration: 1 }) }));
      }
    });
  }

  /* Work grid: one staggered reveal for the whole track (filtering can hide cards, so no per-card triggers) */
  gsap.from('.work__track > *', {
    autoAlpha: 0, y: 50, duration: .9, stagger: .1,
    scrollTrigger: { trigger: '.work__track', start: 'top 85%', once: true }
  });

  /* Industries: cards start stacked in the middle and fan out with scroll.
     GSAP takes over the transform here (it clears the CSS rotate/translate fallback), so the
     fan pose is read from each card's --r / --ty variables. */
  const mm = gsap.matchMedia();
  mm.add('(min-width: 768px) and (prefers-reduced-motion: no-preference)', () => {
    const fan = $('.fan');
    const cards = $$('.fan-card', fan);
    const mid = (cards.length - 1) / 2;
    const rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
    const cssVar = (el, name) => parseFloat(getComputedStyle(el).getPropertyValue(name)) || 0;
    gsap.fromTo(cards, {
      // offsetLeft ignores transforms, so this stays correct on refresh
      x: (i, card) => fan.offsetWidth / 2 - (card.offsetLeft + card.offsetWidth / 2),
      y: 0,
      rotation: i => (i - mid) * 3
    }, {
      x: 0,
      y: (i, card) => cssVar(card, '--ty') * rem,
      rotation: (i, card) => cssVar(card, '--r'),
      ease: 'none',
      scrollTrigger: { trigger: fan, start: 'top 90%', end: 'bottom 55%', scrub: 1, invalidateOnRefresh: true }
    });
  });

  /* Footer: wordmark rises into place; an accent spotlight follows the cursor through it */
  const footerBrand = $('.footer__brand');
  if (!reduceMotion) {
    gsap.from(footerBrand, {
      yPercent: 35, autoAlpha: .3, ease: 'none',
      scrollTrigger: { trigger: '.footer', start: 'top bottom', end: 'bottom bottom', scrub: true }
    });

    const spot = { x: 75, y: 55 };
    const paint = () => {
      footerBrand.style.setProperty('--mx', spot.x);
      footerBrand.style.setProperty('--my', spot.y);
    };
    if (finePointer) {
      const sx = gsap.quickTo(spot, 'x', { duration: .9, ease: 'power3', onUpdate: paint });
      const sy = gsap.quickTo(spot, 'y', { duration: .9, ease: 'power3', onUpdate: paint });
      $('.footer').addEventListener('mousemove', e => {
        const r = footerBrand.getBoundingClientRect();
        sx((e.clientX - r.left) / r.width * 100);
        sy((e.clientY - r.top) / r.height * 100);
      });
    } else {
      gsap.fromTo(spot, { x: 85 }, { x: 15, duration: 5, ease: 'sine.inOut', yoyo: true, repeat: -1, onUpdate: paint });
    }
  }

  /* Floating chat: always on (CSS), hidden only while the contact section is on screen */
  const chat = $('.float-chat');
  ScrollTrigger.create({ trigger: '#contact', start: 'top 80%', end: 'max', onToggle: self => chat.classList.toggle('is-hidden', self.isActive) });

  /* FAQ open/close changes page height — keep scroll positions accurate */
  $$('.faq .collapse').forEach(panel => {
    panel.addEventListener('shown.bs.collapse', () => ScrollTrigger.refresh());
    panel.addEventListener('hidden.bs.collapse', () => ScrollTrigger.refresh());
  });

  ScrollTrigger.sort();
  ScrollTrigger.refresh();
  window.addEventListener('load', () => ScrollTrigger.refresh());

  /* ------------------------------------------------------------------
     Helpers that don't depend on GSAP
  ------------------------------------------------------------------ */

  // Work filters (pills + industry fan cards). Animated with GSAP Flip when available, instant otherwise.
  function initWorkFilter() {
    const buttons = $$('[data-filter]');
    const items = $$('.work__track > *');
    if (!buttons.length) return;
    const canFlip = window.gsap && window.Flip && !reduceMotion;
    if (canFlip) gsap.registerPlugin(Flip);

    const setFilter = cat => {
      buttons.forEach(b => {
        const on = b.dataset.filter === cat;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-pressed', on);
      });
      const state = canFlip && Flip.getState(items);
      // cards without data-cat (the "your product could be next" card) always stay
      items.forEach(el => el.classList.toggle('is-filtered-out', cat !== 'all' && !!el.dataset.cat && el.dataset.cat !== cat));
      if (!canFlip) { window.ScrollTrigger?.refresh(); return; }
      Flip.from(state, {
        duration: .7, ease: 'power3.inOut', absolute: true, scale: true, stagger: .03,
        onEnter: els => gsap.fromTo(els, { autoAlpha: 0, scale: .9 }, { autoAlpha: 1, scale: 1, duration: .5, delay: .15 }),
        onLeave: els => gsap.to(els, { autoAlpha: 0, scale: .9, duration: .35 }),
        onComplete: () => ScrollTrigger.refresh()
      });
    };

    buttons.forEach(b => b.addEventListener('click', () => setFilter(b.dataset.filter)));
    $$('[data-filter-link]').forEach(a => a.addEventListener('click', () => setFilter(a.dataset.filterLink)));
  }

  // One modal for client photos (image), own video files (video) and YouTube links (youtube)
  function initMediaModal() {
    const modalEl = $('#mediaModal');
    if (!modalEl || !window.bootstrap) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const stage = $('.media-modal__stage', modalEl);
    const caption = $('.media-modal__caption', modalEl);

    $$('[data-media]').forEach(trigger => {
      trigger.addEventListener('click', () => {
        const { media, src } = trigger.dataset;
        let node;
        if (media === 'youtube') {
          node = Object.assign(document.createElement('iframe'), {
            src: `https://www.youtube-nocookie.com/embed/${encodeURIComponent(src)}?autoplay=1&rel=0`,
            title: trigger.dataset.caption || 'Client video review',
            allow: 'autoplay; encrypted-media; picture-in-picture',
            allowFullscreen: true
          });
        } else if (media === 'video') {
          node = Object.assign(document.createElement('video'), { src, controls: true, autoplay: true, playsInline: true });
        } else {
          node = Object.assign(document.createElement('img'), { src, alt: trigger.dataset.caption || '' });
        }
        stage.replaceChildren(node);
        caption.textContent = trigger.dataset.caption || '';
        modal.show();
      });
    });

    modalEl.addEventListener('show.bs.modal', () => lenis?.stop());
    modalEl.addEventListener('hidden.bs.modal', () => {
      stage.replaceChildren(); // stops any playing video
      lenis?.start();
    });
  }
  // Client-photo rail: prev/next buttons step one photo at a time, the bar shows how far along you are,
  // and each button disables at its end. Native horizontal scrolling (swipe, trackpad) keeps working.
  function initGallery() {
    const gallery = $('[data-gallery]');
    if (!gallery) return;
    const track = $('.gallery__track', gallery);
    const bar = $('.gallery__progress span', gallery);
    const prev = $('[data-gallery-prev]', gallery);
    const next = $('[data-gallery-next]', gallery);
    const step = () => {
      const item = $('.gallery__item', track);
      return item.offsetWidth + parseFloat(getComputedStyle(track).columnGap || 0);
    };
    const update = () => {
      const max = track.scrollWidth - track.clientWidth;
      const visible = track.clientWidth / track.scrollWidth;
      const p = max > 0 ? track.scrollLeft / max : 1;
      bar.parentElement.style.setProperty('--p', Math.min(1, visible + (1 - visible) * p));
      prev.disabled = track.scrollLeft <= 2;
      next.disabled = track.scrollLeft >= max - 2;
    };
    const behavior = reduceMotion ? 'auto' : 'smooth';
    prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior }));
    next.addEventListener('click', () => track.scrollBy({ left: step(), behavior }));
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  }

  // Review slider: one slide at a time, moved with translateX. Arrows, dots, swipe and ← → keys navigate;
  // it autoplays every 7s only while on screen and not hovered/focused (never with reduced motion).
  // Off-screen slides are inert so keyboard users only tab through the visible review.
  function initTestimonials() {
    const root = $('[data-tslider]');
    if (!root) return;
    const track = $('.tslider__track', root);
    const slides = $$('.tslider__slide', root);
    const dots = $$('.tslider__dot', root);
    const count = $('.tslider__count b', root);
    const pad = n => String(n).padStart(2, '0');
    let index = 0;
    let timer = null;
    let onScreen = false;
    let held = false; // hovered or focused
    let watching = false; // a video review is open in the media viewer

    const go = i => {
      index = (i + slides.length) % slides.length;
      track.style.transform = `translateX(${-index * 100}%)`;
      slides.forEach((s, n) => {
        const on = n === index;
        s.inert = !on;
        s.setAttribute('aria-hidden', String(!on));
      });
      dots.forEach((d, n) => d.setAttribute('aria-current', String(n === index)));
      count.textContent = pad(index + 1);
    };
    const stop = () => { clearInterval(timer); timer = null; };
    const sync = () => {
      stop();
      if (!reduceMotion && onScreen && !held && !watching) timer = setInterval(() => go(index + 1), 7000);
    };
    // any manual move restarts the 7s countdown
    const step = i => { go(i); sync(); };

    $('[data-tslider-prev]', root).addEventListener('click', () => step(index - 1));
    $('[data-tslider-next]', root).addEventListener('click', () => step(index + 1));
    dots.forEach((d, n) => d.addEventListener('click', () => step(n)));
    root.addEventListener('keydown', e => {
      if (e.key === 'ArrowLeft') step(index - 1);
      if (e.key === 'ArrowRight') step(index + 1);
    });

    let startX = null;
    const viewport = $('.tslider__viewport', root);
    viewport.addEventListener('pointerdown', e => { startX = e.clientX; });
    viewport.addEventListener('pointerup', e => {
      if (startX === null) return;
      const dx = e.clientX - startX;
      startX = null;
      if (Math.abs(dx) > 50) step(index + (dx < 0 ? 1 : -1));
    });

    const hold = on => { held = on; sync(); };
    root.addEventListener('mouseenter', () => hold(true));
    root.addEventListener('mouseleave', () => hold(false));
    root.addEventListener('focusin', () => hold(true));
    root.addEventListener('focusout', e => { if (!root.contains(e.relatedTarget)) hold(false); });
    new IntersectionObserver(([entry]) => { onScreen = entry.isIntersecting; sync(); }, { threshold: .4 }).observe(root);
    const modal = $('#mediaModal');
    modal?.addEventListener('show.bs.modal', () => { watching = true; sync(); });
    modal?.addEventListener('hidden.bs.modal', () => { watching = false; sync(); });

    go(0);
  }

  function initCopyButtons() {
    $$('[data-copy]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const text = btn.dataset.copy;
        try {
          await navigator.clipboard.writeText(text);
        } catch {
          const tmp = Object.assign(document.createElement('textarea'), { value: text });
          document.body.append(tmp);
          tmp.select();
          document.execCommand('copy');
          tmp.remove();
        }
        const lbl = $('span', btn);
        lbl.textContent = 'copied ✓';
        btn.classList.add('is-copied');
        setTimeout(() => { lbl.textContent = 'copy'; btn.classList.remove('is-copied'); }, 1800);
      });
    });
  }

  // Mobile menu: full-screen sheet (CSS keys off .menu-open); page scroll is locked while it's open.
  // Runs without GSAP too. `lenis` is read at event time, so it's whatever smooth-scroll instance exists by then.
  function initMobileMenu() {
    const menu = $('#mainNav');
    const siteNav = $('.site-nav');
    const lock = on => {
      siteNav.classList.toggle('menu-open', on);
      document.documentElement.classList.toggle('menu-locked', on);
      if (on) lenis?.stop(); else lenis?.start();
    };
    menu.addEventListener('show.bs.collapse', () => lock(true));
    menu.addEventListener('hide.bs.collapse', () => lock(false));
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && menu.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(menu).hide();
    });
  }

  // Front-end validation, then opens the visitor's mail app.
  // Swap for Formspree / your Laravel endpoint when ready.
  function initContactForm() {
    const form = $('.contact-form');
    if (!form) return;

    form.addEventListener('submit', e => {
      e.preventDefault();
      form.classList.add('was-validated');
      if (!form.checkValidity()) return;

      const data = new FormData(form);
      const subject = `New project inquiry — ${data.get('name')}`;
      const body = [
        `Name: ${data.get('name')}`,
        `Email: ${data.get('email')}`,
        `Phone: ${data.get('phone') || '—'}`,
        '',
        data.get('message')
      ].join('\n');
      window.location.href = `mailto:${form.dataset.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

      let note = $('.form-note', form);
      if (!note) {
        note = document.createElement('p');
        note.className = 'form-note mono';
        note.setAttribute('role', 'status');
        form.append(note);
      }
      note.textContent = '✓ Your email app should open with the message ready to send.';
    });
  }
})();

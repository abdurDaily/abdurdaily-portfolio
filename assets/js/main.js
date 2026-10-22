(() => {
  'use strict';

  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const sleep = ms => new Promise(r => setTimeout(r, ms));

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

  if (!window.gsap || !window.ScrollTrigger) {
    $('.preloader')?.remove();
    renderTerminalStatic();
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
     Terminal — types a real build & deploy flow, then loops
  ------------------------------------------------------------------ */
  const terminalScript = [
    { cmd: 'composer create-project laravel/laravel api' },
    { out: 'Creating a "laravel/laravel" project…', cls: 't-dim' },
    { out: '✓ Laravel API ready', cls: 't-ok', wait: 500 },
    { cmd: 'npx create-next-app@latest web --ts' },
    { out: '✓ Next.js front-end ready', cls: 't-ok', wait: 500 },
    { cmd: 'php artisan test' },
    { out: '✓ All tests passed', cls: 't-ok', wait: 500 },
    { cmd: 'git push origin main' },
    { out: '→ CI pipeline running…', cls: 't-dim', wait: 700 },
    { out: '✓ Deployed to production 🚀', cls: 't-ok' }
  ];
  const promptHTML = '<span class="t-path">~/new-project</span> <span class="t-branch">git:(main)</span> <span class="t-prompt">❯</span> ';
  const esc = s => s.replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

  function renderTerminalStatic() {
    $('.js-terminal').innerHTML = terminalScript
      .map(s => (s.cmd ? promptHTML + esc(s.cmd) : `<span class="${s.cls}">${esc(s.out)}</span>`))
      .join('\n');
  }

  async function runTerminal() {
    const el = $('.js-terminal');
    for (;;) {
      let html = '';
      el.innerHTML = '';
      for (const step of terminalScript) {
        if (step.cmd) {
          html += promptHTML;
          for (let i = 1; i <= step.cmd.length; i++) {
            el.innerHTML = html + esc(step.cmd.slice(0, i));
            await sleep(26 + Math.random() * 45);
          }
          html += esc(step.cmd) + '\n';
          await sleep(380);
        } else {
          html += `<span class="${step.cls}">${esc(step.out)}</span>\n`;
          el.innerHTML = html;
          await sleep(step.wait || 280);
        }
      }
      await sleep(5000);
    }
  }

  /* ------------------------------------------------------------------
     Preloader + hero intro
  ------------------------------------------------------------------ */
  const heroWords = splitWords($('[data-hero-split]'));
  gsap.set(heroWords, { yPercent: 115 });
  gsap.set(['.hero__top', '.hero__kicker', '.hero__sub', '.hero__cta', '.hero__trust'], { autoAlpha: 0, y: 24 });
  gsap.set('.hero__glow', { autoAlpha: 0, scale: .6 });
  const heroHl = $$('.hero__title .hl');
  gsap.set(heroHl, { backgroundSize: '0% 30%' });
  gsap.set('.hero-photo', { clipPath: 'circle(0% at 50% 50%)' });
  gsap.set('.orbit__ring', { autoAlpha: 0, scale: .7 });
  gsap.set('.orbit__icon', { autoAlpha: 0 }); // opacity only — their transform belongs to the CSS orbit
  gsap.set(['.hero-tag', '.hero-badge'], { autoAlpha: 0, scale: .6 });

  function heroIntro() {
    return gsap.timeline()
      .to('.hero__top', { autoAlpha: 1, y: 0, duration: .8 })
      .to('.hero__kicker', { autoAlpha: 1, y: 0, duration: .8 }, '-=.6')
      .to(heroWords, { yPercent: 0, duration: 1.1, stagger: .045, ease: 'power4.out' }, '-=.6')
      .to(heroHl, { backgroundSize: '100% 30%', duration: .9, ease: 'power3.inOut' }, '-=.3')
      .to('.hero__sub', { autoAlpha: 1, y: 0 }, '-=1')
      .to('.hero__cta', { autoAlpha: 1, y: 0 }, '-=.85')
      .to('.hero__trust', { autoAlpha: 1, y: 0 }, '-=.85')
      .from('.avatars span', { x: -14, autoAlpha: 0, duration: .6, stagger: .07 }, '<')
      // portrait: circular iris-open, then the orbit draws in around it
      .to('.hero-photo', { clipPath: 'circle(50% at 50% 50%)', duration: 1.4, ease: 'power4.inOut', clearProps: 'clipPath' }, 0.2)
      .from('.hero-photo img', { scale: 1.35, duration: 1.8, ease: 'power3.out' }, '<')
      .to('.orbit__ring', { autoAlpha: 1, scale: 1, duration: 1.2, stagger: .12 }, '<.5')
      .to('.orbit__icon', { autoAlpha: 1, duration: .5, stagger: .08 }, '<.3')
      .to(['.hero-tag', '.hero-badge'], { autoAlpha: 1, scale: 1, duration: .8, stagger: .15, ease: 'back.out(1.8)' }, '<.2')
      .to('.hero__glow', { autoAlpha: 1, scale: 1, duration: 1.6, ease: 'power2.out' }, '<-.6')
      .add(() => {
        if (reduceMotion) return;
        gsap.to('.hero-tag', { y: -10, duration: 2.6, ease: 'sine.inOut', yoyo: true, repeat: -1 });
        gsap.to('.hero-badge', { y: 10, duration: 3.2, ease: 'sine.inOut', yoyo: true, repeat: -1 });
      })
      .from('.float-chat', { scale: 0, autoAlpha: 0, duration: .8, ease: 'back.out(2.2)', clearProps: 'transform,opacity,visibility' }, '-=.6');
  }

  // the accent glow, portrait and orbit drift gently (in layers) toward the pointer while it's over the hero
  if (finePointer && !reduceMotion) {
    const hero = $('.hero');
    const layers = [['.hero__glow', .08], ['.hero-photo', .02], ['.orbit', -.015]].map(([sel, k]) => ({
      k, x: gsap.quickTo(sel, 'x', { duration: 1.4, ease: 'power3' }), y: gsap.quickTo(sel, 'y', { duration: 1.4, ease: 'power3' })
    }));
    hero.addEventListener('mousemove', e => {
      const r = hero.getBoundingClientRect();
      const dx = e.clientX - r.left - r.width / 2;
      const dy = e.clientY - r.top - r.height / 2;
      layers.forEach(l => { l.x(dx * l.k); l.y(dy * l.k); });
    });
    hero.addEventListener('mouseleave', () => layers.forEach(l => { l.x(0); l.y(0); }));
  }

  // the terminal (now in About) starts typing when it scrolls into view
  ScrollTrigger.create({
    trigger: '.terminal', start: 'top 85%', once: true,
    onEnter: () => (reduceMotion ? renderTerminalStatic() : runTerminal())
  });

  // lifecycle steps light up one after another
  gsap.from('.flow li', {
    autoAlpha: 0, x: -14, duration: .5, stagger: .12,
    scrollTrigger: { trigger: '.flow', start: 'top 90%', once: true }
  });

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
      const interactive = e.target.closest('a, button, .stack-item, .brand-logo, .service-row');
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

  $$('[data-count]').forEach(el => {
    const obj = { v: 0 };
    gsap.to(obj, {
      v: +el.dataset.count, duration: 2, ease: 'power2.out',
      scrollTrigger: { trigger: el, start: 'top 90%', once: true },
      onUpdate: () => { el.textContent = Math.round(obj.v); }
    });
  });

  gsap.from('.stack-item', {
    autoAlpha: 0, y: 40, scale: .92, duration: .8, stagger: { each: .05, from: 'random' },
    scrollTrigger: { trigger: '.stack-grid', start: 'top 85%', once: true }
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

  /* Mission: each word lights up as the paragraph scrolls through the viewport */
  $$('[data-scrub-words]').forEach(el => {
    const words = splitWords(el);
    if (reduceMotion) return;
    gsap.fromTo(words, { opacity: .14 }, {
      opacity: 1, ease: 'none', stagger: .1,
      scrollTrigger: { trigger: el, start: 'top 80%', end: 'bottom 45%', scrub: .6 }
    });
  });

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

    // Pricing "Get a quote" buttons start the message for the visitor — unless they've already written their own
    const msg = $('#cMsg');
    $$('[data-plan]').forEach(btn => btn.addEventListener('click', () => {
      if (msg.value.trim() && msg.value !== msg.dataset.prefill) return;
      msg.value = msg.dataset.prefill = `Hi Abdur, I'm interested in the ${btn.dataset.plan} package. `;
      setTimeout(() => msg.focus({ preventScroll: true }), 1200);
    }));

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

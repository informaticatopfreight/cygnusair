/* Animaciones de la portada. Generado desde propuesta/src/index.html con tools/sync-theme-assets.py */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  // El comunicado de emergencia lo prepara crisis.js; si está activo, la web va en modo sereno.
  const crisis = document.documentElement.classList.contains('is-crisis');

  /* ---------- partir texto en letras o palabras (respeta etiquetas internas) ---------- */
  function splitInto(el, mode) {
    const walk = node => [...node.childNodes].forEach(n => {
      if (n.nodeType === 3) {
        const frag = document.createDocumentFragment();
        n.textContent.split(/(\s+)/).forEach(part => {
          if (!part) return;
          if (/^\s+$/.test(part)) { frag.append(document.createTextNode(' ')); return; }
          const w = document.createElement('span');
          if (mode === 'words') { w.className = 'word'; w.textContent = part; }
          else { w.className = 'w'; [...part].forEach(ch => { const c = document.createElement('span'); c.className = 'c'; c.textContent = ch; w.append(c); }); }
          frag.append(w);
        });
        n.replaceWith(frag);
      } else if (n.nodeType === 1) walk(n);
    });
    walk(el);
    return $$(mode === 'words' ? '.word' : '.c', el);
  }
  const split = el => splitInto(el, 'chars');

  /* ---------- cielo estrellado sobre la foto ---------- */
  const sky = $('#sky'), ctx = sky.getContext('2d');
  let stars = [], W = 0, H = 0, mx = 0, my = 0, scrollY = 0, shoot = null;
  function resize() {
    const dpr = Math.min(devicePixelRatio || 1, 2);
    W = sky.clientWidth; H = sky.clientHeight;
    sky.width = W * dpr; sky.height = H * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    stars = Array.from({ length: Math.round(W * H / 2600) }, () => ({ x: Math.random() * W, y: Math.random() * H, r: Math.random() * 1.1 + .2, d: Math.random() * .9 + .1, p: Math.random() * 6.28 }));
  }
  resize(); addEventListener('resize', resize);
  addEventListener('pointermove', e => { mx = e.clientX / innerWidth - .5; my = e.clientY / innerHeight - .5; });
  function drawSky(t) {
    ctx.clearRect(0, 0, W, H);
    for (const s of stars) {
      const tw = reduce ? .8 : .55 + .45 * Math.sin(t / 900 * s.d + s.p);
      const x = s.x - mx * 30 * s.d, y = s.y - my * 30 * s.d - scrollY * .25 * s.d;
      ctx.globalAlpha = tw * s.d * (1 - (((y % H) + H) % H) / H * .6); ctx.fillStyle = '#EAF0FF';
      ctx.beginPath(); ctx.arc(x, ((y % H) + H) % H, s.r, 0, 6.283); ctx.fill();
    }
    if (!reduce) {
      if (!shoot && Math.random() < .004) shoot = { x: Math.random() * W * .7, y: Math.random() * H * .5, l: 0 };
      if (shoot) {
        shoot.l += 14; const g = ctx.createLinearGradient(shoot.x, shoot.y, shoot.x + shoot.l, shoot.y + shoot.l * .35);
        g.addColorStop(0, 'rgba(234,240,255,0)'); g.addColorStop(1, 'rgba(234,240,255,.85)');
        ctx.globalAlpha = Math.max(0, 1 - shoot.l / 420); ctx.strokeStyle = g; ctx.lineWidth = 1.2;
        ctx.beginPath(); ctx.moveTo(shoot.x, shoot.y); ctx.lineTo(shoot.x + shoot.l, shoot.y + shoot.l * .35); ctx.stroke();
        if (shoot.l > 420) shoot = null;
      }
    }
    ctx.globalAlpha = 1;
  }

  /* ---------- cabecera con fondo al bajar ---------- */
  const header = $('.header');
  addEventListener('scroll', () => header.classList.toggle('is-scrolled', window.scrollY > 40), { passive: true });

  if (reduce || crisis || !window.gsap) {
    // web quieta: todo visible y con sus cifras finales
    $$('[data-count]').forEach(el => {
      const dec = +(el.dataset.dec || 0);
      el.textContent = 'nogroup' in el.dataset ? el.dataset.count : (+el.dataset.count).toLocaleString('es-ES', { minimumFractionDigits: dec, maximumFractionDigits: dec });
    });
    drawSky(0);
    $('.preloader') && $('.preloader').remove();
    return;
  }

  gsap.registerPlugin(ScrollTrigger, MotionPathPlugin);

  /* ---------- scroll suave ---------- */
  const lenis = new Lenis({ lerp: .09, smoothWheel: true });
  lenis.stop();
  let velocity = 0;
  lenis.on('scroll', e => { ScrollTrigger.update(); velocity = e.velocity; scrollY = e.scroll; });
  gsap.ticker.add(t => { lenis.raf(t * 1000); drawSky(t * 1000); });
  gsap.ticker.lagSmoothing(0);
  $$('a[href^="#"]:not([data-legal])').forEach(a => a.addEventListener('click', e => {
    const id = a.getAttribute('href'); if (id.length < 2 && id !== '#top') return;
    e.preventDefault(); lenis.scrollTo(id === '#top' ? 0 : id, { duration: 1.6, offset: id === '#flota' ? 0 : -70 });
  }));

  /* ---------- cursor y botón magnético ---------- */
  const cur = $('.cursor');
  const cx = gsap.quickTo(cur, 'x', { duration: .35, ease: 'power3' }), cy = gsap.quickTo(cur, 'y', { duration: .35, ease: 'power3' });
  addEventListener('pointermove', e => { cur.style.opacity = 1; cx(e.clientX); cy(e.clientY); });
  $$('[data-hover]').forEach(el => { el.addEventListener('pointerenter', () => cur.classList.add('is-hover')); el.addEventListener('pointerleave', () => cur.classList.remove('is-hover')); });
  $$('.magnet').forEach(m => {
    const xTo = gsap.quickTo(m, 'x', { duration: .6, ease: 'elastic.out(1,.4)' }), yTo = gsap.quickTo(m, 'y', { duration: .6, ease: 'elastic.out(1,.4)' });
    m.addEventListener('pointermove', e => { const r = m.getBoundingClientRect(); xTo((e.clientX - r.left - r.width / 2) * .45); yTo((e.clientY - r.top - r.height / 2) * .45); });
    m.addEventListener('pointerleave', () => { xTo(0); yTo(0); });
  });

  /* ---------- aviones que vuelan solos, en bucle, solo mientras su sección se ve ---------- */
  const whileVisible = (trigger, tl) => ScrollTrigger.create({ trigger, start: 'top bottom', end: 'bottom top', onToggle: st => st.isActive ? tl.play() : tl.pause() });
  const autoFly = (layer, o = {}) => {
    const jet = $('.fly-jet', layer), path = $('.fly-trail', layer), L = path.getTotalLength(), d = o.duration || 10, ease = o.ease || 'none';
    gsap.set(jet, { autoAlpha: 0 });
    const tl = gsap.timeline({ repeat: -1, repeatDelay: o.gap ?? 2, delay: o.delay || 0, paused: true })
      .set(path, { strokeDasharray: L, strokeDashoffset: L, opacity: 1 }, 0)
      .set(jet, { autoAlpha: 1 }, 0)
      .to(jet, { motionPath: { path, align: path, alignOrigin: [.5, .5], autoRotate: true }, duration: d, ease }, 0)
      .to(path, { strokeDashoffset: 0, duration: d, ease }, 0)
      .to([path, jet], { autoAlpha: 0, duration: 1.2 }, '>-0.2');
    whileVisible(o.trigger || layer.parentNode, tl);
    return tl;
  };

  /* ---------- preparar la entrada ---------- */
  const preChars = split($('.preloader__word'));
  const heroLines = $$('.hero__title .line').map(split);
  gsap.set(preChars, { yPercent: 110 });
  gsap.set(heroLines.flat(), { yPercent: 110 });
  gsap.set('[data-fade]', { autoAlpha: 0, y: 24 });
  gsap.set('.hero__photo img', { scale: 1.3, autoAlpha: 0 });
  const cLines = $$('.c-lines line');
  cLines.forEach(l => { const len = Math.hypot(l.x2.baseVal.value - l.x1.baseVal.value, l.y2.baseVal.value - l.y1.baseVal.value); gsap.set(l, { strokeDasharray: len, strokeDashoffset: len }); });
  gsap.set('.c-stars circle', { scale: 0, transformOrigin: '50% 50%' });
  gsap.set('.c-names text', { autoAlpha: 0 });

  const counter = { v: 0 };
  gsap.timeline({ defaults: { ease: 'expo.out' } })
    .to(preChars, { yPercent: 0, duration: 1, stagger: .045 })
    .to(counter, { v: 100, duration: 1.4, ease: 'power2.inOut', onUpdate: () => $('.js-count').textContent = String(Math.round(counter.v)).padStart(3, '0') }, 0)
    .to('.preloader__bar', { scaleX: 1, duration: 1.4, ease: 'power2.inOut' }, 0)
    .to(preChars, { yPercent: -110, duration: .7, stagger: .02, ease: 'expo.in' }, 1.55)
    .to('.preloader', { yPercent: -100, duration: 1.1, ease: 'expo.inOut' }, 2.05)
    .add('hero', 2.45)
    .to('.hero__photo img', { scale: 1.06, autoAlpha: 1, duration: 2.4, ease: 'expo.out' }, 'hero')
    .to(heroLines[0], { yPercent: 0, duration: 1.3, stagger: .03 }, 'hero+=.2')
    .to(heroLines[1], { yPercent: 0, duration: 1.3, stagger: .03 }, 'hero+=.35')
    .to('.c-stars circle', { scale: 1, duration: .8, stagger: .08, ease: 'back.out(3)' }, 'hero+=.4')
    .to(cLines, { strokeDashoffset: 0, duration: 1.2, stagger: .12, ease: 'power2.inOut' }, 'hero+=.7')
    .to('.c-names text', { autoAlpha: 1, duration: .8, stagger: .1 }, 'hero+=1.6')
    .to('.hero [data-fade]', { autoAlpha: 1, y: 0, duration: 1, stagger: .1 }, 'hero+=.8')
    .add(() => { lenis.start(); $('.preloader').remove(); });
  autoFly($('#fly-hero'), { duration: 11, gap: 3, delay: 3.2 });

  // al bajar: la foto se hunde, el titular se separa y la constelación gira
  const heroST = { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true };
  gsap.to('.hero__photo', { yPercent: 22, ease: 'none', scrollTrigger: heroST });
  gsap.to('.hero__title .line:first-child', { xPercent: -12, ease: 'none', scrollTrigger: heroST });
  gsap.to('.hero__title .line:last-of-type', { xPercent: 10, ease: 'none', scrollTrigger: heroST });
  gsap.to('.constellation', { yPercent: 30, rotate: -12, ease: 'none', scrollTrigger: heroST });

  /* ---------- cinta que acelera con la velocidad del scroll ---------- */
  const track = $('.marquee__track'), group = $('.marquee__group');
  for (let i = 0; i < 2; i++) track.append(group.cloneNode(true));
  let mxPos = 0, skew = 0;
  gsap.ticker.add(() => {
    const gw = group.offsetWidth;
    mxPos -= 1.2 + Math.abs(velocity) * .6;
    if (mxPos <= -gw) mxPos += gw;
    skew += (gsap.utils.clamp(-12, 12, velocity * -.8) - skew) * .1;
    track.style.transform = `translate3d(${mxPos}px,0,0) skewX(${skew}deg)`;
  });

  /* ---------- títulos letra a letra y textos que aparecen ---------- */
  $$('[data-split-scroll]').forEach(el => {
    const chars = split(el);
    gsap.from(chars, { yPercent: 110, rotate: 6, duration: 1.1, ease: 'expo.out', stagger: .03, scrollTrigger: { trigger: el, start: 'top 88%' } });
  });
  $$('[data-fade]').forEach(el => {
    if (el.closest('.hero')) return;
    gsap.to(el, { autoAlpha: 1, y: 0, duration: 1, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 90%' } });
  });

  /* ---------- párrafo que se ilumina palabra a palabra ---------- */
  gsap.to(splitInto($('[data-words]'), 'words'), { opacity: 1, stagger: .1, ease: 'none', scrollTrigger: { trigger: '.about__text', start: 'top 80%', end: 'bottom 45%', scrub: true } });

  /* ---------- contadores (formato español) ---------- */
  const fmt = (v, dec, nogroup) => nogroup ? String(Math.round(v)) : v.toLocaleString('es-ES', { minimumFractionDigits: dec, maximumFractionDigits: dec });
  const countUp = (el, opts = {}) => {
    const o = { v: 0 }, dec = +(el.dataset.dec || 0), nog = 'nogroup' in el.dataset;
    return gsap.to(o, { v: +el.dataset.count, duration: 2, ease: 'power3.out', ...opts, onUpdate: () => el.textContent = fmt(o.v, dec, nog) });
  };
  $$('[data-count]').forEach(el => el.textContent = fmt(0, +(el.dataset.dec || 0), 'nogroup' in el.dataset));
  $$('.stats [data-count]').forEach(el => countUp(el, { scrollTrigger: { trigger: el, start: 'top 92%' } }));

  /* ---------- certificaciones: las tarjetas entran girando, dibujan su icono y se inclinan con el ratón ---------- */
  gsap.from('.lic', { y: 90, rotationX: -40, autoAlpha: 0, transformOrigin: '50% 100%', duration: 1.3, stagger: .1, ease: 'expo.out', scrollTrigger: { trigger: '.lics', start: 'top 85%' } });
  $$('.lic__ico path, .lic__ico circle').forEach(el => {
    const L = el.getTotalLength();
    if (el.getAttribute('stroke-dasharray')) return;
    gsap.fromTo(el, { strokeDasharray: L, strokeDashoffset: L }, { strokeDashoffset: 0, duration: 1.6, delay: .4, ease: 'power2.inOut', scrollTrigger: { trigger: el.closest('.lic'), start: 'top 88%' } });
  });
  if (matchMedia('(hover: hover)').matches) $$('.lic').forEach(c => {
    const rx = gsap.quickTo(c, 'rotationX', { duration: .6, ease: 'power3' }), ry = gsap.quickTo(c, 'rotationY', { duration: .6, ease: 'power3' });
    c.addEventListener('pointermove', e => {
      const r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
      rx((.5 - y) * 12); ry((x - .5) * 14);
      c.style.setProperty('--mx', (x * 100).toFixed(1) + '%'); c.style.setProperty('--my', (y * 100).toFixed(1) + '%');
    });
    c.addEventListener('pointerleave', () => { rx(0); ry(0); });
  });

  /* ---------- servicios: el avión recorre solo las cinco fases y va iluminando cada servicio ---------- */
  const prof = $('#svc-profile'), route = $('.svc__ghost', prof), sTrail = $('.svc__trail', prof), sJet = $('.fly-jet', prof);
  const RL = route.getTotalLength(), svcItems = $$('.svc__item');
  const alt = $('.js-alt'), spd = $('.js-spd'), hdg = $('.js-hdg');
  // punto de paso de cada fase, bajo el centro de su columna (x = 100, 300, 500, 700, 900)
  const wp = [100, 300, 500, 700, 900].map(x => { let lo = 0, hi = RL; for (let k = 0; k < 30; k++) { const m = (lo + hi) / 2; route.getPointAtLength(m).x < x ? lo = m : hi = m; } return lo; });
  wp.forEach(l => {
    const pt = route.getPointAtLength(l), c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    c.setAttribute('cx', pt.x); c.setAttribute('cy', pt.y); c.setAttribute('r', 6); c.setAttribute('class', 'svc__dot');
    $('.svc__dots', prof).append(c);
  });
  const svcDots = $$('.svc__dot', prof);
  let phase = -1;
  const setPhase = i => {
    if (i === phase) return; phase = i;
    svcItems.forEach((el, k) => el.classList.toggle('on', k === i));
    svcDots.forEach((d, k) => d.classList.toggle('on', k <= i));
  };
  const hud = p => {
    const a = p < .3 ? p / .3 : p > .7 ? (1 - p) / .3 : 1;
    alt.textContent = String(Math.round(a * 35000)).padStart(5, '0').replace(/(\d{2})(\d{3})/, '$1 $2') + ' FT';
    spd.textContent = String(Math.round(140 + a * 330)).padStart(3, '0') + ' KT';
    hdg.textContent = String(Math.round(80 + Math.sin(p * 9) * 25)).padStart(3, '0') + '°';
  };
  gsap.set(sTrail, { strokeDasharray: RL, strokeDashoffset: RL });
  whileVisible('.svc', gsap.timeline({ repeat: -1, repeatDelay: 1.2, paused: true })
    .set(sTrail, { strokeDashoffset: RL, autoAlpha: 1 }, 0)
    .set(sJet, { autoAlpha: 1 }, 0)
    .to(sJet, { motionPath: { path: route, align: route, alignOrigin: [.5, .5], autoRotate: true }, duration: 14, ease: 'none',
      onUpdate() { const p = this.progress(), l = p * RL; let i = 0; wp.forEach((w, k) => { if (l >= w - RL * .05) i = k; }); setPhase(i); hud(p); } }, 0)
    .to(sTrail, { strokeDashoffset: 0, duration: 14, ease: 'none' }, 0)
    .to([sTrail, sJet], { autoAlpha: 0, duration: 1 }, '>-0.1'));
  setPhase(0);
  gsap.from(svcItems, { y: 40, autoAlpha: 0, duration: 1, stagger: .08, ease: 'expo.out', scrollTrigger: { trigger: '.svc__list', start: 'top 88%' } });

  /* ---------- destinos: el mapa se abre desde Madrid ---------- */
  gsap.to('.dest__map', { clipPath: 'circle(75% at 52% 46%)', ease: 'none', scrollTrigger: { trigger: '.dest', start: 'top 75%', end: 'center 55%', scrub: 1 } });
  gsap.fromTo('.dest__layer', { scale: 1.25 }, { scale: 1, ease: 'none', scrollTrigger: { trigger: '.dest', start: 'top bottom', end: 'bottom top', scrub: true } });

  const MAD = [835, 515], DESTS = [[850, 250], [1000, 55], [1215, 520], [715, 615], [980, 740], [1180, 640]];
  const NS = 'http://www.w3.org/2000/svg', routesG = $('#fly-map .routes'), mapJet = $('#fly-map .fly-jet');
  gsap.set(mapJet, { autoAlpha: 0 });
  const mapTl = gsap.timeline({ repeat: -1, repeatDelay: 1, paused: true });
  DESTS.forEach(([x, y], i) => {
    const cx = (MAD[0] + x) / 2, cy = (MAD[1] + y) / 2 - Math.hypot(x - MAD[0], y - MAD[1]) * .35;
    const path = document.createElementNS(NS, 'path'); path.setAttribute('d', `M${MAD[0]} ${MAD[1]} Q${cx} ${cy} ${x} ${y}`); path.setAttribute('class', 'map-route');
    const dot = document.createElementNS(NS, 'circle'); dot.setAttribute('cx', x); dot.setAttribute('cy', y); dot.setAttribute('r', 10); dot.setAttribute('class', 'map-dot');
    routesG.append(path, dot);
    const L = path.getTotalLength(), t = i * 3;
    gsap.set(path, { strokeDasharray: L, strokeDashoffset: L }); gsap.set(dot, { scale: 0, transformOrigin: '50% 50%' });
    mapTl.set(mapJet, { autoAlpha: 1 }, t)
      .to(mapJet, { motionPath: { path, align: path, alignOrigin: [.5, .5], autoRotate: true }, duration: 2.5, ease: 'power1.inOut' }, t)
      .to(path, { strokeDashoffset: 0, duration: 2.5, ease: 'power1.inOut' }, t)
      .to(dot, { scale: 1, duration: .5, ease: 'back.out(3)' }, t + 2.4)
      .set(mapJet, { autoAlpha: 0 }, t + 2.6);
  });
  mapTl.to('#fly-map .map-route, #fly-map .map-dot', { opacity: 0, duration: .8 }, '+=1.5');
  whileVisible('.dest', mapTl);

  /* ---------- flota en horizontal (escritorio) ---------- */
  const mm = gsap.matchMedia();
  mm.add('(min-width: 900px)', () => {
    const fleet = $('.fleet'), ftrack = $('.fleet__track');
    const dist = () => ftrack.scrollWidth - innerWidth;
    const setH = () => fleet.style.height = (dist() + innerHeight) + 'px';
    setH(); ScrollTrigger.addEventListener('refreshInit', setH);
    const tween = gsap.to(ftrack, { x: () => -dist(), ease: 'none', scrollTrigger: { trigger: fleet, start: 'top top', end: () => '+=' + dist(), scrub: true, invalidateOnRefresh: true } });
    $$('.fleet .pic').forEach(p => {
      const panel = p.closest('.panel');
      gsap.fromTo(p, { clipPath: 'inset(0 0 0 100%)' }, { clipPath: 'inset(0 0 0 0%)', ease: 'none', scrollTrigger: { trigger: panel, containerAnimation: tween, start: 'left 85%', end: 'left 15%', scrub: true } });
      gsap.fromTo($('img', p), { xPercent: 12, scale: 1.2 }, { xPercent: -6, scale: 1, ease: 'none', scrollTrigger: { trigger: p, containerAnimation: tween, start: 'left right', end: 'right left', scrub: true } });
    });
    $$('.panel--plane').forEach(panel => {
      const st = { trigger: panel, containerAnimation: tween, start: 'left 55%' };
      $$('dl > div, .engines, .btn', panel).forEach((el, i) => gsap.from(el, { y: 40, autoAlpha: 0, duration: .9, delay: i * .05, ease: 'expo.out', scrollTrigger: st }));
      $$('.specs [data-count]', panel).forEach((el, i) => countUp(el, { delay: i * .05, scrollTrigger: st }));
    });
    return () => { fleet.style.height = ''; ScrollTrigger.removeEventListener('refreshInit', setH); };
  });
  mm.add('(max-width: 899px)', () => {
    $$('.specs [data-count]').forEach(el => countUp(el, { scrollTrigger: { trigger: el, start: 'top 92%' } }));
  });
  $$('.speedlines').forEach(box => {
    for (let i = 0; i < 14; i++) {
      const l = document.createElement('i'); box.append(l);
      gsap.set(l, { top: gsap.utils.random(5, 95) + '%', width: gsap.utils.random(80, 320) });
      gsap.fromTo(l, { x: innerWidth * 1.1 }, { x: -400, duration: gsap.utils.random(.6, 1.4), repeat: -1, delay: gsap.utils.random(0, 2), ease: 'none' });
    }
  });

  /* ---------- galería: cortina + parallax ---------- */
  $$('.tile').forEach(t => {
    gsap.to(t, { clipPath: 'inset(0% 0 0 0)', duration: 1.4, ease: 'expo.inOut', scrollTrigger: { trigger: t, start: 'top 88%' } });
    gsap.fromTo($('img', t), { yPercent: -6, scale: 1.12 }, { yPercent: 6, scale: 1, ease: 'none', scrollTrigger: { trigger: t, start: 'top bottom', end: 'bottom top', scrub: true } });
  });

  /* ---------- contacto: el avión despega ---------- */
  autoFly($('#fly-cta'), { duration: 7, gap: 2, ease: 'power1.in' });

  addEventListener('load', () => ScrollTrigger.refresh());
  document.fonts && document.fonts.ready.then(() => ScrollTrigger.refresh());
})();

#!/usr/bin/env python3
"""Pasa a la carpeta del tema (cygnusair/) lo que sale de la propuesta:

- CSS de propuesta/src/index.html      -> cygnusair/assets/css/main.css
- JavaScript de la propuesta           -> cygnusair/assets/js/main.js (sin el comunicado ni el panel legal,
                                          que en WordPress van aparte)
- Textos legales (propuesta/src/legal.html) -> cygnusair/inc/legal/*.html
- Fotos (propuesta/img)                -> cygnusair/assets/img
- GSAP y Lenis (node_modules)          -> cygnusair/assets/js/vendor

Uso:  npm install && python3 tools/sync-theme-assets.py
"""
import re
import shutil
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / 'propuesta' / 'src' / 'index.html'
LEGAL = ROOT / 'propuesta' / 'src' / 'legal.html'
THEME = ROOT / 'cygnusair'
LIBS = Path(sys.argv[1]) if len(sys.argv) > 1 else ROOT / 'node_modules'

html = SRC.read_text(encoding='utf-8')


def cut(text, start, end):
    i = text.index(start)
    j = text.index(end, i)
    return text[:i] + text[j:]


def must_replace(text, old, new):
    assert text.count(old) == 1, (text.count(old), old[:70])
    return text.replace(old, new)


# ---------- CSS ----------
css = html[html.index('<style>') + len('<style>'):html.index('</style>')].strip('\n')
css = css.replace('.panel--757', '.panel--plane')
(THEME / 'assets' / 'css').mkdir(parents=True, exist_ok=True)
(THEME / 'assets' / 'css' / 'main.css').write_text(
    '/* Generado desde propuesta/src/index.html con tools/sync-theme-assets.py */\n' + css + '\n', encoding='utf-8')

# ---------- JavaScript ----------
js = html[html.index('<script>\n(() => {') + len('<script>\n'):html.index('})();\n</script>') + len('})();')]

# El comunicado lo gestiona assets/js/crisis.js (se carga en todas las páginas).
i = js.index('  /* =====================================================================\n     COMUNICADO DE EMERGENCIA')
j = js.index('  /* ---------- partir texto en letras o palabras')
js = js[:i] + "  // El comunicado de emergencia lo prepara crisis.js; si está activo, la web va en modo sereno.\n  const crisis = document.documentElement.classList.contains('is-crisis');\n\n" + js[j:]

# En WordPress los textos legales son páginas normales, no un panel.
js = cut(js, '  /* ---------- textos legales en un panel', '  /* ---------- cabecera con fondo al bajar')
js = must_replace(js, '  lenisRef = lenis;\n', '')
js = must_replace(js, "if (legal.hidden) lenis.start();", 'lenis.start();')

# La flota puede tener varios aviones: cada panel anima lo suyo.
js = must_replace(js, "  const specsIn = (trigger, extra) => $$('.specs [data-count]').forEach((el, i) => countUp(el, { delay: i * .05, scrollTrigger: { trigger, start: 'left 70%', ...extra } }));\n", '')
js = must_replace(js, """    $$('.panel--757 dl > div, .panel--757 .engines, .panel--757 .btn').forEach((el, i) => gsap.from(el, { y: 40, autoAlpha: 0, duration: .9, delay: i * .05, ease: 'expo.out', scrollTrigger: { trigger: '.panel--757', containerAnimation: tween, start: 'left 55%' } }));
    specsIn('.panel--757', { containerAnimation: tween, start: 'left 55%' });
""", """    $$('.panel--plane').forEach(panel => {
      const st = { trigger: panel, containerAnimation: tween, start: 'left 55%' };
      $$('dl > div, .engines, .btn', panel).forEach((el, i) => gsap.from(el, { y: 40, autoAlpha: 0, duration: .9, delay: i * .05, ease: 'expo.out', scrollTrigger: st }));
      $$('.specs [data-count]', panel).forEach((el, i) => countUp(el, { delay: i * .05, scrollTrigger: st }));
    });
""")

# WordPress escribe las cifras finales en el HTML; con animaciones se ponen a cero para contarlas.
js = must_replace(js, "  $$('.stats [data-count]').forEach(el => countUp(el,",
                  "  $$('[data-count]').forEach(el => el.textContent = fmt(0, +(el.dataset.dec || 0), 'nogroup' in el.dataset));\n  $$('.stats [data-count]').forEach(el => countUp(el,")

assert 'openLegal' not in js and 'COMUNICADO' not in js and 'panel--757' not in js
(THEME / 'assets' / 'js').mkdir(parents=True, exist_ok=True)
(THEME / 'assets' / 'js' / 'main.js').write_text(
    '/* Animaciones de la portada. Generado desde propuesta/src/index.html con tools/sync-theme-assets.py */\n' + js + '\n',
    encoding='utf-8')

# ---------- Textos legales ----------
legal = LEGAL.read_text(encoding='utf-8')
links = {'aviso-legal': '{{aviso-legal}}', 'privacidad': '{{privacidad}}', 'cookies': '{{cookies}}'}
out = THEME / 'inc' / 'legal'
out.mkdir(parents=True, exist_ok=True)
for doc, fname in [('aviso-legal', 'aviso-legal.html'), ('privacidad', 'politica-de-privacidad.html'), ('cookies', 'politica-de-cookies.html')]:
    m = re.search(r'<article class="legal__doc"[^>]*data-doc="%s"[^>]*>(.*?)</article>' % doc, legal, re.S)
    body = re.sub(r'\s*<h2>.*?</h2>', '', m.group(1), count=1, flags=re.S)
    body = re.sub(r'href="#([\w-]+)" data-legal="[\w-]+"', lambda mm: 'href="%s"' % links[mm.group(1)], body)
    lines = [l[4:] if l.startswith('    ') else l for l in body.strip('\n').split('\n')]
    (out / fname).write_text('\n'.join(lines).strip() + '\n', encoding='utf-8')

# ---------- Fotos, librerías y ficha ----------
img_dst = THEME / 'assets' / 'img'
img_dst.mkdir(parents=True, exist_ok=True)
for f in (ROOT / 'propuesta' / 'img').iterdir():
    if f.name != 'logo-claro.png':
        shutil.copy2(f, img_dst / f.name)
vendor = THEME / 'assets' / 'js' / 'vendor'
vendor.mkdir(parents=True, exist_ok=True)
for f in ['gsap/dist/gsap.min.js', 'gsap/dist/ScrollTrigger.min.js', 'gsap/dist/MotionPathPlugin.min.js', 'lenis/dist/lenis.min.js']:
    shutil.copy2(LIBS / f, vendor / Path(f).name)
print('Tema actualizado en', THEME)

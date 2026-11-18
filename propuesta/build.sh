#!/usr/bin/env bash
# Genera a partir de src/index.html:
#   index.html      -> un solo archivo con librerías y fotos incluidas (se abre en cualquier navegador)
#   ARTIFACT_OUT    -> (opcional) versión para la vista previa publicada en claude.ai: librerías desde CDN y sin <html>/<head>/<body>
set -euo pipefail
cd "$(dirname "$0")"
LIBS="${LIBS:-../node_modules}"
python3 - "$LIBS" "${ARTIFACT_OUT:-}" <<'PY'
import sys, re, base64, mimetypes
libs, art_out = sys.argv[1], sys.argv[2]
src = open('src/index.html', encoding='utf-8').read()
src = src.replace('<!--__LEGAL__-->', open('src/legal.html', encoding='utf-8').read())
FILES = {'GSAP': 'gsap/dist/gsap.min.js', 'SCROLLTRIGGER': 'gsap/dist/ScrollTrigger.min.js',
         'MOTIONPATH': 'gsap/dist/MotionPathPlugin.min.js', 'LENIS': 'lenis/dist/lenis.min.js'}
CDN = {'GSAP': 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
       'SCROLLTRIGGER': 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
       'MOTIONPATH': 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/MotionPathPlugin.min.js',
       'LENIS': 'https://unpkg.com/lenis@1.1.13/dist/lenis.min.js'}
def inline_imgs(s):
    def rep(m):
        path = m.group(1)
        mime = mimetypes.guess_type(path)[0] or 'application/octet-stream'
        return 'data:%s;base64,%s' % (mime, base64.b64encode(open(path, 'rb').read()).decode())
    return re.sub(r'(?<=["(])(img/[^")]+)', rep, s)
s = src
for k, f in FILES.items():
    s = s.replace(f'/*__{k}__*/', open(f'{libs}/{f}').read().replace('</script', '<\\/script'))
s = inline_imgs(s)
open('index.html', 'w', encoding='utf-8').write(s)
print('index.html', len(s) // 1024, 'KB')
if art_out:
    a = src
    for k, url in CDN.items():
        a = a.replace(f'<script>/*__{k}__*/</script>', f'<script src="{url}"></script>')
    for t in ['<!doctype html>\n', '<html lang="es">\n', '<head>\n', '<meta charset="utf-8">\n',
              '<meta name="viewport" content="width=device-width, initial-scale=1">\n', '</head>\n', '<body>\n', '</body>\n', '</html>\n']:
        a = a.replace(t, '', 1)
    a = inline_imgs(a)
    open(art_out, 'w', encoding='utf-8').write(a)
    print(art_out, len(a) // 1024, 'KB')
PY

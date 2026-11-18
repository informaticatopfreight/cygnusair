#!/usr/bin/env bash
# Genera propuesta/index.html en un solo archivo (librerías y fotos incluidas) a partir de src/index.html
set -euo pipefail
cd "$(dirname "$0")"
LIBS="${LIBS:-../node_modules}"
python3 - "$LIBS" <<'PY'
import sys, re, base64, mimetypes
libs = sys.argv[1]
s = open('src/index.html', encoding='utf-8').read()
for k, f in {'GSAP': 'gsap/dist/gsap.min.js', 'SCROLLTRIGGER': 'gsap/dist/ScrollTrigger.min.js',
             'MOTIONPATH': 'gsap/dist/MotionPathPlugin.min.js', 'LENIS': 'lenis/dist/lenis.min.js'}.items():
    s = s.replace(f'/*__{k}__*/', open(f'{libs}/{f}').read().replace('</script', '<\\/script'))
def inline(m):
    path = m.group(1)
    mime = mimetypes.guess_type(path)[0] or 'application/octet-stream'
    return 'data:%s;base64,%s' % (mime, base64.b64encode(open(path, 'rb').read()).decode())
s = re.sub(r'(?<=["(])(img/[^")]+)', inline, s)
open('index.html', 'w', encoding='utf-8').write(s)
print('index.html', len(s) // 1024, 'KB')
PY

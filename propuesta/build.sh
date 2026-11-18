#!/usr/bin/env bash
# Genera index.html (un solo archivo, librerías incluidas) a partir de src/index.html
set -euo pipefail
cd "$(dirname "$0")"
LIBS="${LIBS:-../node_modules}"
python3 - "$LIBS" <<'PY'
import sys
libs=sys.argv[1]
s=open('src/index.html').read()
for k,f in {'GSAP':'gsap/dist/gsap.min.js','SCROLLTRIGGER':'gsap/dist/ScrollTrigger.min.js','MOTIONPATH':'gsap/dist/MotionPathPlugin.min.js','LENIS':'lenis/dist/lenis.min.js'}.items():
    code=open(f'{libs}/{f}').read().replace('</script','<\\/script')
    s=s.replace(f'/*__{k}__*/',code)
open('index.html','w').write(s)
print('index.html', len(s)//1024, 'KB')
PY

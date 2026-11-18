#!/usr/bin/env bash
# Crea cygnusair.zip listo para subir en WordPress → Apariencia → Temas → Añadir nuevo → Subir tema.
set -euo pipefail
cd "$(dirname "$0")/.."
rm -f cygnusair.zip
zip -rq cygnusair.zip cygnusair -x "*.DS_Store"
echo "Creado: $(pwd)/cygnusair.zip ($(du -h cygnusair.zip | cut -f1))"

# Cygnus Air · web

Nueva web de **Cygnus Air** (ygnusair.com) con movimiento: textos que entran letra a letra, un avión que vuela con el scroll, la flota en horizontal y fotos con efecto cortina.

## Estado

**Fase 1 · Propuesta visual** (esta carpeta `propuesta/`)
Maqueta en un solo archivo para ver el estilo y las animaciones antes de pasarlo a WordPress. Los textos, cifras e imágenes son provisionales.

- Abrir `propuesta/index.html` en el navegador (funciona sin internet; solo las fuentes vienen de Google Fonts).
- Para regenerarlo después de editar `propuesta/src/index.html`:

```bash
npm install
npm run build:propuesta
```

**Fase 2 · Tema de WordPress** (pendiente de aprobar la propuesta)
Tema a medida, como el de AMUMA: se sube como `.zip`, crea solo las páginas y la flota se gestiona desde el panel (cada avión nuevo aparece en la web con su animación).

## Qué hay en la propuesta

| Sección | Movimiento |
|---|---|
| Entrada | Pantalla de carga con contador y el nombre letra a letra |
| Portada | Cielo estrellado que reacciona al ratón, la constelación del Cisne (Cygnus) se dibuja sola, estrellas fugaces, estela de avión |
| Cinta | Palabras en bucle que aceleran y se inclinan con la velocidad del scroll |
| Compañía | El párrafo se ilumina palabra a palabra; contadores animados |
| Servicios | "Plan de vuelo": un avión recorre una ruta mientras bajas, con altímetro, velocidad y rumbo |
| Flota | Desplazamiento horizontal; cada avión entra "aterrizando" con líneas de velocidad |
| Galería | Fotos que se descubren como una cortina y con parallax |
| Contacto | Botón magnético y pista de aterrizaje animada |

Respeta la opción del sistema "reducir movimiento": quien la tenga activada ve la web estática.

Librerías: [GSAP](https://gsap.com) (con ScrollTrigger y MotionPath) y [Lenis](https://lenis.darkroom.engineering) para el scroll suave.

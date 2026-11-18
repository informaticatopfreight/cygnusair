# Cygnus Air · web

Nueva web de **Cygnus Air** (ygnusair.com) con movimiento: textos que entran letra a letra, un avión que vuela con el scroll, la flota en horizontal y fotos con efecto cortina.

## Estado

**Fase 1 · Propuesta visual** (esta carpeta `propuesta/`)
Maqueta en un solo archivo para ver el estilo y las animaciones antes de pasarlo a WordPress. Textos, fotos, logo y colores sacados de la web actual (www.ygnusair.com/cms). Las fotos optimizadas están en `propuesta/img/` y el script las incluye dentro de `index.html`.

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
| Portada | Foto nocturna del 757 que se acerca al entrar, cielo estrellado que reacciona al ratón y la constelación del Cisne (Cygnus) que se dibuja sola |
| Cinta | Palabras en bucle que aceleran y se inclinan con la velocidad del scroll |
| Quiénes somos | El párrafo se ilumina palabra a palabra; contadores animados |
| Certificaciones | Filas que se dibujan al aparecer y se rellenan de azul al pasar el ratón |
| Servicios | "Plan de vuelo": un avión recorre una ruta mientras bajas, con altímetro, velocidad y rumbo; cada tramo enseña un servicio |
| Destinos | El mapa se abre en círculo desde Madrid, con barrido de radar |
| Flota | Desplazamiento horizontal: 757-200PCF con ficha técnica animada, historia de la flota y hueco para el próximo avión |
| Galería | Fotos que se descubren como una cortina y con parallax |
| Equipo | Lista desplegable con la trayectoria de cada persona |
| Contacto | Botón magnético y formulario con etiquetas flotantes |

Respeta la opción del sistema "reducir movimiento": quien la tenga activada ve la web estática.

Librerías: [GSAP](https://gsap.com) (con ScrollTrigger y MotionPath) y [Lenis](https://lenis.darkroom.engineering) para el scroll suave.

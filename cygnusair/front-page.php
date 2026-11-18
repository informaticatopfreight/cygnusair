<?php
/**
 * Portada: toda la web en una página. El menú lleva a cada sección.
 * La flota sale de Flota (panel) y el comunicado de Apariencia → Comunicado y contacto.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

get_header();

$aviones = cygnus_aviones();
$modelos = wp_list_pluck( $aviones, 'post_title' );
?>
<div class="preloader" aria-hidden="true">
	<div class="preloader__word display" data-split>Cygnus&nbsp;<span class="accent">Air</span></div>
	<div class="preloader__count"><span class="js-count">000</span> / 100</div>
	<div class="preloader__bar"></div>
</div>
<div class="cursor" aria-hidden="true"></div>

<main id="top">

<!-- ================= PORTADA ================= -->
<section class="hero">
	<div class="hero__photo"><img src="<?php echo cygnus_img( '757-aterrizando.jpg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt="Boeing 757-200PCF de Cygnus Air despegando" fetchpriority="high"></div>
	<div class="hero__shade" aria-hidden="true"></div>
	<canvas id="sky" aria-hidden="true"></canvas>
	<svg class="fly-layer" id="fly-hero" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><path class="fly-trail" d="M-80 470 C 320 430 760 260 1540 120"/><g class="fly-jet"><use href="#jet-s" x="-24" y="-24" width="48" height="48"/></g></svg>

	<!-- La constelación del Cisne (Cygnus), el mismo animal que el logotipo -->
	<svg class="constellation" viewBox="0 0 420 420" aria-hidden="true">
		<g class="c-lines">
			<line x1="210" y1="40"  x2="210" y2="165"/><line x1="210" y1="165" x2="210" y2="262"/><line x1="210" y1="262" x2="210" y2="380"/>
			<line x1="210" y1="165" x2="122" y2="214"/><line x1="122" y1="214" x2="48"  y2="268"/>
			<line x1="210" y1="165" x2="296" y2="128"/><line x1="296" y1="128" x2="360" y2="74"/>
		</g>
		<g class="c-stars">
			<circle cx="210" cy="40" r="4.4"/><circle cx="210" cy="165" r="3.6"/><circle cx="210" cy="262" r="2.4"/><circle cx="210" cy="380" r="3.2"/>
			<circle cx="122" cy="214" r="3"/><circle cx="48" cy="268" r="2.4"/><circle cx="296" cy="128" r="3"/><circle cx="360" cy="74" r="2.2"/>
		</g>
		<g class="c-names"><text x="222" y="44">DENEB</text><text x="222" y="169">SADR</text><text x="222" y="384">ALBIREO</text><text x="52" y="204">GIENAH</text></g>
	</svg>

	<h1 class="hero__title display">
		<span class="line" data-split>Experiencia</span><br>
		<span class="line" data-split>en cada <span class="accent">vuelo.</span></span>
	</h1>
	<div class="hero__row">
		<p class="hero__sub" data-fade><strong>CygnusAir</strong> es un operador aéreo con una extensa experiencia en el sector de la aviación, y una de las primeras compañías cargueras aéreas españolas.</p>
		<div class="scrollcue" data-fade><i></i>Desliza para despegar</div>
	</div>
</section>

<!-- ================= CINTA ================= -->
<div class="marquee" aria-hidden="true">
	<div class="marquee__track">
		<div class="marquee__group">
			<?php
			$words = array( 'Wet lease', 'ACMI', 'Vuelos ad-hoc', 'CMI', 'Cobertura AOC', 'FAR Part 129' );
			foreach ( $words as $i => $w ) {
				printf( '<span class="display%s">%s</span><svg viewBox="0 0 30 16"><path d="M0 9 L22 9 L30 5 L30 7 L24 11 L4 12 Z M10 9 L16 1 L19 1 L15 9 Z" fill="#7CBCD4"/></svg>', $i % 2 ? ' out' : '', esc_html( $w ) );
			}
			?>
		</div>
	</div>
</div>

<!-- ================= QUIÉNES SOMOS ================= -->
<section class="about" id="quienes">
	<div class="eyebrow" data-fade>Quiénes somos</div>
	<p class="about__text" data-words>Operamos aviones cargueros con nuestro propio <em>Certificado de Operador Aéreo</em>, emitido por AESA, y con la licencia FAR Part 129 para volar hacia y desde Estados Unidos. Todo bajo las máximas condiciones de seguridad y los más altos estándares de calidad.</p>
	<div class="about__more">
		<p data-fade>Nuestra actividad principal es la operación de aeronaves cargueras mediante contratos de leasing, como el Wet Lease, o mediante vuelos chárter según la demanda del transporte de mercancías.</p>
		<p data-fade>CygnusAir nació en 1994 como Regional Líneas Aéreas, la primera compañía española dedicada a la aviación regional. Hoy forma parte del Grupo Swiftair y se dedica exclusivamente a vuelos regulares de carga y vuelos ‘on demand’ para importantes empresas del transporte aéreo de mercancías.</p>
	</div>
	<div class="stats">
		<div class="stat"><b><span data-count="1994" data-nogroup>1994</span></b><span>Año de fundación</span></div>
		<div class="stat"><b>A2</b><span>Tipo de AOC · AESA</span></div>
		<div class="stat"><b><span data-count="239">239</span><small>m³</small></b><span>Carga por avión</span></div>
		<div class="stat"><b><span data-count="129">129</span></b><span>FAR Part · EE. UU.</span></div>
	</div>
</section>

<!-- ================= SERVICIOS: el avión recorre solo las cinco fases del vuelo ================= -->
<section class="svc" id="servicios">
	<div class="svc__gridbg" aria-hidden="true"></div>
	<div class="svc__head">
		<div>
			<div class="eyebrow" data-fade>Nuestros servicios</div>
			<h2 class="display" data-split-scroll>Plan de vuelo</h2>
		</div>
		<div class="hud" aria-hidden="true">
			<span>ALT</span><b class="js-alt">00 000 FT</b>
			<span>GS</span><b class="js-spd">000 KT</b>
			<span>HDG</span><b class="js-hdg">090°</b>
		</div>
	</div>
	<svg class="svc__profile" id="svc-profile" viewBox="0 0 1000 160" aria-hidden="true">
		<path class="svc__ground" d="M0 152 H1000"/>
		<path class="svc__ghost" d="M0 140 L70 140 C170 140 220 70 300 45 C360 30 420 28 500 28 C580 28 640 30 700 45 C780 70 830 140 930 140 L1000 140"/>
		<path class="svc__trail" d="M0 140 L70 140 C170 140 220 70 300 45 C360 30 420 28 500 28 C580 28 640 30 700 45 C780 70 830 140 930 140 L1000 140"/>
		<g class="svc__dots"></g>
		<g class="fly-jet"><use href="#jet-s" x="-17" y="-17" width="34" height="34"/></g>
	</svg>
	<div class="svc__list">
		<article class="svc__item"><div class="num">01 · Embarque</div><h3>Wet Lease · ACMI</h3><p>Arrendamiento de nuestras aeronaves con avión, tripulación, mantenimiento y seguro. Toda la operación recae sobre CygnusAir; el cliente comercializa los vuelos.</p></article>
		<article class="svc__item"><div class="num">02 · Despegue</div><h3>Vuelos ad-hoc</h3><p>Vuelos de carga ‘on demand’ a cualquier parte del mundo según la disponibilidad de la flota, estudiando siempre la ruta óptima. En condiciones ACMI o ‘full charter’.</p></article>
		<article class="svc__item"><div class="num">03 · Crucero</div><h3>CMI</h3><p>Tripulaciones (Crew), mantenimiento (Maintenance) y seguro (Insurance) para que nuestros clientes entren en el transporte aéreo con agilidad, sin preocuparse de las áreas más costosas.</p></article>
		<article class="svc__item"><div class="num">04 · Aproximación</div><h3>Cobertura AOC</h3><p>Operamos bajo nuestro AOC cualquier aeronave que cumpla los requisitos de seguridad: abre el mercado europeo a compañías de fuera de la UE y permite a las nuevas aerolíneas empezar a volar mientras tramitan su propio certificado.</p></article>
		<article class="svc__item"><div class="num">05 · Aterrizaje</div><h3>Asesoramiento</h3><p>La amplia experiencia de nuestro personal al servicio del cliente, con altos parámetros de calidad y buscando siempre la mejor solución.</p></article>
	</div>
</section>

<!-- ================= DESTINOS ================= -->
<section class="dest" id="destinos">
	<div>
		<div class="eyebrow" data-fade>Nuestros destinos</div>
		<h2 class="display" data-split-scroll>Destinos</h2>
		<p data-fade>Destinos de carga operados actualmente, desde nuestra base en Madrid hacia Europa, África y América.</p>
		<div class="dest__base" data-fade><span>Base<b>MAD</b></span><span>Indicativo<b>CYGNUS</b></span><span>OACI<b>RGN</b></span></div>
	</div>
	<div class="dest__map">
		<div class="dest__layer">
			<img src="<?php echo cygnus_img( 'destinos.jpg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt="Mapa con los destinos de carga que opera Cygnus Air" loading="lazy">
			<svg class="fly-layer" id="fly-map" viewBox="0 0 1600 1113" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><g class="routes"></g><g class="fly-jet"><use href="#jet-s" x="-30" y="-30" width="60" height="60"/></g></svg>
			<div class="dest__ping" aria-hidden="true"></div>
		</div>
		<div class="dest__radar" aria-hidden="true"></div>
	</div>
</section>

<!-- ================= FLOTA: desplazamiento horizontal ================= -->
<section class="fleet" id="flota">
	<div class="fleet__sticky">
		<div class="fleet__track">
			<div class="panel panel--intro">
				<div class="eyebrow">Nuestra flota</div>
				<h2 class="display" data-split-scroll>La flota</h2>
				<?php if ( $modelos ) : ?>
					<p>CygnusAir opera en la actualidad con <strong><?php echo esc_html( wp_sprintf( '%l', array_unique( $modelos ) ) ); ?></strong>: un excelente rendimiento en el corto alcance, capaz de cubrir largas distancias de la manera más óptima, económica y eficaz.</p>
				<?php endif; ?>
			</div>

			<?php
			foreach ( $aviones as $avion ) {
				cygnus_render_avion( $avion );
			}
			?>

			<div class="panel panel--history">
				<div>
					<div class="eyebrow">Historia</div>
					<h3 class="display" style="margin-top:14px">Desde nuestros comienzos</h3>
					<p>CygnusAir ha operado con diversas aeronaves de transporte y carga: Airbus 340 y Boeing 757 en rutas de pasaje con Iberia, y vuelos chárter ejecutivos con Embraer 135, Falcon 20 y Hawker 700.</p>
					<div class="types"><span>Airbus 340</span><span>Boeing 757</span><span>DC-8-62F</span><span>Embraer 135</span><span>Falcon 20</span><span>Hawker 700</span></div>
				</div>
				<div class="history__pics">
					<figure class="pic"><img src="<?php echo cygnus_img( 'dc8.jpg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt="McDonnell Douglas DC-8-62F de Cygnus Air" loading="lazy"><figcaption>DC-8-62F</figcaption></figure>
					<figure class="pic"><img src="<?php echo cygnus_img( 'embraer135.jpg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt="Embraer 135" loading="lazy"><figcaption>Embraer 135</figcaption></figure>
					<figure class="pic"><img src="<?php echo cygnus_img( 'falcon20.jpg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt="Dassault Falcon 20" loading="lazy"><figcaption>Falcon 20</figcaption></figure>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ================= GALERÍA ================= -->
<section class="gallery">
	<div class="gallery__head">
		<h2 class="display" data-split-scroll>Galería</h2>
		<p data-fade>Todas las fotos propiedad de sus respectivos autores.</p>
	</div>
	<?php
	$tiles = array(
		't1' => array( '757-pista.jpg', 'Boeing 757 de Cygnus Air en pista' ),
		't2' => array( '757-arranque.jpg', 'Boeing 757 de Cygnus Air en plataforma' ),
		't3' => array( '757-cielo.jpg', 'Boeing 757 de Cygnus Air en vuelo' ),
		't4' => array( '757-mar.jpg', 'Boeing 757 de Cygnus Air sobre el mar' ),
		't5' => array( 'hero-757-noche.jpg', 'Boeing 757 de Cygnus Air en plataforma de noche' ),
	);
	foreach ( $tiles as $cls => $t ) {
		printf( '<figure class="tile %s"><img src="%s" alt="%s" loading="lazy"></figure>', esc_attr( $cls ), cygnus_img( $t[0] ), esc_attr( $t[1] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</section>

<!-- ================= CERTIFICACIONES ================= -->
<section class="certs" id="certificados">
	<div class="certs__grid" aria-hidden="true"></div>
	<div class="certs__head">
		<div>
			<div class="eyebrow" data-fade>Certificaciones</div>
			<h2 class="display" data-split-scroll>Licencias</h2>
		</div>
		<p data-fade>Las licencias y aprobaciones que nos permiten operar en cualquier parte del mundo, cubriendo cualquier línea de conexión.</p>
	</div>
	<div class="lics">
		<?php
		$lics = array(
			array( 'Operador', 'AESA', '<path d="M23 4 L39 10 V21 C39 31 32 38 23 42 C14 38 7 31 7 21 V10 Z"/><path d="M15 23 l6 6 l11 -12"/>', 'A.O.C', 'Certificado de Operador Aéreo · Tipo A2' ),
			array( 'Internacional', 'FAA · EE. UU.', '<circle cx="23" cy="23" r="17"/><path d="M6 23h34M23 6c6 5 6 29 0 34M23 6c-6 5-6 29 0 34"/><path d="M10 14h26M10 32h26"/>', 'FAR Part 129', 'Operación a y dentro de Estados Unidos para operadores extranjeros' ),
			array( 'Baja visibilidad', 'AESA', '<path d="M14 42 L20 8 M32 42 L26 8"/><path d="M23 38v-4M23 28v-4M23 18v-3"/><path d="M4 16h10M32 16h10M6 24h8M34 24h8" stroke-dasharray="2 3"/>', 'CAT II/III · LVTO', 'Aproximaciones y despegues con baja visibilidad' ),
			array( 'Carga', 'AESA', '<path d="M23 3 L43 23 L23 43 L3 23 Z"/><path d="M23 14v13"/><circle cx="23" cy="32" r="1.2"/>', 'Mercancías peligrosas', 'Transporte de mercancías peligrosas' ),
			array( 'Espacio aéreo', 'AESA', '<path d="M5 11h36M5 23h36M5 35h36"/><path d="M15 15v4M15 27v4M31 15v4M31 27v4"/><path d="M12 18l3 3 3-3M28 30l3 3 3-3"/>', 'RVSM · MNPS', 'Separación vertical mínima reducida y especificaciones de navegación del Atlántico Norte' ),
			array( 'Navegación', 'AESA', '<circle cx="8" cy="34" r="3"/><circle cx="21" cy="14" r="3"/><circle cx="38" cy="26" r="3"/><path d="M10 31 L19 17 M24 16 L35 24"/><path d="M38 8v8M34 12h8"/>', 'RNAV1 · RNAV5 · RNP APP', 'Navegación de área y aproximaciones RNP' ),
		);
		foreach ( $lics as $l ) :
			?>
			<article class="lic">
				<div class="lic__top"><span><?php echo esc_html( $l[0] ); ?></span><span class="lic__issuer"><?php echo esc_html( $l[1] ); ?></span></div>
				<svg class="lic__ico" viewBox="0 0 46 46" aria-hidden="true"><?php echo $l[2]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG fijo del tema. ?></svg>
				<h3 class="lic__t"><?php echo esc_html( $l[3] ); ?></h3>
				<p class="lic__d"><?php echo esc_html( $l[4] ); ?></p>
			</article>
		<?php endforeach; ?>
	</div>
</section>

<div class="eco">
	<svg viewBox="0 0 40 40" fill="none" stroke="#7CBCD4" stroke-width="1.8" aria-hidden="true"><path d="M8 32C8 16 18 8 34 6c-1 16-9 26-24 26z"/><path d="M8 32 24 16"/></svg>
	<p data-split-scroll>Comprometidos con el medio ambiente</p>
</div>

<!-- ================= CONTACTO ================= -->
<section class="cta" id="contacto">
	<div class="cta__top">
		<svg class="fly-layer" id="fly-cta" viewBox="0 0 1440 640" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><path class="fly-trail" d="M-100 560 C 380 560 620 420 900 320 S 1300 120 1560 50"/><g class="fly-jet"><use href="#jet-s" x="-30" y="-30" width="60" height="60"/></g></svg>
		<div class="eyebrow" style="justify-content:center" data-fade>Contacto</div>
		<h2 class="display" data-split-scroll>¿Despegamos?</h2>
		<p data-fade>Cuéntenos qué necesita mover y le preparamos el plan de vuelo.</p>
		<a class="magnet" href="#formulario" data-hover><span>Escríbanos</span></a>
	</div>
	<div class="cta__grid">
		<dl class="cta__info" data-fade>
			<div><dt>Oficinas centrales</dt><dd>Avenida de Suiza, 18-20<br>Edificio JAS, 1.ª planta<br>28821 Coslada · Madrid · España</dd></div>
			<div><dt>Teléfono</dt><dd><a href="tel:+34917461530">+34 917 461 530</a></dd></div>
			<div><dt>Email</dt><dd><a href="mailto:info@ygnusair.com">info@ygnusair.com</a></dd></div>
		</dl>
		<form class="form" id="formulario" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php cygnus_contacto_aviso(); ?>
			<input type="hidden" name="action" value="cygnus_contacto">
			<?php wp_nonce_field( 'cygnus_contacto', '_cygnus_nonce' ); ?>
			<div class="hp" aria-hidden="true"><label for="f-web">Web</label><input id="f-web" name="web" tabindex="-1" autocomplete="off"></div>
			<div class="field"><input id="f-n" name="nombre" placeholder=" " autocomplete="name" required maxlength="100"><label for="f-n">Nombre</label><i></i></div>
			<div class="field"><input id="f-e" name="email" type="email" placeholder=" " autocomplete="email" required maxlength="150"><label for="f-e">Correo electrónico</label><i></i></div>
			<div class="field full"><textarea id="f-m" name="mensaje" rows="4" placeholder=" " required maxlength="5000"></textarea><label for="f-m">Mensaje</label><i></i></div>
			<label class="check"><input type="checkbox" name="privacidad" value="1" required> <span>He leído y acepto la <a href="<?php echo esc_url( cygnus_page_url( 'politica-de-privacidad' ) ); ?>">política de privacidad</a>.</span></label>
			<button class="btn" type="submit" data-hover>Enviar mensaje →</button>
		</form>
	</div>
</section>

</main>
<?php
get_footer();

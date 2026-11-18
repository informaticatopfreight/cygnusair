<?php
/**
 * Tema Cygnus Air — funciones principales.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

define( 'CYGNUS_VERSION', '1.0.0' );
define( 'CYGNUS_DIR', get_template_directory() );
define( 'CYGNUS_URI', get_template_directory_uri() );

require CYGNUS_DIR . '/inc/helpers.php';
require CYGNUS_DIR . '/inc/aviones.php';
require CYGNUS_DIR . '/inc/ajustes.php';
require CYGNUS_DIR . '/inc/contacto.php';
require CYGNUS_DIR . '/inc/activacion.php';

/**
 * Configuración del tema.
 */
function cygnus_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script', 'gallery', 'caption' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 150,
			'width'       => 440,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_image_size( 'cygnus-avion', 1600, 1050, false );
}
add_action( 'after_setup_theme', 'cygnus_setup' );

/**
 * CSS y JS.
 * - En todas las páginas: estilos y el comunicado de emergencia.
 * - Solo en la portada: GSAP, Lenis y las animaciones.
 */
function cygnus_assets() {
	wp_enqueue_style( 'cygnus-fonts', 'https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,300..900&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'cygnus-main', CYGNUS_URI . '/assets/css/main.css', array( 'cygnus-fonts' ), CYGNUS_VERSION );
	wp_enqueue_style( 'cygnus-wp', CYGNUS_URI . '/assets/css/wp.css', array( 'cygnus-main' ), CYGNUS_VERSION );

	wp_enqueue_script( 'cygnus-crisis', CYGNUS_URI . '/assets/js/crisis.js', array(), CYGNUS_VERSION, true );
	wp_add_inline_script( 'cygnus-crisis', 'window.CYGNUS_COMUNICADO = ' . wp_json_encode( cygnus_comunicado_publico() ) . ';', 'before' );

	if ( is_front_page() ) {
		$v = CYGNUS_URI . '/assets/js/vendor/';
		wp_enqueue_script( 'gsap', $v . 'gsap.min.js', array(), '3.12.5', true );
		wp_enqueue_script( 'gsap-scrolltrigger', $v . 'ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );
		wp_enqueue_script( 'gsap-motionpath', $v . 'MotionPathPlugin.min.js', array( 'gsap' ), '3.12.5', true );
		wp_enqueue_script( 'lenis', $v . 'lenis.min.js', array(), '1.1.13', true );
		wp_enqueue_script( 'cygnus-main', CYGNUS_URI . '/assets/js/main.js', array( 'gsap', 'gsap-scrolltrigger', 'gsap-motionpath', 'lenis', 'cygnus-crisis' ), CYGNUS_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'cygnus_assets' );

/**
 * Descripción para buscadores en la portada.
 */
function cygnus_meta_description() {
	if ( is_front_page() ) {
		echo '<meta name="description" content="' . esc_attr__( 'Cygnus Air, operador aéreo de carga con base en Madrid: Wet Lease (ACMI), vuelos ad-hoc, CMI, cobertura AOC y asesoramiento con Boeing 757-200PCF.', 'cygnusair' ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'cygnus_meta_description', 1 );

/**
 * Clase en <html> cuando el comunicado está activo: la web pasa a modo sereno sin esperar al JavaScript.
 *
 * @param string $output Atributos de <html>.
 * @return string
 */
function cygnus_html_class( $output ) {
	if ( cygnus_comunicado_activo() ) {
		$output .= ' class="is-crisis is-calm"';
	}
	return $output;
}
add_filter( 'language_attributes', 'cygnus_html_class' );

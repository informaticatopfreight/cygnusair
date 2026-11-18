<?php
/**
 * Utilidades de las plantillas.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

/**
 * Dirección de una foto del tema.
 *
 * @param string $file Nombre del archivo en assets/img.
 * @return string
 */
function cygnus_img( $file ) {
	return esc_url( CYGNUS_URI . '/assets/img/' . $file );
}

/**
 * Enlace a una sección de la portada (#flota). Fuera de la portada lleva a /#flota.
 *
 * @param string $id Ancla.
 * @return string
 */
function cygnus_anchor( $id ) {
	return is_front_page() ? '#' . $id : esc_url( home_url( '/#' . $id ) );
}

/**
 * Dirección de una página legal por su slug, o el inicio si aún no existe.
 *
 * @param string $slug Slug de la página.
 * @return string
 */
function cygnus_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/**
 * Logotipo: el que se suba en Apariencia → Personalizar, o el del tema.
 */
function cygnus_logo() {
	$id = (int) get_theme_mod( 'custom_logo' );
	if ( $id ) {
		echo wp_get_attachment_image( $id, 'full', false, array( 'alt' => 'Corporación Cygnus Air' ) );
		return;
	}
	echo '<img src="' . cygnus_img( 'logo.png' ) . '" alt="Corporación Cygnus Air" width="298" height="101">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Muestra una cifra para que la animación la cuente.
 * Admite "239", "47,32", "99.779" o "99779". Cualquier otro texto ("4.500–5.000") se muestra tal cual.
 *
 * @param string $raw Valor escrito en el panel.
 * @return string HTML.
 */
function cygnus_number( $raw ) {
	$v = str_replace( ' ', '', trim( (string) $raw ) );
	if ( '' === $v ) {
		return '';
	}
	if ( ! preg_match( '/^\d{1,3}(\.\d{3})+(,\d+)?$/', $v ) && ! preg_match( '/^\d+(,\d+)?$/', $v ) ) {
		return esc_html( $raw );
	}
	$comma = strpos( $v, ',' );
	$dec   = false === $comma ? 0 : strlen( $v ) - $comma - 1;
	$num   = (float) str_replace( array( '.', ',' ), array( '', '.' ), $v );
	// Igual que el navegador en español: los números de 4 cifras van sin punto (5000, 12.500).
	$text = ( $num < 10000 && 0 === $dec ) ? (string) (int) $num : number_format( $num, $dec, ',', '.' );
	return sprintf( '<span data-count="%s"%s>%s</span>', esc_attr( $num ), $dec ? ' data-dec="' . (int) $dec . '"' : '', esc_html( $text ) );
}

<?php
/**
 * Cabecera: comunicado de emergencia (oculto si no está activo), menú y logotipo.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#top"><?php esc_html_e( 'Saltar al contenido', 'cygnusair' ); ?></a>

<!-- Comunicado de emergencia: se activa en Apariencia → Comunicado y contacto -->
<div class="crisis-strip" id="crisis-strip" hidden>
	<span class="crisis-strip__tag"><?php esc_html_e( 'Comunicado oficial', 'cygnusair' ); ?></span>
	<span class="crisis-strip__txt" id="crisis-strip-txt"></span>
	<button type="button" id="crisis-open"><?php esc_html_e( 'Leer comunicado', 'cygnusair' ); ?></button>
</div>
<div class="crisis" id="crisis" hidden role="alertdialog" aria-modal="true" aria-labelledby="crisis-title" aria-describedby="crisis-body" data-lenis-prevent>
	<div class="crisis__box">
		<div class="crisis__meta"><span><?php esc_html_e( 'Comunicado oficial · Cygnus Air', 'cygnusair' ); ?></span><span id="crisis-date"></span></div>
		<h2 id="crisis-title"></h2>
		<div class="crisis__body" id="crisis-body"></div>
		<div class="crisis__phone" id="crisis-phone" hidden><?php esc_html_e( 'Teléfono de atención a familiares', 'cygnusair' ); ?><b></b></div>
		<button type="button" class="crisis__close" id="crisis-close"><?php esc_html_e( 'Continuar a la web', 'cygnusair' ); ?></button>
	</div>
</div>

<svg width="0" height="0" style="position:absolute" aria-hidden="true"><symbol id="jet-s" viewBox="0 0 41 40"><path d="M40 20 C40 18.6 38.6 18 36 18 L26 18 L16 5 L12 5 L18 18 L8 18 L4 12 L1 12 L3 20 L1 28 L4 28 L8 22 L18 22 L12 35 L16 35 L26 22 L36 22 C38.6 22 40 21.4 40 20 Z" fill="currentColor"/></symbol></svg>

<header class="header">
	<a href="<?php echo is_front_page() ? '#top' : esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="<?php esc_attr_e( 'Corporación Cygnus Air, inicio', 'cygnusair' ); ?>"><?php cygnus_logo(); ?></a>
	<nav class="nav" aria-label="<?php esc_attr_e( 'Principal', 'cygnusair' ); ?>">
		<?php
		$items = array(
			'quienes'      => __( 'Quiénes somos', 'cygnusair' ),
			'servicios'    => __( 'Servicios', 'cygnusair' ),
			'destinos'     => __( 'Destinos', 'cygnusair' ),
			'flota'        => __( 'Flota', 'cygnusair' ),
			'certificados' => __( 'Certificados', 'cygnusair' ),
			'contacto'     => __( 'Contacto', 'cygnusair' ),
		);
		foreach ( $items as $anchor => $label ) {
			printf(
				'<a href="%1$s"%2$s data-hover><span data-t="%3$s">%3$s</span></a>',
				cygnus_anchor( $anchor ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'contacto' === $anchor ? ' class="keep"' : '',
				esc_html( $label )
			);
		}
		?>
	</nav>
</header>

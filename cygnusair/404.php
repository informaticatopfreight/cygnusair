<?php
/**
 * Página no encontrada.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="top" class="page-wrap">
	<div class="legal__doc">
		<p class="legal__kicker">Error 404</p>
		<h1 class="page__title"><?php esc_html_e( 'Fuera de ruta', 'cygnusair' ); ?></h1>
		<p><?php esc_html_e( 'La página que busca no existe o ha cambiado de dirección.', 'cygnusair' ); ?></p>
		<a class="btn page-back" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Volver al inicio', 'cygnusair' ); ?></a>
	</div>
</main>
<?php
get_footer();

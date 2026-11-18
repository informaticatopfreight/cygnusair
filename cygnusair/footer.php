<?php
/**
 * Pie de página.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="footer">
	<div class="footer__big display" data-split-scroll aria-hidden="true">Cygnus Air</div>
	<div class="footer__row">
		<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Cygnus Air · Grupo Swiftair</span>
		<span>
			<a href="<?php echo esc_url( cygnus_page_url( 'aviso-legal' ) ); ?>"><?php esc_html_e( 'Aviso legal', 'cygnusair' ); ?></a> ·
			<a href="<?php echo esc_url( cygnus_page_url( 'politica-de-privacidad' ) ); ?>"><?php esc_html_e( 'Política de privacidad', 'cygnusair' ); ?></a> ·
			<a href="<?php echo esc_url( cygnus_page_url( 'politica-de-cookies' ) ); ?>"><?php esc_html_e( 'Política de cookies', 'cygnusair' ); ?></a>
		</span>
		<a href="#top" data-hover><?php esc_html_e( 'Volver arriba ↑', 'cygnusair' ); ?></a>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

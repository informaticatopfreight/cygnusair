<?php
/**
 * Páginas (aviso legal, privacidad, cookies y cualquier otra).
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="top" class="page-wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'legal__doc' ); ?>>
			<h1 class="page__title"><?php the_title(); ?></h1>
			<?php the_content(); ?>
			<a class="btn page-back" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Volver al inicio', 'cygnusair' ); ?></a>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();

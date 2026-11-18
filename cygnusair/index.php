<?php
/**
 * Plantilla de respaldo (entradas, archivos, búsquedas). La web es de una sola página;
 * esto solo se ve si alguien publica entradas.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="top" class="page-wrap">
	<div class="legal__doc">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<?php if ( is_singular() ) : ?>
						<h1 class="page__title"><?php the_title(); ?></h1>
						<?php the_content(); ?>
					<?php else : ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<h1 class="page__title"><?php esc_html_e( 'No hay nada aquí', 'cygnusair' ); ?></h1>
		<?php endif; ?>
		<a class="btn page-back" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Volver al inicio', 'cygnusair' ); ?></a>
	</div>
</main>
<?php
get_footer();

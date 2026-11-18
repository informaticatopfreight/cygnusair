<?php
/**
 * Al activar el tema en un WordPress nuevo se deja todo listo (se puede cambiar después):
 * - Páginas de aviso legal, política de privacidad y política de cookies con sus textos.
 * - El primer avión de la flota (Boeing 757-200PCF) con su foto, su ficha y su PDF.
 * - Correo del formulario: info@ygnusair.com.
 * - El comunicado de emergencia con el texto de ejemplo, activado para enseñarlo.
 * - Enlaces permanentes del tipo /aviso-legal/ y fuera el contenido de ejemplo de WordPress.
 * Se ejecuta una sola vez por versión del tema.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

/**
 * Activación.
 */
function cygnus_on_activation() {
	if ( get_option( 'cygnus_setup_done' ) === CYGNUS_VERSION ) {
		return;
	}
	cygnus_create_legal_pages();
	cygnus_create_first_avion();

	add_option( 'cygnus_contacto_email', 'info@ygnusair.com' );
	add_option(
		'cygnus_comunicado',
		array(
			'activo'   => 1,
			'titulo'   => 'Comunicado de Cygnus Air',
			'fecha'    => '[fecha y hora]',
			'texto'    => cygnus_comunicado_ejemplo(),
			'telefono' => '',
		)
	);

	// Contenido de ejemplo de una instalación nueva, si no se ha tocado.
	foreach ( array( array( 'post', array( 'hola-mundo', 'hello-world' ) ), array( 'page', array( 'pagina-ejemplo', 'sample-page' ) ) ) as $sample ) {
		foreach ( $sample[1] as $slug ) {
			$found = get_posts( array( 'name' => $slug, 'post_type' => $sample[0], 'post_status' => 'any', 'numberposts' => 1 ) );
			if ( $found && $found[0]->post_modified === $found[0]->post_date ) {
				wp_delete_post( $found[0]->ID, true );
			}
		}
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	cygnus_register_avion();
	flush_rewrite_rules();

	update_option( 'cygnus_setup_done', CYGNUS_VERSION );
}
add_action( 'after_switch_theme', 'cygnus_on_activation' );

/**
 * Páginas legales con los textos de inc/legal. Si ya existen, no se tocan.
 */
function cygnus_create_legal_pages() {
	$pages = array(
		'aviso-legal'            => array( 'Aviso legal', 'aviso-legal.html' ),
		'politica-de-privacidad' => array( 'Política de privacidad', 'politica-de-privacidad.html' ),
		'politica-de-cookies'    => array( 'Política de cookies', 'politica-de-cookies.html' ),
	);
	$created = array();
	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$created[ $slug ] = wp_insert_post(
			array(
				'post_title'  => $page[0],
				'post_name'   => $slug,
				'post_type'   => 'page',
				'post_status' => 'publish',
			)
		);
	}
	// El contenido se escribe después, cuando ya existen las tres y se pueden enlazar entre sí.
	$links = array(
		'{{aviso-legal}}' => cygnus_page_url( 'aviso-legal' ),
		'{{privacidad}}'  => cygnus_page_url( 'politica-de-privacidad' ),
		'{{cookies}}'     => cygnus_page_url( 'politica-de-cookies' ),
	);
	foreach ( $created as $slug => $id ) {
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$content = file_get_contents( CYGNUS_DIR . '/inc/legal/' . $pages[ $slug ][1] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => "<!-- wp:html -->\n" . strtr( $content, $links ) . "\n<!-- /wp:html -->",
			)
		);
	}

	// Página de privacidad de WordPress: la nuestra en lugar del borrador que crea WordPress.
	$privacy = get_page_by_path( 'politica-de-privacidad' );
	$current = get_post( (int) get_option( 'wp_page_for_privacy_policy' ) );
	if ( $privacy && ( ! $current || 'publish' !== $current->post_status ) ) {
		update_option( 'wp_page_for_privacy_policy', $privacy->ID );
		if ( $current && 'draft' === $current->post_status && $current->ID !== $privacy->ID ) {
			wp_delete_post( $current->ID, true );
		}
	}
}

/**
 * Copia un archivo del tema a la biblioteca de medios.
 *
 * @param string $rel   Ruta dentro del tema.
 * @param string $title Título del archivo.
 * @return int ID del adjunto o 0.
 */
function cygnus_import_media( $rel, $title ) {
	$path = CYGNUS_DIR . '/' . $rel;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type = wp_check_filetype( $upload['file'] );
	$id   = wp_insert_attachment(
		array(
			'post_title'     => $title,
			'post_mime_type' => $type['type'],
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	return (int) $id;
}

/**
 * Primer avión de la flota, si todavía no hay ninguno.
 */
function cygnus_create_first_avion() {
	cygnus_register_avion();
	$any = get_posts( array( 'post_type' => 'avion', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $any ) {
		return;
	}
	$id = wp_insert_post(
		array(
			'post_title'  => 'Boeing 757-200PCF',
			'post_type'   => 'avion',
			'post_status' => 'publish',
			'menu_order'  => 0,
		)
	);
	if ( ! $id || is_wp_error( $id ) ) {
		return;
	}
	$meta = array(
		'etiqueta'    => 'Flota actual',
		'capacidad'   => '239',
		'mtow'        => '99779',
		'longitud'    => '47,32',
		'envergadura' => '38,05',
		'velocidad'   => '868',
		'alcance'     => '4.500–5.000',
		'altura'      => '13,56',
		'techo'       => '12500',
		'motores'     => '2× Rolls-Royce RB211-535E4 o Pratt & Whitney PW2037 / PW2040 / PW2043',
		'peso_vacio'  => '58.390',
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, 'cygnus_' . $k, $v );
	}
	$foto = cygnus_import_media( 'assets/img/757-costa.jpg', 'Boeing 757-200PCF de Cygnus Air' );
	if ( $foto ) {
		set_post_thumbnail( $id, $foto );
	}
	$pdf = cygnus_import_media( 'assets/docs/B757-200PCF-ficha.pdf', 'Ficha técnica Boeing 757-200PCF' );
	if ( $pdf ) {
		update_post_meta( $id, 'cygnus_ficha', wp_get_attachment_url( $pdf ) );
	}
}

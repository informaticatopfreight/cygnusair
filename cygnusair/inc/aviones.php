<?php
/**
 * Flota: cada avión se añade en el panel (Flota → Añadir avión) y aparece en la portada con su ficha.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

/**
 * Campos de la ficha de cada avión: clave => [etiqueta, unidad, ejemplo].
 *
 * @return array
 */
function cygnus_avion_fields() {
	return array(
		'capacidad'   => array( __( 'Capacidad de carga', 'cygnusair' ), 'm³', '239' ),
		'mtow'        => array( __( 'Peso máx. al despegue', 'cygnusair' ), 'kg', '99779' ),
		'longitud'    => array( __( 'Longitud', 'cygnusair' ), 'm', '47,32' ),
		'envergadura' => array( __( 'Envergadura', 'cygnusair' ), 'm', '38,05' ),
		'velocidad'   => array( __( 'Velocidad de crucero', 'cygnusair' ), 'km/h', '868' ),
		'alcance'     => array( __( 'Alcance', 'cygnusair' ), 'km', '4.500–5.000' ),
		'altura'      => array( __( 'Altura', 'cygnusair' ), 'm', '13,56' ),
		'techo'       => array( __( 'Techo de vuelo', 'cygnusair' ), 'm', '12500' ),
	);
}

/**
 * Tipo de contenido "Avión".
 */
function cygnus_register_avion() {
	register_post_type(
		'avion',
		array(
			'labels'          => array(
				'name'               => __( 'Flota', 'cygnusair' ),
				'singular_name'      => __( 'Avión', 'cygnusair' ),
				'menu_name'          => __( 'Flota', 'cygnusair' ),
				'add_new'            => __( 'Añadir avión', 'cygnusair' ),
				'add_new_item'       => __( 'Añadir avión', 'cygnusair' ),
				'edit_item'          => __( 'Editar avión', 'cygnusair' ),
				'new_item'           => __( 'Nuevo avión', 'cygnusair' ),
				'all_items'          => __( 'Todos los aviones', 'cygnusair' ),
				'search_items'       => __( 'Buscar aviones', 'cygnusair' ),
				'not_found'          => __( 'No hay aviones.', 'cygnusair' ),
				'not_found_in_trash' => __( 'No hay aviones en la papelera.', 'cygnusair' ),
				'featured_image'     => __( 'Foto del avión', 'cygnusair' ),
				'set_featured_image' => __( 'Elegir foto del avión', 'cygnusair' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_position'   => 21,
			'menu_icon'       => 'dashicons-airplane',
			'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'cygnus_register_avion' );

/**
 * Caja "Ficha técnica" en la pantalla de edición del avión.
 */
function cygnus_avion_metabox() {
	add_meta_box( 'cygnus-avion-ficha', __( 'Ficha técnica', 'cygnusair' ), 'cygnus_avion_metabox_html', 'avion', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'cygnus_avion_metabox' );

/**
 * HTML de la caja.
 *
 * @param WP_Post $post Avión.
 */
function cygnus_avion_metabox_html( $post ) {
	wp_nonce_field( 'cygnus_avion_save', 'cygnus_avion_nonce' );
	$etiqueta = get_post_meta( $post->ID, 'cygnus_etiqueta', true );
	?>
	<style>
		.cygnus-ficha{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 24px;margin-top:8px}
		.cygnus-ficha label{display:block;font-weight:600;margin-bottom:4px}
		.cygnus-ficha input[type=text],.cygnus-ficha input[type=url]{width:100%}
		.cygnus-ficha .full{grid-column:1 / -1}
		.cygnus-ficha .unit{color:#646970;font-weight:400}
		@media (max-width:782px){.cygnus-ficha{grid-template-columns:1fr}}
	</style>
	<p><?php esc_html_e( 'Escriba las cifras sin unidades. Use coma para los decimales (47,32). Las cifras se animan en la web; un texto como «4.500–5.000» se muestra tal cual. Los campos vacíos no se muestran.', 'cygnusair' ); ?></p>
	<div class="cygnus-ficha">
		<p class="full">
			<label for="cygnus_etiqueta"><?php esc_html_e( 'Texto pequeño sobre el nombre', 'cygnusair' ); ?></label>
			<input type="text" id="cygnus_etiqueta" name="cygnus_etiqueta" value="<?php echo esc_attr( $etiqueta ); ?>" placeholder="<?php esc_attr_e( 'Flota actual', 'cygnusair' ); ?>">
		</p>
		<?php foreach ( cygnus_avion_fields() as $key => $f ) : ?>
			<p>
				<label for="cygnus_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f[0] ); ?> <span class="unit">(<?php echo esc_html( $f[1] ); ?>)</span></label>
				<input type="text" id="cygnus_<?php echo esc_attr( $key ); ?>" name="cygnus_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, 'cygnus_' . $key, true ) ); ?>" placeholder="<?php echo esc_attr( $f[2] ); ?>">
			</p>
		<?php endforeach; ?>
		<p class="full">
			<label for="cygnus_motores"><?php esc_html_e( 'Planta motriz', 'cygnusair' ); ?></label>
			<input type="text" id="cygnus_motores" name="cygnus_motores" value="<?php echo esc_attr( get_post_meta( $post->ID, 'cygnus_motores', true ) ); ?>" placeholder="2× Rolls-Royce RB211-535E4 o Pratt &amp; Whitney PW2037 / PW2040 / PW2043">
		</p>
		<p>
			<label for="cygnus_peso_vacio"><?php esc_html_e( 'Peso vacío', 'cygnusair' ); ?> <span class="unit">(kg)</span></label>
			<input type="text" id="cygnus_peso_vacio" name="cygnus_peso_vacio" value="<?php echo esc_attr( get_post_meta( $post->ID, 'cygnus_peso_vacio', true ) ); ?>" placeholder="58.390">
		</p>
		<p>
			<label for="cygnus_ficha"><?php esc_html_e( 'Ficha técnica en PDF', 'cygnusair' ); ?></label>
			<input type="url" id="cygnus_ficha" name="cygnus_ficha" value="<?php echo esc_attr( get_post_meta( $post->ID, 'cygnus_ficha', true ) ); ?>" placeholder="https://…/ficha.pdf">
			<button type="button" class="button" id="cygnus_ficha_btn" style="margin-top:6px"><?php esc_html_e( 'Elegir o subir PDF', 'cygnusair' ); ?></button>
		</p>
	</div>
	<p class="description"><?php esc_html_e( 'La foto se elige en «Foto del avión» (columna derecha). El orden en la web se cambia en «Atributos → Orden» (primero el número más bajo).', 'cygnusair' ); ?></p>
	<script>
	( function () {
		var btn = document.getElementById( 'cygnus_ficha_btn' ), frame;
		if ( ! btn || ! window.wp || ! wp.media ) { return; }
		btn.addEventListener( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( { title: <?php echo wp_json_encode( __( 'Ficha técnica en PDF', 'cygnusair' ) ); ?>, library: { type: 'application/pdf' }, multiple: false } );
				frame.on( 'select', function () { document.getElementById( 'cygnus_ficha' ).value = frame.state().get( 'selection' ).first().get( 'url' ); } );
			}
			frame.open();
		} );
	} )();
	</script>
	<?php
}

/**
 * Carga el selector de medios en la pantalla del avión (para el PDF).
 *
 * @param string $hook Pantalla.
 */
function cygnus_avion_admin_assets( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && 'avion' === get_post_type() ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'cygnus_avion_admin_assets' );

/**
 * Guarda la ficha.
 *
 * @param int $post_id Avión.
 */
function cygnus_avion_save( $post_id ) {
	if ( ! isset( $_POST['cygnus_avion_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['cygnus_avion_nonce'] ), 'cygnus_avion_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$keys = array_merge( array( 'etiqueta', 'motores', 'peso_vacio' ), array_keys( cygnus_avion_fields() ) );
	foreach ( $keys as $key ) {
		$name = 'cygnus_' . $key;
		if ( isset( $_POST[ $name ] ) ) {
			update_post_meta( $post_id, $name, sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) );
		}
	}
	if ( isset( $_POST['cygnus_ficha'] ) ) {
		update_post_meta( $post_id, 'cygnus_ficha', esc_url_raw( wp_unslash( $_POST['cygnus_ficha'] ) ) );
	}
}
add_action( 'save_post_avion', 'cygnus_avion_save' );

/**
 * Columnas del listado de aviones: foto y orden.
 *
 * @param array $cols Columnas.
 * @return array
 */
function cygnus_avion_columns( $cols ) {
	return array(
		'cb'           => $cols['cb'],
		'cygnus_foto'  => __( 'Foto', 'cygnusair' ),
		'title'        => __( 'Avión', 'cygnusair' ),
		'cygnus_orden' => __( 'Orden', 'cygnusair' ),
		'date'         => $cols['date'],
	);
}
add_filter( 'manage_avion_posts_columns', 'cygnus_avion_columns' );

/**
 * Contenido de las columnas.
 *
 * @param string $col     Columna.
 * @param int    $post_id Avión.
 */
function cygnus_avion_column_content( $col, $post_id ) {
	if ( 'cygnus_foto' === $col ) {
		echo get_the_post_thumbnail( $post_id, array( 90, 60 ) );
	} elseif ( 'cygnus_orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_avion_posts_custom_column', 'cygnus_avion_column_content', 10, 2 );

/**
 * Aviones publicados, en su orden.
 *
 * @return WP_Post[]
 */
function cygnus_aviones() {
	return get_posts(
		array(
			'post_type'      => 'avion',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		)
	);
}

/**
 * Panel de un avión en la flota de la portada.
 *
 * @param WP_Post $avion Avión.
 */
function cygnus_render_avion( $avion ) {
	$id       = $avion->ID;
	$title    = get_the_title( $avion );
	$etiqueta = get_post_meta( $id, 'cygnus_etiqueta', true );
	$motores  = get_post_meta( $id, 'cygnus_motores', true );
	$vacio    = get_post_meta( $id, 'cygnus_peso_vacio', true );
	$ficha    = get_post_meta( $id, 'cygnus_ficha', true );
	// "Boeing 757-200PCF" → "Boeing<br>757-200PCF".
	$parts = explode( ' ', $title, 2 );
	?>
	<div class="panel panel--plane">
		<div class="speedlines" aria-hidden="true"></div>
		<div class="pic">
			<?php
			if ( has_post_thumbnail( $avion ) ) {
				echo get_the_post_thumbnail( $avion, 'cygnus-avion', array( 'alt' => esc_attr( $title ) ) );
			}
			?>
		</div>
		<div>
			<div class="eyebrow"><?php echo esc_html( $etiqueta ? $etiqueta : __( 'Flota actual', 'cygnusair' ) ); ?></div>
			<h3 class="display" style="margin-top:14px"><?php echo esc_html( $parts[0] ); ?><?php echo isset( $parts[1] ) ? '<br>' . esc_html( $parts[1] ) : ''; ?></h3>
			<dl class="specs">
				<?php
				foreach ( cygnus_avion_fields() as $key => $f ) :
					$value = get_post_meta( $id, 'cygnus_' . $key, true );
					if ( '' === trim( (string) $value ) ) {
						continue;
					}
					?>
					<div><dt><?php echo esc_html( $f[0] ); ?></dt><dd><?php echo cygnus_number( $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><small><?php echo esc_html( $f[1] ); ?></small></dd></div>
				<?php endforeach; ?>
			</dl>
			<?php if ( $motores || $vacio ) : ?>
				<p class="engines">
					<?php
					$bits = array();
					if ( $motores ) {
						$bits[] = sprintf( /* translators: %s: motores */ __( 'Planta motriz: %s', 'cygnusair' ), $motores );
					}
					if ( $vacio ) {
						$bits[] = sprintf( /* translators: %s: peso */ __( 'Peso vacío %s kg', 'cygnusair' ), $vacio );
					}
					echo esc_html( implode( ' · ', $bits ) );
					?>
				</p>
			<?php endif; ?>
			<?php if ( $ficha ) : ?>
				<a class="btn" href="<?php echo esc_url( $ficha ); ?>" target="_blank" rel="noopener" data-hover><?php echo esc_html( sprintf( /* translators: %s: avión */ __( 'Descargar ficha %s ↓', 'cygnusair' ), $title ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

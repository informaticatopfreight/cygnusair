<?php
/**
 * Apariencia → Comunicado y contacto: comunicado de emergencia y correo del formulario.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

/**
 * Texto de ejemplo del comunicado.
 *
 * @return string
 */
function cygnus_comunicado_ejemplo() {
	return "Cygnus Air lamenta comunicar que la aeronave con matrícula EC-XXX, que operaba el vuelo BCSXXX entre [origen] y [destino], ha sufrido un accidente en [lugar] durante la fase de [despegue / ascenso / crucero / aproximación / aterrizaje].\n\nLa compañía colabora con las autoridades y servicios de emergencia, y ofrecerá más información en cuanto disponga de datos confirmados. Nuestros pensamientos están con las personas afectadas y sus familias.";
}

/**
 * Ajustes del comunicado con sus valores por defecto.
 *
 * @return array
 */
function cygnus_comunicado() {
	$defaults = array(
		'activo'   => 0,
		'titulo'   => 'Comunicado de Cygnus Air',
		'fecha'    => '',
		'texto'    => '',
		'telefono' => '',
	);
	return wp_parse_args( (array) get_option( 'cygnus_comunicado', array() ), $defaults );
}

/**
 * ¿Hay que mostrar el comunicado?
 *
 * @return bool
 */
function cygnus_comunicado_activo() {
	$c = cygnus_comunicado();
	return ! empty( $c['activo'] ) && '' !== trim( $c['texto'] );
}

/**
 * Datos que recibe crisis.js.
 *
 * @return array
 */
function cygnus_comunicado_publico() {
	$c = cygnus_comunicado();
	return array(
		'activo'   => cygnus_comunicado_activo(),
		'titulo'   => $c['titulo'],
		'fecha'    => $c['fecha'],
		'texto'    => $c['texto'],
		'telefono' => $c['telefono'],
	);
}

/**
 * Correo que recibe los mensajes del formulario.
 *
 * @return string
 */
function cygnus_contacto_email() {
	$email = get_option( 'cygnus_contacto_email', '' );
	return is_email( $email ) ? $email : get_option( 'admin_email' );
}

/**
 * Registra los ajustes.
 */
function cygnus_register_settings() {
	register_setting(
		'cygnus_ajustes',
		'cygnus_comunicado',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'cygnus_sanitize_comunicado',
		)
	);
	register_setting(
		'cygnus_ajustes',
		'cygnus_contacto_email',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
		)
	);
}
add_action( 'admin_init', 'cygnus_register_settings' );

/**
 * Limpia el comunicado.
 *
 * @param array $in Valores del formulario.
 * @return array
 */
function cygnus_sanitize_comunicado( $in ) {
	$in = (array) $in;
	return array(
		'activo'   => empty( $in['activo'] ) ? 0 : 1,
		'titulo'   => sanitize_text_field( $in['titulo'] ?? '' ),
		'fecha'    => sanitize_text_field( $in['fecha'] ?? '' ),
		'texto'    => sanitize_textarea_field( $in['texto'] ?? '' ),
		'telefono' => sanitize_text_field( $in['telefono'] ?? '' ),
	);
}

/**
 * Página del panel.
 */
function cygnus_settings_page() {
	add_theme_page( __( 'Comunicado y contacto', 'cygnusair' ), __( 'Comunicado y contacto', 'cygnusair' ), 'edit_theme_options', 'cygnus-ajustes', 'cygnus_settings_page_html' );
}
add_action( 'admin_menu', 'cygnus_settings_page' );

/**
 * HTML de la página del panel.
 */
function cygnus_settings_page_html() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$c = cygnus_comunicado();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Comunicado y contacto', 'cygnusair' ); ?></h1>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'cygnus_ajustes' ); ?>

			<h2><?php esc_html_e( 'Comunicado de emergencia', 'cygnusair' ); ?></h2>
			<p><?php esc_html_e( 'Cuando está activo, el comunicado aparece al abrir cualquier página, queda una franja fija arriba para volver a leerlo y la web pasa a modo sereno: sin animaciones ni aviones en movimiento y en escala de grises.', 'cygnusair' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Estado', 'cygnusair' ); ?></th>
					<td><label><input type="checkbox" name="cygnus_comunicado[activo]" value="1" <?php checked( ! empty( $c['activo'] ) ); ?>> <strong><?php esc_html_e( 'Mostrar el comunicado en la web', 'cygnusair' ); ?></strong></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="cygnus-com-titulo"><?php esc_html_e( 'Título', 'cygnusair' ); ?></label></th>
					<td><input type="text" class="regular-text" id="cygnus-com-titulo" name="cygnus_comunicado[titulo]" value="<?php echo esc_attr( $c['titulo'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="cygnus-com-fecha"><?php esc_html_e( 'Fecha y hora', 'cygnusair' ); ?></label></th>
					<td><input type="text" class="regular-text" id="cygnus-com-fecha" name="cygnus_comunicado[fecha]" value="<?php echo esc_attr( $c['fecha'] ); ?>" placeholder="<?php echo esc_attr( wp_date( 'j \d\e F \d\e Y · H:i' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Por ejemplo, la hora de la última actualización.', 'cygnusair' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="cygnus-com-texto"><?php esc_html_e( 'Texto', 'cygnusair' ); ?></label></th>
					<td><textarea class="large-text" rows="8" id="cygnus-com-texto" name="cygnus_comunicado[texto]"><?php echo esc_textarea( $c['texto'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Deje una línea en blanco entre párrafos.', 'cygnusair' ); ?> <a href="#" id="cygnus-com-ejemplo"><?php esc_html_e( 'Poner el texto de ejemplo', 'cygnusair' ); ?></a></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="cygnus-com-tel"><?php esc_html_e( 'Teléfono para familiares', 'cygnusair' ); ?></label></th>
					<td><input type="text" class="regular-text" id="cygnus-com-tel" name="cygnus_comunicado[telefono]" value="<?php echo esc_attr( $c['telefono'] ); ?>">
					<p class="description"><?php esc_html_e( 'Opcional. Si se deja vacío no se muestra.', 'cygnusair' ); ?></p></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Formulario de contacto', 'cygnusair' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="cygnus-contacto-email"><?php esc_html_e( 'Enviar los mensajes a', 'cygnusair' ); ?></label></th>
					<td><input type="email" class="regular-text" id="cygnus-contacto-email" name="cygnus_contacto_email" value="<?php echo esc_attr( get_option( 'cygnus_contacto_email', '' ) ); ?>" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"></td>
				</tr>
			</table>
			<?php submit_button( __( 'Guardar cambios', 'cygnusair' ) ); ?>
		</form>
	</div>
	<script>
	( function () {
		var a = document.getElementById( 'cygnus-com-ejemplo' );
		if ( a ) { a.addEventListener( 'click', function ( e ) { e.preventDefault(); document.getElementById( 'cygnus-com-texto' ).value = <?php echo wp_json_encode( cygnus_comunicado_ejemplo() ); ?>; } ); }
	} )();
	</script>
	<?php
}

/**
 * Aviso en la barra superior mientras el comunicado está activo, para no olvidarlo.
 *
 * @param WP_Admin_Bar $bar Barra.
 */
function cygnus_admin_bar_comunicado( $bar ) {
	if ( ! cygnus_comunicado_activo() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'cygnus-comunicado',
			'title' => '<span style="background:#b32d2e;color:#fff;padding:2px 8px;border-radius:3px">' . esc_html__( 'Comunicado activo', 'cygnusair' ) . '</span>',
			'href'  => admin_url( 'themes.php?page=cygnus-ajustes' ),
		)
	);
}
add_action( 'admin_bar_menu', 'cygnus_admin_bar_comunicado', 80 );

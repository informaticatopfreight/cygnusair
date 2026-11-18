<?php
/**
 * Formulario de contacto de la portada: envía el mensaje por correo.
 *
 * @package cygnusair
 */

defined( 'ABSPATH' ) || exit;

/**
 * Recibe el formulario.
 */
function cygnus_contacto_enviar() {
	$back = home_url( '/' );
	$fail = function ( $code ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'contacto', $code, $back ) . '#contacto' );
		exit;
	};

	if ( ! isset( $_POST['_cygnus_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_cygnus_nonce'] ), 'cygnus_contacto' ) ) {
		$fail( 'caducado' );
	}
	// Campo trampa para robots: las personas no lo ven ni lo rellenan.
	if ( ! empty( $_POST['web'] ) ) {
		$fail( 'ok' );
	}

	$nombre  = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$mensaje = sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ?? '' ) );

	if ( '' === $nombre || ! is_email( $email ) || '' === $mensaje ) {
		$fail( 'incompleto' );
	}
	if ( empty( $_POST['privacidad'] ) ) {
		$fail( 'privacidad' );
	}

	// Un mensaje cada 30 segundos por dirección IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'cygnus_contacto_' . md5( $ip );
	if ( get_transient( $key ) ) {
		$fail( 'espera' );
	}
	set_transient( $key, 1, 30 );

	$subject = sprintf( /* translators: %s: nombre */ __( 'Mensaje desde la web de Cygnus Air: %s', 'cygnusair' ), $nombre );
	$body    = sprintf(
		"%s: %s\n%s: %s\n\n%s\n\n—\n%s",
		__( 'Nombre', 'cygnusair' ),
		$nombre,
		__( 'Correo', 'cygnusair' ),
		$email,
		mb_substr( $mensaje, 0, 5000 ),
		home_url( '/' )
	);
	$headers = array( 'Reply-To: ' . $nombre . ' <' . $email . '>' );

	$sent = wp_mail( cygnus_contacto_email(), $subject, $body, $headers );
	$fail( $sent ? 'ok' : 'fallo' );
}
add_action( 'admin_post_nopriv_cygnus_contacto', 'cygnus_contacto_enviar' );
add_action( 'admin_post_cygnus_contacto', 'cygnus_contacto_enviar' );

/**
 * Aviso que se muestra sobre el formulario después de enviarlo.
 */
function cygnus_contacto_aviso() {
	$code = isset( $_GET['contacto'] ) ? sanitize_key( $_GET['contacto'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$msgs = array(
		'ok'         => array( 'ok', __( 'Mensaje enviado. Le responderemos lo antes posible.', 'cygnusair' ) ),
		'incompleto' => array( 'error', __( 'Revise el formulario: faltan el nombre, un correo válido o el mensaje.', 'cygnusair' ) ),
		'privacidad' => array( 'error', __( 'Para enviar el mensaje hay que aceptar la política de privacidad.', 'cygnusair' ) ),
		'espera'     => array( 'error', __( 'Acaba de enviar un mensaje. Espere unos segundos antes de enviar otro.', 'cygnusair' ) ),
		'caducado'   => array( 'error', __( 'La página llevaba mucho tiempo abierta. Vuelva a enviar el mensaje.', 'cygnusair' ) ),
		'fallo'      => array( 'error', __( 'No se ha podido enviar el mensaje. Escríbanos directamente a info@ygnusair.com.', 'cygnusair' ) ),
	);
	if ( isset( $msgs[ $code ] ) ) {
		printf( '<p class="form__msg form__msg--%s" role="status">%s</p>', esc_attr( $msgs[ $code ][0] ), esc_html( $msgs[ $code ][1] ) );
	}
}

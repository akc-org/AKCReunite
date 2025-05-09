<?php

/**
 * SearchWP License.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class License is responsible for managing a SearchWP license.
 *
 * @since 4.0
 */
class License {

	/**
	 * The license types.
	 * Array key stands for EDD Price IDs corresponding to the license type.
	 *
	 * @since 4.2.2
	 */
	const LICENSE_TYPES = [
		0 => 'standard',
		1 => 'pro',
		2 => 'agency',
		3 => 'agency', // Agency Unlimited.
	];

	/**
	 * The license data.
	 *
	 * @since 4.0
	 * @var object
	 */
	private static $data;

	/**
	 * The license key.
	 *
	 * @since 4.0
	 * @var string
	 */
	private static $key;

	/**
	 * License constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		self::$data = Settings::get( 'license' );

		// Allow license key to be defined as a constant.
		if ( defined( 'SEARCHWP_LICENSE_KEY' ) && \SEARCHWP_LICENSE_KEY ) {
			$key = \SEARCHWP_LICENSE_KEY;
		} else {
			$key  = isset( self::$data['key'] ) ? self::$data['key'] : '';
		}

		self::$key = trim( sanitize_text_field( apply_filters( 'searchwp\license\key', $key ) ) );

		if ( ( empty( self::$data ) || ! isset( self::$data['key'] ) ) && ! empty( self::$key ) ) {
			// License key was added programmatically, we need to activate it.
			self::activate( self::$key );
		}

		add_action( SEARCHWP_PREFIX . 'maintenance', [ __CLASS__, 'maintenance' ] );

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'license_get',   [ __CLASS__, 'get' ] );

		new Updater( SEARCHWP_EDD_STORE_URL, SEARCHWP_PLUGIN_FILE, [
			'version'   => SEARCHWP_VERSION,
			'license'   => self::$key,
			'item_id'   => 216029,
			'item_name' => urlencode( SEARCHWP_EDD_ITEM_NAME ),
			'author'    => 'SearchWP',
		] );
	}

	/**
	 * Getter for license.
	 *
	 * @since 4.0
	 *
	 * @return mixed
	 */
	public static function get() {

		Utils::check_ajax_permissions();

		wp_send_json_success( Settings::get( 'license' ) );
	}

	/**
	 * Activates the submitted license key.
	 *
	 * @since 4.0
	 * @param string $key The license key to activate.
	 * @return array|boolean The result of activation.
	 */
	public static function activate( $key ) {
		$api_params = [
			'edd_action' => 'activate_license',
			'license'    => $key,
			'item_name'  => rawurlencode( SEARCHWP_EDD_ITEM_NAME ),
			'url'        => home_url(),
		];

		$response = wp_remote_post(
			SEARCHWP_EDD_STORE_URL,
			[
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			]
		);

		$parsed_response = self::handle_activation_response( $response );

		if ( $parsed_response['success'] ) {
			self::$data = array_merge( $parsed_response['data'], [ 'key' => $key ] );

			Settings::update( 'license', self::$data );
		} else {
			Settings::update( 'license', false );
		}

		return $parsed_response;
	}

	/**
	 * Deactivate the submitted license key
	 *
	 * @since 4.0
	 * @param string $key The license key to deactivate.
	 * @return array|false The response from deactivation.
	 */
	public static function deactivate( $key ) {
		$api_params = [
			'edd_action' => 'deactivate_license',
			'license'    => $key,
			'item_name'  => rawurlencode( SEARCHWP_EDD_ITEM_NAME ),
			'url'        => home_url(),
		];

		$response = wp_remote_post(
			SEARCHWP_EDD_STORE_URL,
			[
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			]
		);

		$parsed_response = self::handle_deactivation_response( $response );

		if ( $parsed_response['success'] ) {
			Settings::update( 'license', [
				'status'    => $parsed_response['data']->license,
				'expires'   => $parsed_response['data']->expires,
				'remaining' => '',
			] );
		} else {
			Settings::update( 'license', false );
		}

		return $parsed_response;
	}

	/**
	 * Handler for deactivation request response
	 *
	 * @since 4.0
	 * @param array $response The response from wp_remote_post() call to licensing server.
	 * @return array
	 */
	public static function handle_deactivation_response( $response ) {
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'searchwp' );
			}

			return [
				'success' => false,
				'data'    => $message,
			];
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 'deactivated' === $license_data->license ) {
			Settings::update( 'license', false );
		}

		// License activation was a success.
		return [
			'success' => true,
			'data'    => $license_data,
		];
	}

	/**
	 * Wether the License is active or not.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public static function is_active() {
		return ! empty( self::$data['status'] ) && 'valid' === self::$data['status'];
	}

	/**
	 * Getter for license data
	 *
	 * @since 4.0
	 * @return array The license data.
	 */
	public static function get_data() {
		return self::$data;
	}

	/**
	 * Getter for license key
	 *
	 * @since 4.0
	 * @return string The license key.
	 */
	public static function get_key() {
		return self::$key;
	}

	/**
	 * Getter for license type.
	 *
	 * @since 4.2.2
	 *
	 * @return string The license type.
	 */
	public static function get_type() {

		// Update the license data from remote if the license is active but 'type' key is missing.
		if ( ! isset( self::$data['type'] ) && self::is_active() ) {
			self::maintenance();
		}

		return self::$data['type'] ?? '';
	}

	/**
	 * Callback for WP_Cron event that updates license status once a day.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function maintenance() {

		$api_params = [
			'edd_action' => 'check_license',
			'license'    => self::$key,
			'item_name'  => rawurlencode( SEARCHWP_EDD_ITEM_NAME ),
			'url'        => home_url(),
		];

		$api_args = [
			'timeout'   => 30,
			'sslverify' => false,
			'body'      => $api_params,
		];

		$response = wp_remote_post( SEARCHWP_EDD_STORE_URL, $api_args );

		if ( is_wp_error( $response ) ) {
			return;
		}

		$parsed_response        = self::handle_activation_response( $response );
		$license_check_attempts = Settings::get( 'license_check_attempts', 'absint' );

		if ( ! $parsed_response['success'] && $license_check_attempts < 2 ) {
			Settings::update( 'license_check_attempts', $license_check_attempts + 1 );
		}

		if ( ! $parsed_response['success'] && $license_check_attempts >= 2 ) {
			Settings::update( 'license', false );
			Settings::delete( 'license_check_attempts' );
		}

		if ( $parsed_response['success'] ) {
			self::$data        = $parsed_response['data'];
			self::$data['key'] = self::$key;

			Settings::update( 'license', self::$data );
			Settings::delete( 'license_check_attempts' );
		}
	}

	/**
	 * Returns whether notice(s) should be shown when the license is inactive.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public static function inactive_license_notice() {
		return ! self::is_active() && apply_filters( 'searchwp\license\inactive_notice', true );
	}

	/**
	 * Handler for activation request response.
	 *
	 * @since 4.0
	 * @param array $response The response from wp_remote_post() call to licensing server.
	 */
	public static function handle_activation_response( $response ) {

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'searchwp' );
			}

			return [
				'success' => false,
				'data'    => $message,
			];
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success && ! empty( $license_data->error ) ) {
			switch ( $license_data->error ) {
				case 'expired':
					$message = sprintf(
						// Translators: %s is the date the license expired.
						__( 'Your license key expired on %s', 'searchwp' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled':
				case 'revoked':
					$message = __( 'Your license key has been disabled', 'searchwp' );
					break;

				case 'missing':
					$message = __( 'Invalid license', 'searchwp' );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL', 'searchwp' );
					break;

				case 'item_name_mismatch':
					// Translators: %s is the name of this plugin.
					$message = sprintf( __( 'This appears to be an invalid license key for %s' ), SEARCHWP_EDD_ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit', 'searchwp' );
					break;

				default:
					$message = __( 'An error occurred, please try again', 'searchwp' );
					break;
			}
		} else if ( ! empty( $license_data->error ) ) {
			$message = __( 'An error occurred, please try again', 'searchwp' );
		} else if ( isset( $license_data->license ) && 'invalid' === $license_data->license ) {
			$message = __( 'Invalid license', 'searchwp' );
		}

		// A populated message constitutes an error.
		if ( ! empty( $message ) ) {
			return [
				'success' => false,
				'data'    => $message,
			];
		}

		// Generate human readable remaining time.
		if ( 'lifetime' === $license_data->expires ) {
			$expiration = __( 'Lifetime license', 'searchwp' );
		} else {
			$expiration = sprintf(
				// Translators: First placeholder is the date of license expiration, second placeholder is the time until the license expires.
				__( 'Active until %s (%s)', 'searchwp' ),
				date( 'M j, Y', strtotime( $license_data->expires ) ),
				human_time_diff( strtotime( $license_data->expires ), current_time( 'timestamp' ) )
			);
		}

		// Assign the lowest license type as a fallback.
		$type = self::LICENSE_TYPES[0];

		// Translate the EDD Price ID into the license type.
		if ( isset( $license_data->price_id ) && array_key_exists( $license_data->price_id, self::LICENSE_TYPES ) ) {
			$type = self::LICENSE_TYPES[ $license_data->price_id ];
		}

		// License activation was a success.
		return [
			'success' => true,
			'data'    => [
				'status'    => $license_data->license,
				'expires'   => $license_data->expires,
				'remaining' => $expiration,
				'type'      => $type,
			],
		];
	}
}

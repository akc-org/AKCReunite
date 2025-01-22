<?php
/**
 * Class definition file for the Onetrust Settings Page
 *
 * @package AKCReunite
 */

/**
 * Onetrust_settings class definition
 *
 * @package AKCReunite
 */
class Onetrust_settings {

	/**
	 * Variable to store the options.
	 *
	 * @var option_field
	 */
	private $option_field;

	/**
	 * Constructor for the Onetrust settings page class
	 *
	 * @since 1.1
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'Onetrust_add_options_page' ) );
		add_action( 'admin_init', array( $this, 'Onetrust_register_settings' ) );
		add_action( 'wp_head', array( $this, 'Onetrust_embed' ) );
	}

	/**
	 * Add the options page for the Onetrust code
	 *
	 * @since 1.1
	 */
	public function Onetrust_add_options_page() {
		// Add options page under Settings menu.
		add_options_page(
			'Onetrust Container ID Settings',
			'Onetrust Settings',
			'manage_options',
			'Onetrust-container-options',
			array( $this, 'Onetrust_render_form' )
		);
	}

	/**
	 * Function to register the setting
	 *
	 * @since 1.1
	 */
	public function Onetrust_register_settings() {
		// Register the setting.
		register_setting(
			'Onetrust-container-options-group',
			'Onetrust_container_id'
		);

		// Add a settings section.
		add_settings_section(
			'Onetrust-container-settings-section',
			'Onetrust Container ID Settings',
			null,
			'Onetrust-container-options'
		);

		// Add the settings field for the Onetrust Container ID.
		add_settings_field(
			'Onetrust_container_id',
			'Onetrust Container ID',
			array( $this, 'Onetrust_container_id_field_callback' ),
			'Onetrust-container-options',
			'Onetrust-container-settings-section'
		);
	}

	/**
	 * Render the settings page form for the Onetrust page.
	 *
	 * @since 1.1
	 */
	public function Onetrust_render_form() {
		?>
		<div class="wrap">
			<h1>Onetrust Settings</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'Onetrust-container-options-group' );
				do_settings_sections( 'Onetrust-container-options' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Function to register the setting
	 *
	 * @since 1.1
	 */
	public function Onetrust_container_id_field_callback() {
		// Render the input field for the Onetrust Container ID.
		$value = get_option( 'Onetrust_container_id' );
		echo '<input type="text" id="' . esc_attr( 'Onetrust_container_id' ) . '" name="' . esc_attr( 'Onetrust_container_id' ) . '" value="' . esc_attr( $value ) . '" />';
	}

	/**
	 * Function to create the embed code for Onetrust and put it on the page
	 *
	 * @since 1.1
	 */
	public function Onetrust_embed() {
		$id = get_option( 'Onetrust_container_id', true );
		if ( $id && '' !== $id ) {
			echo '<!-- OneTrust Cookies Consent Notice start for akcreunite.org -->
            <script src="https://cdn.cookielaw.org/consent/' . $id . '/otSDKStub.js"  type="text/javascript" charset="UTF-8" data-domain-script="' . $id . '" ></script>
            <script type="text/javascript">
            function OptanonWrapper() { }
            </script>
			<style>
				body div#ot-sdk-btn-floating {
				display: block !important;
			}
			</style>
            <!-- OneTrust Cookies Consent Notice end for akcreunite.org -->
            ';
		}
	}
}

new Onetrust_settings();


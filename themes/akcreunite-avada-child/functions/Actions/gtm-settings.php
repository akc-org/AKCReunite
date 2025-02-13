<?php 
class Options_Gtm_Container_Id {
    private $option_field;

    public function __construct() {
        $this->option_field = [
            'id' => 'gtm_container_id',
            'label' => 'GTM Container ID'
        ];
        add_action('admin_menu', array($this, 'add_options_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_head', array($this, 'gtm_container_id_embed'));

    }

    public function add_options_page() {
        // Add options page under Settings menu
        add_options_page(
            'GTM Container ID Settings',
            'GTM Settings',
            'manage_options',
            'gtm-container-options',
            array($this, 'render_form')
        );
    }

    public function register_settings() {
        // Register a setting for the container ID
        register_setting(
            'gtm-container-options-group',
            $this->option_field['id']
        );

        // Add a settings section
        add_settings_section(
            'gtm-container-settings-section',
            'GTM Container ID Settings',
            null,
            'gtm-container-options'
        );

        // Add the settings field for the GTM Container ID
        add_settings_field(
            $this->option_field['id'],
            $this->option_field['label'],
            array($this, 'gtm_container_id_field_callback'),
            'gtm-container-options',
            'gtm-container-settings-section'
        );
    }

    public function render_form() {
        ?>
        <div class="wrap">
            <h1>GTM Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('gtm-container-options-group');
                do_settings_sections('gtm-container-options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function gtm_container_id_field_callback() {
        // Render the input field for the GTM Container ID
        $value = get_option($this->option_field['id']);
        echo '<input type="text" id="' . esc_attr($this->option_field['id']) . '" name="' . esc_attr($this->option_field['id']) . '" value="' . esc_attr($value) . '" />';
    }

    public function gtm_container_id_embed() {
		$id = get_option( 'gtm_container_id', true );
		if ( $id && '' !== $id ) {
			echo "<!-- Google Tag Manager -->
            <script type='text/plain' class='optanon-category-C0002'>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','" . $id . "');</script>
            <!-- End Google Tag Manager -->

            ";
		}
	}
}
new Options_Gtm_Container_Id();

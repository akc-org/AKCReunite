<?php

/**
 * Build "Add new" screen
 *
 * @return void
 */
function i_world_map_add_new() {

	$class = '';

	if ( isset( $_POST['action'] ) ) {

		// check nonce
		if ( 'editmap' === sanitize_text_field( $_POST['action'] ) ) {
			if (
				! isset( $_POST['edit-map-nonce'] )
				|| ! wp_verify_nonce( $_POST['edit-map-nonce'], 'edit-map' )
			) {
				return __( 'Something went wrong.', 'interactive-world-maps' );
			}
		}

		if ( 'addmap' === sanitize_text_field( $_POST['action'] ) ) {
			if (
				! isset( $_POST['post-map-nonce'] )
				|| ! wp_verify_nonce( $_POST['post-map-nonce'], 'post-map' )
			) {
				return __( 'Something went wrong.', 'interactive-world-maps' );
			}
		}

		global $wpdb;
		$name          = sanitize_text_field( $_POST['name'] );
		$description   = wp_kses_post( $_POST['description'] );
		$use_defaults  = sanitize_text_field( $_POST['use_defaults'] );
		$border_color  = sanitize_text_field( $_POST['border_color'] );
		$border_stroke = sanitize_text_field( $_POST['border_stroke'] );
		$bg_color      = sanitize_text_field( $_POST['bg_color'] );
		$ina_color     = sanitize_text_field( $_POST['ina_color'] );
		$act_color     = sanitize_text_field( $_POST['act_color'] );
		$marker_size   = sanitize_text_field( $_POST['marker_size'] );
		$width         = sanitize_text_field( $_POST['width'] );
		$height        = sanitize_text_field( $_POST['height'] );

		$aspect_ratio = 0;
		$interactive  = 0;
		$tooltipt     = 0;

		if ( isset( $_POST['aspect_ratio'] ) ) {
			$aspect_ratio = sanitize_text_field( $_POST['aspect_ratio'] ); }
		if ( isset( $_POST['interactive'] ) ) {
			$interactive = sanitize_text_field( $_POST['interactive'] ); }
		if ( isset( $_POST['tooltipt'] ) ) {
			$tooltipt = sanitize_text_field( $_POST['tooltipt'] ); }

		$region        = sanitize_text_field( $_POST['region'] );
		$display_mode  = sanitize_text_field( $_POST['display_mode'] );
		$places        = $_POST['places']; // can't sanitize or it will break the syntax
		$map_action    = sanitize_text_field( $_POST['map_action'] );
		$custom_action = $_POST['custom_action']; // can't sanitize since it will include javascript code

		$image = isset( $_POST['mapimage'] ) ? sanitize_text_field( $_POST['mapimage'] ) : '';
		$css   = $_POST['customcss']; // custom css code, can't sanitize or it will break syntax

		$table_name_imap = i_world_map_table_name();

		if ( $_POST['action'] === 'addmap' ) {

			if ( $wpdb->insert(
				$table_name_imap,
				array(
					'name'          => $name,
					'description'   => $description,
					'use_defaults'  => $use_defaults,
					'bg_color'      => $bg_color,
					'border_color'  => $border_color,
					'border_stroke' => $border_stroke,
					'ina_color'     => $ina_color,
					'act_color'     => $act_color,
					'marker_size'   => $marker_size,
					'width'         => $width,
					'height'        => $height,
					'aspect_ratio'  => $aspect_ratio,
					'interactive'   => $interactive,
					'showtooltip'   => $tooltipt,
					'region'        => $region,
					'display_mode'  => $display_mode,
					'custom_action' => stripslashes( $custom_action ),
					'map_action'    => $map_action,
					'places'        => stripslashes( $places ),
					'image'         => $image,
					'custom_css'    => $css,
				)
			) ) {
				$alert = 'New Map Added';
				$class = 'updated';
			} else {
				$class = 'error';
				$alert = 'ERROR: Map NOT Added. Most likely a problem with the database.';
			}

				i_world_map_build_form( 'edit-map', $wpdb->insert_id, $alert, $class );

		}

		if ( $_POST['action'] === 'editmap' ) {

			$id = sanitize_key( $_POST['id'] );

			if ( $wpdb->update(
				$table_name_imap,
				array(
					'name'          => $name,
					'description'   => $description,
					'use_defaults'  => $use_defaults,
					'bg_color'      => $bg_color,
					'border_color'  => $border_color,
					'border_stroke' => $border_stroke,
					'ina_color'     => $ina_color,
					'act_color'     => $act_color,
					'marker_size'   => $marker_size,
					'width'         => $width,
					'height'        => $height,
					'aspect_ratio'  => $aspect_ratio,
					'interactive'   => $interactive,
					'showtooltip'   => $tooltipt,
					'region'        => $region,
					'display_mode'  => $display_mode,
					'map_action'    => $map_action,
					'custom_action' => stripslashes( $custom_action ),
					'places'        => stripslashes( $places ),
					'image'         => $image,
					'custom_css'    => $css,
				),
				array( 'id' => $id )
			) ) {
				$alert = 'Map Updated';
				$class = 'updated';
			} else {
				$alert = 'Map NOT Updated. Where there any changes?';
				$class = 'error';
			}
		}
	}
	// special if condition to run after the new map is created
	if ( isset( $_POST['action'] ) && ( $_POST['action'] === 'editmap' ) && ( ! isset( $_GET['action'] ) ) &&  wp_verify_nonce( $_POST['edit-map-nonce'], 'edit-map' ) ) {
		$id = sanitize_key( $_POST['id'] );
		i_world_map_build_form( 'edit-map', $id, $alert, $class );
	}

	if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'edit' ) ) {

		if ( ! isset( $_POST ) ) {
			$alert = ''; }
		if ( ! isset( $alert ) ) {
			$alert = ''; }
		if ( ! isset( $id ) ) {
			$id = sanitize_key( $_GET['map'] ); }

		i_world_map_build_form( 'edit-map', $id, $alert, $class );

	}

	if ( ! isset( $_GET['action'] ) && ( ! isset( $_POST['action'] ) ) ) {
		if ( ! isset( $alert ) ) {
			$alert = ''; }
		i_world_map_build_form( 'post-map', 0, $alert, $class );
	}
}

/**
 * Output form to create or edit map
 *
 * @param [string] $type - to set if it's the edit form or add new form
 * @param [string] $id - id of the current map
 * @param [string] $message to display
 * @param [string] $class- class for the message to display
 * @return void
 */
function i_world_map_build_form( $type, $id, $alert, $class ) {

	$capability = i_world_map_user_cap();

	if ( ! is_admin() || ! current_user_can( $capability ) ) {
		return;
	}

	$options           = get_option( 'i-world-map-settings' );
	$apikey            = isset( $options['api_key'] ) && $options['api_key'] !== '' ? true : false;
	$projection        = ( array_key_exists( 'map_projection', $options ) ? $options['map_projection'] : 'mercator' );
	$imageicon         = ( array_key_exists( 'imageicon', $options ) ? $options['imageicon'] : '' );
	$imageiconposition = ( array_key_exists( 'imageicon_position', $options ) ? $options['imageicon_position'] : 'center' );

	if ( $type === 'post-map' ) {

		$message  = __( 'Fill out the form and follow the instructions to create your Interactive Map', 'interactive-world-maps' );
		$formname = 'addimap';
		settings_fields( 'i-world-map-plugin-settings' );
		$title = ' Add New Interactive Map';

		$name            = '';
		$description     = '';
		$use_defaults    = 1;
		$border_color    = $options['default_border_color'];
		$border_stroke   = $options['default_border_stroke'];
		$bg_color        = $options['default_bg_color'];
		$ina_color       = $options['default_ina_color'];
		$act_color       = $options['default_act_color'];
		$marker_size     = $options['default_marker_size'];
		$width           = $options['default_width'];
		$height          = $options['default_height'];
		$aspect_ratio    = isset( $options['default_aspect_ratio'] ) ? $options['default_aspect_ratio'] : '1';
		$interactive     = $options['default_interactive'];
		$tooltipt        = $options['default_showtooltip'];
		$region          = $options['default_region'];
		$display_mode    = $options['default_display_mode'];
		$places          = '';
		$customcss       = '';
		$map_action      = 'none';
		$custom_action   = '';
		$submit_action   = 'addmap';
		$submit_bt_value = 'CREATE MAP';

	}
	if ( $type === 'edit-map' ) {

		$mapdata = i_world_map_get_map_data( $id );

		$title     = __( 'Edit Map', 'interactive-world-maps' );
		$shortcode = "<input type='text' class='shc' value='[show-map id=\"" . $id . "\"]'>";
		$php       = "<input class='shc' value='&lt;?php build_i_world_map(" . $id . "); ?&gt;'>";
		// translators: placeholders will be shortcodes
		$message   = sprintf(
			__( 'To add this map to your website, just use the shortcode %1$s on your posts, pages or widgets, or add %2$s to your template.', 'interactive-world-maps' ),
			$shortcode,
			$php
		);

		if ( defined( 'WPB_VC_VERSION' ) ) {
			$urltouse = "<img src='" . plugins_url( 'interactive-world-maps/imgs/visual_composer.png' ) . "'>";
			// translators: placeholder will be a url
			$message .= '<p>' . sprintf(
				__( "You can also use the %s VISUAL COMPOSER to add this map to your page, by choosing the option 'Add Element > Interactive Map'.", 'interactive-world-maps' ),
				$urltouse
			)
				 . '</p>';
		}

		$formname      = 'addimap';
		$name          = $mapdata['name'];
		$description   = $mapdata['description'];
		$use_defaults  = $mapdata['use_defaults'];
		$border_color  = $mapdata['border_color'];
		$border_stroke = $mapdata['border_stroke'];
		$bg_color      = $mapdata['bg_color'];
		$ina_color     = $mapdata['ina_color'];
		$act_color     = $mapdata['act_color'];
		$marker_size   = $mapdata['marker_size'];
		$width         = $mapdata['width'];
		$height        = $mapdata['height'];
		$aspect_ratio  = $mapdata['aspect_ratio'];
		$interactive   = $mapdata['interactive'];

		$tooltipt        = $mapdata['showtooltip'];
		$region          = $mapdata['region'];
		$display_mode    = $mapdata['display_mode'];
		$places          = $mapdata['places'];
		$customcss       = $mapdata['custom_css'];
		$map_action      = $mapdata['map_action'];
		$custom_action   = $mapdata['custom_action'];
		$submit_action   = 'editmap';
		$submit_bt_value = 'UPDATE MAP';
	}

	?>
	<div id="iwm-visit">
		<i class="fa fa-info-circle"></i> <?php echo __( 'Visit the <a href="http://cmoreira.net/interactive-world-maps-demo/" target="_blank">Plugin Demo Site</a> for more information and tips on how to use it.', 'interactive-world-maps' ); ?>
	</div>

	<div class="wrap">

		<div id="interactive-world-maps" class="icon32"></div>
		<h2><?php echo esc_html( $title ); ?></h2>

		<?php
		if ( $alert !== '' ) {

			$class = isset( $class ) ? $class : 'error';

			?>
		<div id="iwm_message" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $alert ); ?></div>

			<?php
		}

		// Check if database table exists
		global $wpdb;
		$table_name_imap = i_world_map_table_name();
		$table_name      = $wpdb->prefix . 'i_world_map';

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name_imap ) ) !== $table_name ) {

			$ms = __( 'This is rare, but it can happen when there are limitations on your database. Try to disable and enable the plugin again to check if this message disappears.', 'interactive-world-maps' );

			if ( is_multisite() ) {
				$ms = __( 'Seems you are on a multisite instalation. <a href="https://codecanyon.net/item/interactive-world-maps/2874264/faqs/17881" target="_blank">Check if this information is helpful.</a>', 'interactive-world-maps' ); }

			echo '<div id="message" class="error">'
			. __( 'There is a problem with your database and the maps won\'t be able to be saved.', 'interactive-world-maps' )
			. '<br> ' . $ms . ' <br >'
			. __( 'If the problem persits, contact the plugin support team.', 'interactive-world-maps' ) . '</div>';
		}

		?>

	<div id="iwm-message-intro"><?php echo $message; ?></div>
		<form method="post" action="" id="<?php echo $formname; ?>" name="<?php echo $formname; ?>">
			<?php
				wp_nonce_field( $type, $type . '-nonce' );
			?>
			<table width="100%" cellspacing="5" cellpadding="5">
			<tr>
				<td width="25%" style="min-width:180px;" valign="top"><h3> <?php esc_html_e( 'Details', 'interactive-world-maps' ); ?> </h3>

				<table width="100%" cellspacing="2" cellpadding="2" class="stuffbox" id="name-table">
					<tr valign="top">
						<td><?php esc_html_e( 'Name', 'interactive-world-maps' ); ?><br><input type="text" name="name" value="<?php echo $name; ?>" /></td>
					</tr>
					<tr valign="top">

						<td><?php esc_html_e( 'Description', 'interactive-world-maps' ); ?><br>

							<textarea name="description" cols="20" rows="3"><?php echo $description; ?></textarea></td>
					</tr>
					<?php $editor = isset( $options['default_editor'] ) ? '1' : '0'; ?>
					<input type="hidden" id="editor" name="editor" value="<?php echo $editor; ?>" />

				</table>
				<h3><?php esc_html_e( 'Visual Settings', 'interactive-world-maps' ); ?></h3>
				<table width="100%" cellpadding="2" cellspacing="2" class="stuffbox" id="add-table">
					<tr valign="top">
						<td colspan="2"><input name="use_defaults" id="use_defaults" type="radio" value="1"
							<?php checked( $use_defaults, 1 ); ?> onclick="iwm_hidecustomsettings();" />
							<?php esc_html_e( 'Default', 'interactive-world-maps' ); ?><input name="use_defaults" id="use_defaults" type="radio" value="0"
							<?php checked( $use_defaults, 0 ); ?>onclick="iwm_showcustomsettings();"/>
							<?php esc_html_e( 'Custom', 'interactive-world-maps' ); ?></td>
					</tr>
				</table>
				<div id="default-settings-table-add" class="stuffbox" style="display:none;">
				<table>
					<tr>
						<td><?php esc_html_e( 'Background Colour', 'interactive-world-maps' ); ?> <br> <input type="text" name="bg_color" class="color {hash:true, adjust:false}" value="<?php echo $bg_color; ?>" onchange="iwm_drawVisualization();" /></td>
					</tr>
					<tr>
						<td class="iwmsmall"><i class="fa fa-info-circle"></i> <?php echo __( 'Tip: In color fields you can also use the word "transparent" instead of a color code.' ); ?></td>
					</tr>
					<tr valign="top">
						<td><?php esc_html_e( 'Border Colour', 'interactive-world-maps' ); ?><br><input type="text" name="border_color" class="color {hash:true, adjust:false}" value="<?php echo $border_color; ?>" onchange="iwm_drawVisualization();" /></td>
					</tr>
					<tr valign="top">
						<td><?php esc_html_e( 'Border Width (px)', 'interactive-world-maps' ); ?><br><input type="text" name="border_stroke" value="<?php echo $border_stroke; ?>" onchange="iwm_drawVisualization();" /></td>
					</tr>
					<tr valign="top">
						<td><?php esc_html_e( 'Inactive Region Colour', 'interactive-world-maps' ); ?><br><input type="text" name="ina_color" class="color {hash:true, adjust:false}" value="<?php echo $ina_color; ?>"onchange="iwm_drawVisualization();" /><input type="hidden" name="act_color" class="color {hash:true, adjust:false}" value="<?php echo $act_color; ?>"onchange="iwm_drawVisualization();" /></td>
					</tr>

					<tr valign="top" >
						<td><?php esc_html_e( 'Marker Size', 'interactive-world-maps' ); ?><br><input type="text" name="marker_size" value="<?php echo esc_html( $marker_size ); ?>" onchange="iwm_drawVisualization();" /></td>
					</tr>

					<?php if ( isset( $options['default_responsive'] ) && $options['default_responsive'] === '1' ) { ?>

					<tr valign="top" >

						<td><span class="howto"><?php esc_html_e( 'These settings will be ignored since the responsive mode is enabled.', 'interactive-world-maps' ); ?></span>

						<input type="hidden" name="responsivemode" value="on">

					</td>


					</tr>

					<?php

						} else {

						?>

						<input type="hidden" name="responsivemode" value="off">

						<?php

					}
					?>

					<input type="hidden" name="mapprojection" value="<?php echo $projection; ?>">
					<input type="hidden" name="imageicon" value="<?php echo $imageicon; ?>">
					<input type="hidden" name="imageicon_position" value="<?php echo $imageiconposition; ?>">

					<tr valign="top" >

					<td><?php esc_html_e( 'Width (px)', 'interactive-world-maps' ); ?><br><input type="text" name="width" value="<?php echo $width; ?>" onchange="iwm_drawVisualization();" /></td>
					</tr>
					<tr valign="top" >

					<td><?php esc_html_e( 'Height (px)', 'interactive-world-maps' ); ?><br><input type="text" name="height" value="<?php echo $height; ?>" onchange="iwm_drawVisualization();" /></td>
					</tr>
					<tr valign="top" >

					<td><input name="aspect_ratio"  id="aspratio" type="checkbox" value="1"
					<?php
					if ( $aspect_ratio === '1' ) {
						?>
							checked <?php } ?> onchange="iwm_drawVisualization();" /> <?php esc_html_e( 'Keep Aspect Ratio', 'interactive-world-maps' ); ?></td>
					</tr>


				</table>


				</div>

				<p class="submit">
			<input type="submit" class="button-primary" value="<?php echo $submit_bt_value; ?>" />
		</p>

				</td>
				<td width="75%" valign="top"><h3><?php esc_html_e( 'Map Settings', 'interactive-world-maps' ); ?></h3>
				<table width="100%" cellspacing="5" cellpadding="5" class="stuffbox" id="add-table">
					<tr valign="top" >
					<td><strong><?php esc_html_e( 'Region to Display', 'interactive-world-maps' ); ?></strong>
					<span class="howto"><?php esc_html_e( 'Choose the region you want the map to focus on', 'interactive-world-maps' ); ?></span>
						</td>
					<td><strong><?php esc_html_e( 'Display Mode', 'interactive-world-maps' ); ?></strong>
						<span class="howto"><?php esc_html_e( 'Choose what type of interactive elements you will apply', 'interactive-world-maps' ); ?></span>
					</td>
					<td><strong><?php esc_html_e( 'Active Region Action', 'interactive-world-maps' ); ?></strong>
						<span class="howto"><?php esc_html_e( 'What to do when user clicks active region/marker', 'interactive-world-maps' ); ?></span>
					</td>
					</tr>
					<tr valign="top" >
					<td>
						<?php i_world_map_build_region_select_options( 'region', $region, 'iwm_isolinkcheck()' ); ?><br />

					</td>

					<td>
						<select name="display_mode" onchange="iwm_isolinkcheck();">
						<option value="regions"
						<?php
						if ( $display_mode === 'regions' ) {
							?>
								selected="selected" <?php } ?> ><?php esc_html_e( 'Regions', 'interactive-world-maps' ); ?></option>
						<option value="markers"
						<?php
						if ( $display_mode === 'markers' ) {
							?>
								selected="selected" <?php } ?> ><?php esc_html_e( 'Round Markers (text - Needs API Key)', 'interactive-world-maps' ); ?></option>
						<option value="markers02"
						<?php
						if ( $display_mode === 'markers02' ) {
							?>
								selected="selected" <?php } ?>><?php esc_html_e( 'Round Markers (coordinates)', 'interactive-world-maps' ); ?></option>
						<option value="text"
						<?php
						if ( $display_mode === 'text' ) {
							?>
								selected="selected" <?php } ?> ><?php esc_html_e( 'Text Labels (text - Needs API Key)', 'interactive-world-maps' ); ?></option>
						<option value="text02"
						<?php
						if ( $display_mode === 'text02' ) {
							?>
								selected="selected" <?php } ?>><?php esc_html_e( 'Text Labels (coordinates)', 'interactive-world-maps' ); ?></option>
						<option value="customicon"
						<?php
						if ( $display_mode === 'customicon' ) {
							?>
								selected="selected" <?php } ?>><?php esc_html_e( 'Custom Icon (coordinates) Beta', 'interactive-world-maps' ); ?></option>

					</select></td>

					<td>
						<?php i_world_map_build_actions_select_options( 'map_action', $map_action, 'iwm_isolinkcheck()' ); ?>

					</td>
					</tr>

					<tr> <td colspan="3"><input name="interactive" type="checkbox"  id="interactive" onchange="iwm_drawVisualization();" value="1"
					<?php
					if ( $interactive === '1' ) {
						?>
						checked <?php } ?> /> <?php esc_html_e( 'Enable Region Hover Effect', 'interactive-world-maps' ); ?>

						<br />

		Tooltip <select name="tooltipt"  id="tooltipt" onchange="iwm_drawVisualization();">
			<option value="1"
			<?php
			if ( $tooltipt === '1' ) {
				?>
				selected="selected" <?php } ?>><?php esc_html_e( 'Display on Hover', 'interactive-world-maps' ); ?></option>
			<option value="2"
			<?php
			if ( $tooltipt === '2' ) {
				?>
				selected="selected" <?php } ?>><?php esc_html_e( 'Display on Click', 'interactive-world-maps' ); ?></option>
			<option value="0"
			<?php
			if ( $tooltipt === '0' ) {
				?>
				selected="selected" <?php } ?>><?php esc_html_e( 'None', 'interactive-world-maps' ); ?></option>

			</select><span class="iwmsmall"><?php esc_html_e( ' ( in Regions Mode "Region Hover Effect" must be enabled for tooltip to work)', 'interactive-world-maps' ); ?></span>

			<?php if ( ! isset( $options['default_usehtml'] ) ) { ?>
			<br />
			<span class="iwmsmall"><?php esc_html_e( 'If you plan to use HTML code in your tooltips, you should enable the HTML Tooltips in the settings', 'interactive-world-maps' ); ?></span>

				<?php

			}

			?>

		</td> </tr>

				</table>



				<span id="iso-code-msg"></span>

				<?php

					$key = isset( $options['api_key'] ) ? $options['api_key'] : '';

				if ( $key === '' ) {

					?>

					<div id="iwm-api-key-msg"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>

					<?php esc_html_e( 'Some maps might need a <a href="http://cmoreira.net/interactive-world-maps-demo/advanced-tips/generate-api-key/" targer="_blank">Google Geocoding API Key</a>.', 'interactive-world-maps' ); ?>
					<?php esc_html_e( 'If your map is not displaying, try adding an API Key in the settings page.', 'interactive-world-maps' ); ?>

					</div>

					<?php
				}
				?>
				<div class="stuffbox" id="custom-action">
					<table>
						<tr><td><strong><?php esc_html_e( 'Insert Custom Javascript Action Here', 'interactive-world-maps' ); ?></strong><br />
							<textarea name="custom_action" cols="50" rows="4"><?php echo stripcslashes( $custom_action ); ?></textarea>
							</td>
							<td>
							<span class="iwmsmall"><?php esc_html_e( 'You can use Javascript with the variable "value" (action value content) and "selectedRegion" (region code)', 'interactive-world-maps' ); ?>
							</span>
							<br>
							<br>
							<?php esc_html_e( 'Javascript code can be dangerous. You are responsible for the code you include here. Avoid copying code from unreliable sources.', 'interactive-world-maps' ); ?>
							</td>
						</tr>
					</table>
				</div>
				<div id="latlondiv">
				<table width="100%" cellspacing="5" cellpadding="5" class="latlon">
					<tr>
					<td>
						<?php
						if ( $apikey ) {
							esc_html_e( 'Use the form below to help you get the coordinates values', 'interactive-world-maps' );
						} else {
							echo "<strong class='error'>
							" . esc_html_e( 'Add a valid Google Geocoding API Key in the settings page to use the converter below', 'interactive-world-maps' ) . '
							</strong>';
						}
						?>


						<br /><i class="fa fa-globe"></i> <?php esc_html_e( 'Convert Address into Lat/Lon:', 'interactive-world-maps' ); ?>
						<label for="mapsearch">
						<input type="text" name="mapsearch" id="mapsearch">
						<input type="button" class="button-secondary" name="convert" id="convert" value="Convert" onClick="iwm_getAddress()">
						</label> <span id="latlonvalues"></span>

						<?php

						if ( ! $apikey ) {
							echo '<br><strong>';
							esc_html_e( 'You can still get the coordinates using external websites like <a href="https://www.latlong.net/convert-address-to-lat-long.html" target="_blank">GetLatLong</a>', 'interactive-world-maps' );
							echo '</strong>';
						}
						?>
					</td>
					</tr>
				</table>
				</div>
				<h3><?php esc_html_e( 'Interactive Regions', 'interactive-world-maps' ); ?></h3>
				<br />
				<a class="activeb" id="shsimple" onclick="iwm_showsimple()" ><?php echo __( 'Simple', 'interactive-world-maps' ); ?></a>
				<a class="inactiveb" id="shadvanced" onclick="iwm_showadvanced()" ><?php echo __( 'Advanced', 'interactive-world-maps' ); ?></a>
				<a class="inactiveb" id="shpopulate" onclick="iwm_showpopulate()" ><?php echo __( 'Populate Automatically', 'interactive-world-maps' ); ?></a>
					<div id="populate-automatically-div">
					<input type="hidden" id="data-directory" value="<?php echo plugins_url( '/data', __FILE__ ); ?>">
						<?php echo __( 'If you want to populate the map faster, you can use these actions. Be aware that your current content will be replaced.<br> You can populate your map and then edit the entries to customize it further.', 'interactive-world-maps' ); ?>
						<div id="us-labels" class="automatebutton">
							<?php echo __( 'US State Labels - 2 letter state codes', 'interactive-world-maps' ); ?>
						</div>
						<div id="us-states" class="automatebutton">
							<?php echo __( 'US States - In different shades of blue with links to Wikipedia', 'interactive-world-maps' ); ?>
						</div>
						<div id="world-countries" class="automatebutton">
							<?php echo __( 'World Countries - Will populate the world map with all country codes in different shades of blue.', 'interactive-world-maps' ); ?>
						</div>
						<div id="categories_count" class="automatebutton">
							<?php echo __( 'Categories - Use Post Categories as country codes and link to the the archive pages', 'interactive-world-maps' ); ?>
						</div>
					</div>
					<div id="simple-table">
						<table width="100%" class="stuffbox" id="add-table">
						<tr valign="top">
							<td><table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td style="width:10%;"><?php esc_html_e( 'Region Code', 'interactive-world-maps' ); ?>: </td>
								<td style="width:20%;"><?php esc_html_e( 'Title', 'interactive-world-maps' ); ?>: </td>
								<td style="width:20%;"><?php esc_html_e( 'Tooltip', 'interactive-world-maps' ); ?>: </td>
								<td style="width:25%;"><?php esc_html_e( 'Action Value', 'interactive-world-maps' ); ?>: </td>
								<td style="width:10%;"><?php esc_html_e( 'Color', 'interactive-world-maps' ); ?>: </td>
								<td style="width:5%;" valign="baseline"></p></td>
							</tr>
						<tr>
						<td valign="top">
							<input name="cd" type="text" id="cd" /><br />
							<span class="iwmsmall"><?php esc_html_e( 'Follow the suggestions above', 'interactive-world-maps' ); ?><br />
								<?php esc_html_e( 'Mandatory', 'interactive-world-maps' ); ?>
							</span>
						</td>
						<td valign="top">
							<input name="c" type="text" id="c" /><br />
							<span class="iwmsmall"><?php esc_html_e( 'First line of tooltip', 'interactive-world-maps' ); ?></span>
						</td>
						<td valign="top">
							<input name="t" type="text" id="t" /> <br />
							<span class="iwmsmall"><?php esc_html_e( 'Second line of tooltip', 'interactive-world-maps' ); ?></span></td>
							<td valign="top"><input name="u" type="text" id="u" size="20" />
							<br />
							<span class="iwmsmall"><?php esc_html_e( 'Paramater for the action', 'interactive-world-maps' ); ?></span><br />
							<span class="iwmsmall" id="actionvaluetip"><?php esc_html_e( 'Ex. Url for Open Url Action.', 'interactive-world-maps' ); ?></span></td>
							<td valign="top"><input name="cl" type="text" id="cl" class="color {hash:true, adjust:false}" value="<?php echo $act_color; ?>"  /></td>
							<td valign="top"><input type="button" class="button-secondary" title="<?php esc_html_e( 'Add New Entry', 'interactive-world-maps' ); ?>" value="<?php esc_html_e( 'Add', 'interactive-world-maps' ); ?>" onclick="iwm_addPlaceToTable();" /></td>
						</tr>
						<tr><td colspan="6" style="font-size:0.9em; height:10px; text-align:right;"><?php esc_html_e( 'Render HTML in Data Table?', 'interactive-world-maps' ); ?><input name="rendertags" id="rendertags" type="checkbox" value="1" onchange="iwm_dataToTable()" /></td></tr>
						</table>
						<div id="htmlplacetable"></div>
						</td>
					</tr>
					<input name="action" type="hidden" value="<?php echo $submit_action; ?>" />
					<?php if ( $type === 'edit-map' ) { ?>
					<input name="id" type="hidden" value="<?php echo $id; ?>" />
						<?php } ?>
				</table>
				</div>
				<div id="advanced-table">
				<table width="100%" cellspacing="5" cellpadding="5" id="add-table-advanced">
					<tr>
					<td><strong><?php esc_html_e( 'Advanced Data Editor', 'interactive-world-maps' ); ?></strong><br />
					<span class="iwmsmall">
						<?php esc_html_e( 'Here you can add or edit the CSV ( comma-separated values) data that will be parsed to build the map. ', 'interactive-world-maps' ); ?>
						<?php esc_html_e( 'It should follow this format:', 'interactive-world-maps' ); ?>
						<?php esc_html_e( 'Region Code, Tooltip Title, Tooltip Text, Action Value, HTML Color Value;', 'interactive-world-maps' ); ?>
						<?php esc_html_e( 'It should not use quotes. Example:', 'interactive-world-maps' ); ?>
						<?php esc_html_e( 'US, USA, Click to visit the White House Website, http://www.whitehouse.gov/,#6699CC;', 'interactive-world-maps' ); ?>
						<?php esc_html_e( 'PT, Portugal, Click to visit Portugal\'s Government Website, http://www.portugal.gov.pt/,#660000;', 'interactive-world-maps' ); ?>
					</span></td>
					</tr>
					<tr>
					<td><textarea name="places" id="places" onchange="iwm_dataToTable();"><?php echo htmlspecialchars( $places ); ?></textarea><br />
						<input type="button" class="button-secondary" value="Preview" onclick="iwm_dataToTable();" /></td>
					</tr>
				</table>
				</div>

			<h3><?php esc_html_e( 'Preview', 'interactive-world-maps' ); ?></h3>
			<span class="iwmsmall"> <i class="fa fa-file-code-o"></i>
				<?php esc_html_e( 'The "Active Region Action" will not work on this preview.', 'interactive-world-maps' ); ?>
				<?php esc_html_e( 'When an active region is clicked an alert message with the value inserted will display for debugging, or no alert, if no value exists.', 'interactive-world-maps' ); ?>
				</span>
				<?php
				if ( isset( $options['default_usehtml'] ) && $options['default_usehtml'] === '1' ) {
					?>
				<br>
				<span class="iwmsmall"><i class="fa fa-comment"></i>
					<?php esc_html_e( 'The HTML tooltip might look different on your site since it can inherit CSS rules from your theme. <br> You can create your own CSS rules to target the tooltip using the class ".google-visualization-tooltip"', 'interactive-world-maps' ); ?>
				</span>
					<?php } ?>
				</div>
				<div id="iwm-wrap-preview" >
					<div id="visualization-wrap-responsive" >
						<div id="visualization"></div>
					</div>
				</div>
				<div>
				<?php
				// Code to break down custom css json
				$cssarray = json_decode( stripslashes( $customcss ), true );
				?>
				<h3><?php esc_html_e( 'Custom CSS Generator', 'interactive-world-maps' ); ?></h3>
				<span class="iwmsmall">
					<strong>
					<?php esc_html_e( 'The options below are not supported by the Google Geochart API (which the plugin uses to generate the maps), so using these CSS techniques is an alternative unsuported solution that might not work as expected and has limitations. Use at your own risk.', 'interactive-world-maps' ); ?>
					</strong>
					<?php esc_html_e( ' These customizations will not reflect on the image preview of the map.', 'interactive-world-maps' ); ?>
				</span>
				<div id="iwmexpandcss">
					<a onclick="expandcustomcss()">
						<i class="fa fa-chevron-circle-right fa-lg"></i><?php esc_html_e( 'Expand Custom CSS Options Box', 'interactive-world-maps' ); ?>
					</a>
				</div>
				<div class="stuffbox" id="iwm-custom-css">
					<div>
					<h4><i class="fa fa-square"></i><?php esc_html_e( 'Change Crop / Zoom Effect', 'interactive-world-maps' ); ?></h4>

					<div id="iwm-control-box" class="stuffbox">
						<?php esc_html_e( 'Zoom', 'interactive-world-maps' ); ?><a onclick="iwm_csscontrol( 'widthplus' )"><i class="fa fa-search-plus fa-2x"></i></a>
						<a onclick="iwm_csscontrol( 'widthminus' )"><i class="fa fa-search-minus fa-2x"></i></a>
						<?php esc_html_e( 'Move', 'interactive-world-maps' ); ?><a onclick="iwm_csscontrol( 'down' )"><i class="fa fa-arrow-circle-down fa-2x"></i></a>
						<a onclick="iwm_csscontrol( 'up' )"><i class="fa fa-arrow-circle-up fa-2x"></i></a>
						<a onclick="iwm_csscontrol( 'left' )"><i class="fa fa-arrow-circle-left fa-2x"></i></a>
						<a onclick="iwm_csscontrol( 'right' )"><i class="fa fa-arrow-circle-right fa-2x"></i></a>
						<?php esc_html_e( 'Height', 'interactive-world-maps' ); ?><a onclick="iwm_csscontrol( 'verticalplus' )"><i class="fa fa-long-arrow-down fa-2x"></i></a>
						<a onclick="iwm_csscontrol( 'verticalminus' )"><i class="fa fa-long-arrow-up fa-2x"></i></a>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php esc_html_e( 'Reset Values', 'interactive-world-maps' ); ?> <a onClick="clearCropValues()"><i class="fa fa-times-circle fa-2x"></i></a>
					</div>
					<span class="iwmsmall">
						<strong>
						<?php esc_html_e( 'The controls above will influence these values', 'interactive-world-maps' ); ?>
						</strong>.
						<?php esc_html_e( 'Changing the following values will allow you change the crop of the map or create a zoom effect by hidding uncessary parts of the map with the overflow:hidden; rule. One of the biggest limitations of this hack is that it will also hide tooltips, if they display on the overflow area.', 'interactive-world-maps' ); ?>
						</span>
					<br> <br>
					<table>
						<tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Width/Height overflow:', 'interactive-world-maps' ); ?> </td>
							<td><input type="number" name="iwm_size" size="10" value="<?php
							if ( isset( $cssarray['iwm_size'] ) ) {
								echo $cssarray['iwm_size']; }
							?>" onchange="iwm_redrawcrop()"
									<?php
									if ( ! isset( $options['default_responsive'] ) ) {
										?>
			disabled <?php } ?>  > % </td>
							<td><span class="iwmsmall">
							<?php
							if ( ! isset( $options['default_responsive'] ) ) {
								?>
									<?php esc_html_e( 'DISABLED. Only works when responsive mode is enabled.', 'interactive-world-maps' ); ?>

									<?php
							} else {
								?>
								<?php esc_html_e( '100% is the default. Exciding size will be hidden, so bigger values will allow you do concentrate on different parts of the map.', 'interactive-world-maps' ); ?>
									<?php } ?></span></td>
						</tr><tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Viewport Height:', 'interactive-world-maps' ); ?></td>
							<td><input type="number" step="any" name="iwm_hsize" size="10" value="<?php echo $cssarray['iwm_hsize']; ?>" onchange="iwm_redrawcrop()"> %</td>
							<td><span class="iwmsmall"><?php esc_html_e( 'Default is 61.7 (~ 5:3 aspect ratio). This field will manipulate the aspect ratio of the map viewport.', 'interactive-world-maps' ); ?></span></td>
						</tr><tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Left Margin', 'interactive-world-maps' ); ?></td>
							<td><input type="number" name="iwm_left" size="10" value="<?php echo $cssarray['iwm_left']; ?>" onchange="iwm_redrawcrop()"> % </td>
							<td><span class="iwmsmall"><?php esc_html_e( 'These values will move the map horizontaly. Use negative values to move the map left and positive to move the map to the right.', 'interactive-world-maps' ); ?></span></td>
						</tr><tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Top Margin', 'interactive-world-maps' ); ?> </td>
							<td><input type="number" name="iwm_top" size="10" value="<?php echo $cssarray['iwm_top']; ?>" onchange="iwm_redrawcrop()"> % </td>
							<td><span class="iwmsmall"><?php esc_html_e( 'These values will move the map verticaly. Use negative values to move the map up and positive to move the map down.', 'interactive-world-maps' ); ?></span></td>
						</tr>
					</table>

					</div>
					<div>
						<h4><i class="fa fa-square"></i><?php esc_html_e( 'Hover Options for Active Elements', 'interactive-world-maps' ); ?></h4>
						<span class="iwmsmall"><?php esc_html_e( ' These options will create css that target the map shapes that do not have the inactive regions colour. The biggest limitation of this hovering hack, is that it will only apply the hover effect to map shapes, it is not capable of recognizing the full region shapes. For example, when hovering a group of islands, only the hovered island will change colour.', 'interactive-world-maps' ); ?></span>
						<br> <br>
						<table>
						<tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Hover Colour:', 'interactive-world-maps' ); ?></td>
							<td><input name="hovercolor" type="text" id="hovercolor" size="15" class="color {hash:true, adjust:false}" value="<?php echo $cssarray['hovercolor']; ?>"  onchange="iwm_redrawcrop()" /></td>
							<td><span class="iwmsmall"><?php esc_html_e( 'The active hovered map shapes will change to this colour.', 'interactive-world-maps' ); ?></span></td>
						</tr><tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Display Hand Cursor:', 'interactive-world-maps' ); ?></td>
							<td><input name="showcursor" id="showcursor" type="checkbox" value="1" onchange="iwm_redrawcrop()"
							<?php
							if ( $cssarray['showcursor'] === '1' ) {
								echo 'checked'; }
							?>
									/></td>
							<td><span class="iwmsmall">
								<?php esc_html_e( 'Active elements, like active regions, markers or text labels, will display the hand cursor.', 'interactive-world-maps' ); ?>
							</span></td>
						</tr>
						</table>
						<h4><i class="fa fa-square"></i><?php esc_html_e( 'Region Border Options', 'interactive-world-maps' ); ?></h4>
						<span class="iwmsmall"><?php esc_html_e( 'These options will target the SVG path shapes and change their fill and stroke values.', 'interactive-world-maps' ); ?></span>
						<br> <br>
						<table>
							<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Borders Colour', 'interactive-world-maps' ); ?>: </td>
								<td><input name="bcolor" type="text" id="bcolor" size="15" class="color {hash:true, adjust:false}" value="<?php echo $cssarray['bcolor']; ?>"  onchange="iwm_redrawcrop()" /></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'Country or region borders colour.', 'interactive-world-maps' ); ?></span></td>
								</tr><tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Stroke Width ( all)', 'interactive-world-maps' ); ?>: </td>
								<td><input type="number" step="1" name="bwidth" size="10" value="<?php echo $cssarray['bwidth']; ?>" onchange="iwm_redrawcrop()"></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'Default is 1 for normal stage and 2 when hovering. Changing this will affect both stages of the shape.', 'interactive-world-maps' ); ?></span></td>
								</tr>

								<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Stroke Width ( inactive Only)', 'interactive-world-maps' ); ?>: </td>
								<td><input type="number" step="1" name="biwidth" size="10" value="<?php echo $cssarray['biwidth']; ?>" onchange="iwm_redrawcrop()"></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'With this option we target only the inactive regions borders.', 'interactive-world-maps' ); ?></span></td>
							</tr>

						</table>

						<h4><i class="fa fa-square"></i><?php esc_html_e( 'Background Options', 'interactive-world-maps' ); ?> </h4>
						<span class="iwmsmall"><?php esc_html_e( 'You can also use an image as a background to your map. This will make the background colour transparent and add the image as the background of the map\'s container.', 'interactive-world-maps' ); ?></span>
						<br> <br>
						<table style="padding-bottom:20px;">
							<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Background Image', 'interactive-world-maps' ); ?>: </td>
								<td><input type="text" name="bgimage" id="bgimage" value="<?php echo $cssarray['bgimage']; ?>" size="10" onchange="iwm_redrawcrop()"></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'Please include full URL to the image you want to use.', 'interactive-world-maps' ); ?></span></td>
								</tr><tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Background Repeat', 'interactive-world-maps' ); ?>: </td>
								<td><input name="bgrepeat" id="bgrepeat" type="checkbox" value="1" onchange="iwm_redrawcrop()"
								<?php
								if ( $cssarray['bgrepeat'] === '1' ) {
									echo 'checked'; }
								?>
									/></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'If active, image will repeat. If disabled image will strech to 100% so it\'s also responsive.', 'interactive-world-maps' ); ?></span></td>
							</tr>
						</table>

						<h4><i class="fa fa-square"></i><?php esc_html_e( 'Marker Options', 'interactive-world-maps' ); ?> </h4>
						<span class="iwmsmall"><?php esc_html_e( 'Options to control the round markers', 'interactive-world-maps' ); ?></span>
						<br> <br>
						<table style="padding-bottom:20px;">
							<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Mobile Marker Size', 'interactive-world-maps' ); ?>: </td>
								<td><input type="number" name="mobilemarker" id="mobilemarker" value="<?php echo isset( $cssarray['mobilemarker'] ) ? $cssarray['mobilemarker'] : ''; ?>" size="10" onchange="iwm_redrawcrop()"></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'You can setup a different size for the marker on smaller screens', 'interactive-world-maps' ); ?></span></td>
								</tr>
						</table>

						<h4><i class="fa fa-square"></i><?php esc_html_e( 'Tooltip Options', 'interactive-world-maps' ); ?></h4>

						<?php if ( ! isset( $options['default_usehtml'] ) ) { ?>

						<i class="fa fa-exclamation-triangle" style="color:red;"></i> <span class="iwmsmall"><?php esc_html_e( 'These settings will not take effect since HTML tooltips are disabled in the settings.', 'interactive-world-maps' ); ?></span>

						<?php } else { ?>

						<span class="iwmsmall"><?php esc_html_e( 'You can create more rules creating custom css for the class .google-visualization-tooltip', 'interactive-world-maps' ); ?></span>

						<?php } ?>
										<br> <br>
						<table style="padding-bottom:20px;">
							<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Font-Family', 'interactive-world-maps' ); ?>: </td>
								<td><input type="text" name="tooltipfontfamily" id="tooltipfontfamily" value="<?php echo $cssarray['tooltipfontfamily']; ?>" size="10" onchange="iwm_redrawcrop()"></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'Specify the font for the tooltip', 'interactive-world-maps' ); ?></span></td>
							</tr><tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Font-size', 'interactive-world-maps' ); ?>: </td>
								<td><input name="tooltipfontsize" id="tooltipfontsize" type="text" value="<?php echo $cssarray['tooltipfontsize']; ?>"  onchange="iwm_redrawcrop()" /></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'You should use the unit value also, like 12px or 1em.', 'interactive-world-maps' ); ?></span></td>
							</tr><tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Background Colour', 'interactive-world-maps' ); ?>: </td>
								<td><input name="tooltipbg" id="tooltipbg" type="text" onchange="iwm_redrawcrop()" value="<?php echo $cssarray['tooltipbg']; ?>" class="color {hash:true, adjust:false}" /></td>
								<td><span class="iwmsmall"></span></td>
							</tr>
							<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Width', 'interactive-world-maps' ); ?>: </td>
								<td><input name="tooltipminwidth" id="tooltipminwidth" value="<?php echo $cssarray['tooltipminwidth']; ?>" type="text"  onchange="iwm_redrawcrop()" /></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'Set a minimum width for the tooltip. You should also use the unit value also, like 12px or 1em.', 'interactive-world-maps' ); ?></span></td>
							</tr><tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Border Colour', 'interactive-world-maps' ); ?>: </td>
								<td><input name="tooltipbordercolor" id="tooltipbordercolor" type="text" onchange="iwm_redrawcrop()" value="<?php echo $cssarray['tooltipbordercolor']; ?>" class="color {hash:true, adjust:false}" /></td>
								<td><span class="iwmsmall"></span></td>
							</tr>
							<tr>
								<td class="iwm_stronger"><?php esc_html_e( 'Border Width', 'interactive-world-maps' ); ?>: </td>
								<td><input name="tooltipborderwidth" id="tooltipborderwidth" value="<?php echo $cssarray['tooltipborderwidth']; ?>" type="text"  onchange="iwm_redrawcrop()" /></td>
								<td><span class="iwmsmall"><?php esc_html_e( 'Set a minimum width for the tooltip. You should also use the unit value also, like 12px or 1em.', 'interactive-world-maps' ); ?></span></td>
							</tr><tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Hide Title', 'interactive-world-maps' ); ?>:</td>
							<td><input name="tooltiphidetitle" id="tooltiphidetitle" type="checkbox" value="1" onchange="iwm_redrawcrop()"
							<?php
							if ( $cssarray['tooltiphidetitle'] === '1' ) {
								echo 'checked'; }
							?>
									/></td>
							<td><span class="iwmsmall"><?php esc_html_e( 'When active, first line of the tooltip (the title field) will not display.', 'interactive-world-maps' ); ?></span></td>
							</tr>
							<tr>
							<td class="iwm_stronger"><?php esc_html_e( 'No Wrap', 'interactive-world-maps' ); ?>:</td>
							<td><input name="tooltipnowrap" id="tooltipnowrap" type="checkbox" value="1" onchange="iwm_redrawcrop()"
							<?php
							if ( isset( $cssarray['tooltipnowrap'] ) && $cssarray['tooltipnowrap'] === '1' ) {
								echo 'checked'; }
							?>
									/></td>
							<td><span class="iwmsmall"><?php esc_html_e( 'When active, a nowrap css rule will be added, which will stop long sentences from having line breaks.', 'interactive-world-maps' ); ?></span></td>
							</tr>
						</table>

						<h4><i class="fa fa-square"></i><?php esc_html_e( 'FontIcon Usage', 'interactive-world-maps' ); ?></h4>
						<span class="iwmsmall"><?php esc_html_e( 'The Geochart API doesn\'t allow us to use custom markers. However we can use the text labels mode and use fonticons. The plugin includes \'FontAwesome\' and <a target="_blank" href="http://cmoreira.net/interactive-world-maps-demo/use-font-icon-as-marker/">you can read more about this technique here.', 'interactive-world-maps' ); ?></a></span>
						<br> <br>
						<table style="padding-bottom:20px;">
							<tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Include FontAwesome', 'interactive-world-maps' ); ?>:</td>
							<td><input name="fontawesomeinclude" id="fontawesomeinclude" type="checkbox" value="1" onchange="iwm_redrawcrop()"
							<?php
							if ( isset( $cssarray['fontawesomeinclude'] ) && $cssarray['fontawesomeinclude'] === '1' ) {
								echo 'checked'; }
							?>
									/></td>
							<td><span class="iwmsmall"><?php esc_html_e( 'If active fontAwesome file will be included with the map. If your page already has fontAwesome included, you don\'t need to include it here.', 'interactive-world-maps' ); ?></span></td>
							</tr>
							<tr>
							<td class="iwm_stronger"><?php esc_html_e( 'Apply FontAwesome', 'interactive-world-maps' ); ?>:</td>
							<td><input name="fontawesomeapply" id="fontawesomeapply" type="checkbox" value="1" onchange="iwm_redrawcrop()"
							<?php
							if ( isset( $cssarray['fontawesomeapply'] ) && $cssarray['fontawesomeapply'] === '1' ) {
								echo 'checked'; }
							?>
									/></td>
							<td><span class="iwmsmall"><?php esc_html_e( 'When active Text Labels can use FontAwesome icons', 'interactive-world-maps' ); ?> (<a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_blank"><?php esc_html_e( 'Apply FontAwesome', 'interactive-world-maps' ); ?></a>)</span></td>
							</tr>
						</table>

						<?php
						$usehtml = '0';
						if ( isset( $options['default_usehtml'] ) && $options['default_usehtml'] === '1' ) {
							$usehtml = '1';
						}
						?>

						<input type="hidden" name="usehtml" id="usehtml" value="<?php echo esc_html( $usehtml ); ?>">
						<input type="button" class="button-secondary" name="iwm-custom-clear" id="iwm-custom-clear" value="Clear Values" onClick="iwm_clearCssValues()">
						<input type="submit" class="button-primary" value="<?php echo $submit_bt_value; ?>" />
					</div>
				</div>

				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			</table>

			<?php

			if ( isset( $options['image_preview_enabled'] ) && $options['image_preview_enabled'] === '1' ) {
				?>
			<input type="hidden" name="mapimage" id="mapimage" value="">
				<?php
			}
			?>
			<input type="hidden" name="customcss" id="customcss" value='<?php echo $customcss; ?>'>

		</form>
	</div>
	<?php
}

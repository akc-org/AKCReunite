<?php

/**
 * Register Settings
 *
 * @return void
 */
function i_world_map_register_settings() {
	// register our settings
	register_setting( 'i-world-map-plugin-settings', 'i-world-map-settings' );
}

/**
 * Register default values
 *
 * @return void
 */
function i_world_map_defaults() {
	$tmp = get_option( 'i-world-map-settings' );
	if ( ( isset( $tmp['empty'] ) && $tmp['empty'] === '1' ) || ( ! is_array( $tmp ) ) ) {
		delete_option( 'i-world-map-settings' );
		$arr = array(
			'default_bg_color'      => '#FFFFFF',
			'default_border_color'  => '#CCCCCC',
			'default_border_stroke' => '0',
			'default_ina_color'     => '#F5F5F5',
			'default_act_color'     => '#438094',
			'default_marker_size'   => '10',
			'default_width'         => '600',
			'default_height'        => '400',
			'default_aspect_ratio'  => '1',
			'default_interactive'   => '1',
			'default_showtooltip'   => '1',
			'default_editor'        => '1',
			'default_display_mode'  => 'regions',
			'default_region'        => 'world, countries',
			'map_projection'        => 'mercator',
			'imageicon'             => plugin_dir_url( __FILE__ ) . 'imgs/location-32.png',
			'imageicon_position'    => 'center',
			'default_responsive'    => '1',
			'empty'                 => '0',
			'scriptadd'             => '1',
			'image_preview_enabled' => '1',
			'html_ios'              => '1',

		);
		update_option( 'i-world-map-settings', $arr );
	}
}


/**
 * Output settings page form
 *
 * @return void
 */
function i_world_map_settings_page() {

	$capability = i_world_map_user_cap();

	if ( ! is_admin() || ! current_user_can( $capability ) ) {
		return;
	}

	?>

<form method="post" action="options.php" id="dsform">

<div class="iwm-wrap">
<div id="interactive-world-maps" class="icon32"></div>
<h2>Settings</h2>
	<?php
	if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) {
		$msg  = 'Settings Updated';
		$type = 'updated';
		i_world_map_message( $msg );
	}

	?>

	<p>
		Edit the default settings for the maps. <br />
		When creating a map, you can choose to use the default visual settings or create custom ones.<br />
	</p>

	<table width="100%" border="0" cellspacing="10"  cellpadding="10">
	<tr>
		<td width="25%" style="vertical-align:top;">
			<?php settings_fields( 'i-world-map-plugin-settings' ); ?>
			<?php $options = get_option( 'i-world-map-settings' ); ?>
			<h3>
			Default Visual Settings
			</h3>
			<table width="100%" cellpadding="2" cellspacing="2" class="stuffbox" id="default-settings-table">
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row"><strong>Background Color</strong></td>
			<td width="20%"><input type="text" name="i-world-map-settings[default_bg_color]" class="color {hash:true, adjust:false}" value="<?php echo $options['default_bg_color']; ?>" onchange="iwm_drawVisualization();" /></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row"><strong>Border Color</strong></td>
			<td width="20%"><input type="text" name="i-world-map-settings[default_border_color]" class="color {hash:true, adjust:false}" value="<?php echo $options['default_border_color']; ?>" onchange="iwm_drawVisualization();" /></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap"><strong>Border Width (px)</strong></td>
			<td width="20%"><input name="i-world-map-settings[default_border_stroke]" value="<?php echo $options['default_border_stroke']; ?>" size="5" onchange="iwm_drawVisualization();" type="number" min="0" max="100" /></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row">&nbsp;</td>
			<td width="20%">&nbsp;</td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row"><strong>Inactive Region Color</strong></td>
			<td width="20%"><input type="text" name="i-world-map-settings[default_ina_color]" class="color {hash:true, adjust:false}" value="<?php echo $options['default_ina_color']; ?>" onchange="iwm_drawVisualization();" /></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row"><strong>Active Region Color</strong></td>
			<td width="20%"><input type="text" name="i-world-map-settings[default_act_color]" class="color {hash:true, adjust:false}" value="<?php echo $options['default_act_color']; ?>" onchange="iwm_drawVisualization();" /></td>
			</tr>
			<tr valign="top">
			<td nowrap="nowrap" scope="row">&nbsp;</td>
			<td>&nbsp;</td>
			</tr>
			<tr valign="top">
			<td nowrap="nowrap" scope="row"><strong>Marker Size (px)</strong></td>
			<td><input name="i-world-map-settings[default_marker_size]" value="<?php echo $options['default_marker_size']; ?>" onchange="iwm_drawVisualization();" type="number" min="1" max="100" /></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row">&nbsp;</td>
			<td width="20%">&nbsp;</td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row"><strong>Width (px)</strong></td>
			<td width="20%"><input name="i-world-map-settings[default_width]" type="text" value="<?php echo $options['default_width']; ?>" size="5" onchange="iwm_drawVisualization();" type="number"/></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap"><strong>Height (px)</strong></td>
			<td width="20%"><input name="i-world-map-settings[default_height]" type="text" value="<?php echo $options['default_height']; ?>" size="5" onchange="iwm_drawVisualization();" type="number"/></td>
			</tr>
			<tr valign="top">
			<td width="10%" nowrap="nowrap" scope="row"><strong>Keep Aspect Ratio</strong></td>
			<td width="20%"><input name="i-world-map-settings[default_aspect_ratio]" id="aspratio" type="checkbox" value="1"
			<?php
			if ( isset( $options['default_aspect_ratio'] ) && $options['default_aspect_ratio'] === '1' ) {
				?>
					checked <?php } ?> onchange="iwm_drawVisualization();" />

			</td>

			<tr>
				<td colspan="2" class="iwmsmall"><i class="fa fa-info-circle"></i>  Tip: In color fields you can use the word 'transparent' </td>
			</tr>

			</tr>
			</table>

			<h3>Default Map Settings </h3>
			<p>Values will be pre-selected when creating a new map.</p>
		<table width="100%" id="default-settings-table" class="stuffbox">
			<tr valign="top">
			<td nowrap="nowrap" scope="row"><strong>Region to Show</strong><br />

				<?php i_world_map_build_region_select_options( 'i-world-map-settings[default_region]', $options['default_region'], 'drawVisualization()' ); ?>              </td>
			</tr>
			<tr valign="top">
			<td scope="row">&nbsp;</td>
			</tr>
			<tr valign="top">
			<td scope="row"><strong>Display Mode</strong><br />
				<select name="i-world-map-settings[default_display_mode]" onchange="iwm_drawVisualization();">
				<option value="regions"
				<?php
				if ( $options['default_display_mode'] === 'regions' ) {
					?>
						selected="selected" <?php } ?>>Regions</option>
				<option value="markers"
				<?php
				if ( $options['default_display_mode'] === 'markers' ) {
					?>
						selected="selected" <?php } ?> >Markers</option>
				<!-- <option value="text"
				<?php
				if ( $options['default_display_mode'] === 'text' ) {
					?>
						selected="selected" <?php } ?> >Text Label</option> -->

			</select></td>
			</tr>
			<tr valign="top">
			<td scope="row">&nbsp;</td>
			</tr>
			<tr valign="top">
			<td scope="row"><p><strong>Interactivity<br />
				</strong>
				<input name="i-world-map-settings[default_interactive]" id="interactive" type="checkbox" value="1"
				<?php
				if ( isset( $options['default_interactive'] ) && '1' === $options['default_interactive'] ) {
					?>
					checked <?php } ?> onchange="iwm_drawVisualization();" />Enable<br />
				<input name="i-world-map-settings[default_showtooltip]" id="showtooltip" type="checkbox" value="1"
				<?php
				if ( isset( $options['default_showtooltip'] ) && '1' === $options['default_showtooltip'] ) {
					?>
					checked <?php } ?> onchange="iwm_drawVisualization();" />Show Tooltip
			</p>
			</td>
			</tr>

		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
		</p>
		</td>
		<td width="75%" valign="top"><!-- <h3>Default Settings Preview</h3>          <div id="visualization"></div> -->

		<h3>General Settings</h3>
		<table id="default-settings-table" class="stuffbox" width="100%">

			<tr><td>

				<p><strong>Google Maps API Key </strong></p>
			<p>
				<input name="i-world-map-settings[api_key]" type="text" value="<?php
				if ( isset( $options['api_key'] ) ) {
					echo $options['api_key']; }
				?>" />
			<a href="http://cmoreira.net/interactive-world-maps-demo/advanced-tips/generate-api-key/" target="_blank">How to Generate an API Key</a><br>
			<span class="howto">Some maps might need to 'Geocode' the region code used and it will use the Google Geocoding API, which needs and API Key to work.</span>
			</p>
			<p>
				<input name="i-world-map-settings[scriptadd]" id="scriptadd" type="checkbox" value="1"
				<?php
				if ( isset( $options['scriptadd'] ) && $options['scriptadd'] === '1' ) {
					?>
					checked <?php } ?> />
			Load Google Maps API File with Key<br>
			<span class="howto">If you already have a script using Google Maps with a valid API key, you might need to disable this option.</span>
			</p>
		<p><strong>Map Projection</strong></p>
			<p>
				<select name="i-world-map-settings[map_projection]" id="map_projection" onchange="iwm_drawVisualization();">
					<option value="mercator"
					<?php
					if ( isset( $options['map_projection'] ) && $options['map_projection'] === 'mercator' ) {
						echo "selected='selected'";}
					?>
					>Mercator</option>
					<option value="kavrayskiy-vii"
					<?php
					if ( isset( $options['map_projection'] ) && $options['map_projection'] === 'kavrayskiy-vii' ) {
						echo "selected='selected'";}
					?>
					>Kavrayskiy-vii</option>
					<option value="albers"
					<?php
					if ( isset( $options['map_projection'] ) && $options['map_projection'] === 'albers' ) {
						echo "selected='selected'";}
					?>
					>Albers</option>
					<option value="lambert"
					<?php
					if ( isset( $options['map_projection'] ) && $options['map_projection'] === 'lambert' ) {
						echo "selected='selected'";}
					?>
					>Lambert</option>
				</select>
				<span class="howto"> Select the map projection format. Currently supported <a href="http://en.wikipedia.org/wiki/Mercator_projection" target="_blank">Mercator</a>, <a href="http://en.wikipedia.org/wiki/Kavrayskiy_VII_projection" target="_blank">Kavrayskiy_VII</a>, <a href="http://en.wikipedia.org/wiki/Albers_projection" target="_blank">Albers</a> and <a href="http://en.wikipedia.org/wiki/Lambert_conformal_conic_projection" target="_blank">Lambert</a>.</span>
			</p>
		</td></tr>

		<tr><td>
		<p><strong>Responsive Maps (Beta Feature)</strong></p>
			<p>
				<input name="i-world-map-settings[default_responsive]" id="responsive" type="checkbox" value="1"
				<?php
				if ( isset( $options['default_responsive'] ) && $options['default_responsive'] === '1' ) {
					?>
					checked <?php } ?> />
			Redraw Map when viewport size changes<br>
			<span class="howto">When enabled the script will ignore the width/height settings of the map and ocupy 100% of the available space. When the window size changes it will try to redraw the map again to fit the available size.</span>
			</p>
		</td></tr>

		<tr><td>
		<p><strong>HTML Tooltips </strong></p>
			<p>
				<input name="i-world-map-settings[default_usehtml]" id="usehtml" type="checkbox" value="1"
				<?php
				if ( isset( $options['default_usehtml'] ) && $options['default_usehtml'] === '1' ) {
					?>
					checked <?php } ?> />
			Render HTML in the tooltips.<br>
			<span class="howto">Consider that the tooltip will inherit styles from your theme that might affect the way the tooltip displays. You can target the tooltip with CSS using the class <i>.google-visualization-tooltip</i>.</span>
			</p>
		</td></tr>

		<tr><td>
		<p><strong>HTML Tooltips in iOS </strong></p>
			<p>
				<input name="i-world-map-settings[html_ios]" id="html_ios" type="checkbox" value="1"
				<?php
				if ( isset( $options['html_ios'] ) && $options['html_ios'] === '1' ) {
					?>
					checked <?php } ?> />
			Render HTML in the tooltips on iOS devices.<br>
			<span class="howto">Currently there's a bug in the Geochart API affecting iOS devices that prevents the click action to work properly when html tooltips are enabled. You can disable the html tooltips option only for iOS using this option to workaround this limitation. HTML will still work on desktop computers and android devices.</span>
			</p>
		</td></tr>

		<tr><td>
		<p><strong>WYSIWYG Editor </strong></p>
			<p>
				<input name="i-world-map-settings[default_editor]" id="useeditor" type="checkbox" value="1"
				<?php
				if ( isset( $options['default_editor'] ) && $options['default_editor'] === '1' ) {
					?>
					checked <?php } ?> />
			Display simple editor in Administration <br>
			<span class="howto">If active when you edit the map entries, the tooltip field ( if html is enabled) and the action value field ( if the action accepts html content) will display a simple editor instead of the simple textarea</span>
			</p>
		</td></tr>
		<tr><td>
					<p><strong>Custom Image Icon (Beta) </strong></p>
					<p>
					<input name="i-world-map-settings[imageicon]" type="text" value="<?php
					if ( isset( $options['imageicon'] ) ) {
						echo $options['imageicon']; }
					?>" />

					<span class="howto">URL to the image you want to use instead of the round markers. This feature is still in beta. There are limitations.</span>
					</p>
			<p>
				<select name="i-world-map-settings[imageicon_position]" id="imageicon_position">
					<option value="top"
					<?php
					if ( isset( $options['imageicon_position'] ) && $options['imageicon_position'] === 'top' ) {
						echo "selected='selected'";}
					?>
					>Top</option>
					<option value="center"
					<?php
					if ( isset( $options['imageicon_position'] ) && $options['imageicon_position'] === 'center' ) {
						echo "selected='selected'";}
					?>
					>Center</option>
				</select>
				<span class="howto">Position of the image in relation to the center point of the coordinate</span>
			</p>
				</td>
			</tr>
			<tr><td>
		<p><strong>Ajax Loading Fix </strong></p>
			<p>
				<input name="i-world-map-settings[ajax_enabled]" id="ajax_enabled" type="checkbox" value="1"
				<?php
				if ( isset( $options['ajax_enabled'] ) && $options['ajax_enabled'] === '1' ) {
					?>
					checked <?php } ?> />
			Load files across site (not recommended)<br>
			<span class="howto">Some themes load content via ajax and fail to load the necessary files for the map to work. With this option enabled the map files will load by default, so they exist on the page when the map needs to display. This is not recomended, unless there isn't an alternative. If your theme allows you to disable ajax loading for specific URLs that would be a better option.</span>
			</p>


		</td></tr>
		<tr><td>
		<p><strong>Image Preview ( connection Timeout Error Fix)</strong></p>
			<p>
				<input name="i-world-map-settings[image_preview_enabled]" id="image_preview_enabled" type="checkbox" value="1"
				<?php
				if ( isset( $options['image_preview_enabled'] ) && $options['image_preview_enabled'] === '1' ) {
					?>
					checked <?php } ?> />
			Save Image Preview<br>
			<span class="howto">Some users have reported issues when trying to save maps, related with the fact some servers are not able to process the long string of data that the image preview generates. Disable this option if you're getting a connection timeout error when trying to save a new map.</span>
			</p>
		</td></tr>
	</table>
		<h3>Custom Styles & Script</h3>
		<table id="default-settings-table" class="stuffbox" width="100%">
		<tr><td>
		<p><strong>Custom CSS</strong></p>
			<p>
				<textarea name="i-world-map-settings[custom_css]" id="iwm_custom_css"><?php
				if ( isset( $options['custom_css'] ) ) {
					echo $options['custom_css']; }
				?></textarea>
			Include this CSS in pages where maps are displayed.<br>
			<span class="howto">If you want to include custom css together with your maps you can include the css here. <a href="http://cmoreira.net/interactive-world-maps-demo/advanced-customization/" target="_blank">You can see some examples of custom CSS in the official website of the plugin.</a></span>
			</p>
		</td></tr>
		<tr><td>
		<p><strong>Custom Javascript</strong></p>
			<p>
				<textarea name="i-world-map-settings[custom_js]" id="iwm_custom_js"><?php
				if ( isset( $options['custom_js'] ) ) {
					echo $options['custom_js']; }
				?></textarea>
			Include this Javascript in pages where maps are displayed.<br>
			<span class="howto">If you want to include custom javascript together with your maps you can include the code here. </span> <br>Javascript code can be dangerous. You are responsible for the code you include here. Avoid copying code from unreliable sources.
			</p>
		</td></tr>
	</table>
		</td>
	</tr>
	</table>
	</td></tr>
	</table>
	<p>&nbsp;</p>
</div>
</form>
	<?php
}

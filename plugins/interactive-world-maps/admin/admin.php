<?php

/**
 * Get capability needed to perform admin operations
 *
 * @return string capability
 */
function i_world_map_user_cap() {
	return apply_filters( 'i_world_map_capability', 'manage_options' );
}

/**
 * Delete maps based on ID
 *
 * @param [int] $id
 * @return void
 */
function i_world_map_delete( $id, $nonce = '' ) {

	$capability = i_world_map_user_cap();

	if (
		( '' === $nonce )
		|| ! wp_verify_nonce( $nonce, 'delete_map' )
		|| ! is_admin()
		|| ! current_user_can( $capability )
	) {
		return false;
	}

	global $wpdb;
	$table_name_imap = i_world_map_table_name();
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM $table_name_imap WHERE id = %d",
			$id
		)
	);

	return true;
}

/**
 * Duplicate map based on id
 *
 * @param [int] $id
 * @return void
 */
function i_world_map_duplicate( $id, $nonce = '' ) {

	$capability = i_world_map_user_cap();

	if (
		( '' === $nonce )
		|| ! wp_verify_nonce( $nonce, 'duplicate_map' )
		|| ! is_admin()
		|| ! current_user_can( $capability )
	) {
		return false;
	}

	global $wpdb;
	$table_name_imap = i_world_map_table_name();

	$wpdb->query(
		$wpdb->prepare(
			"insert into $table_name_imap (`name`, `description`, `use_defaults`, `bg_color`, `border_color`, `border_stroke`, `ina_color`, `act_color`, `marker_size`, `width`, `height`, `aspect_ratio`, `interactive`, `showtooltip`, `region`, `display_mode`, `map_action`, `places`, `image`, `custom_action`, `custom_css`)
		select `name`, `description`, `use_defaults`, `bg_color`, `border_color`, `border_stroke`, `ina_color`, `act_color`, `marker_size`, `width`, `height`, `aspect_ratio`, `interactive`, `showtooltip`, `region`, `display_mode`, `map_action`, `places`, `image`, `custom_action`, `custom_css`
		from $table_name_imap
		where id = %d",
			$id
		)
	);

	$wpdb->update(
		$table_name_imap,
		array(
			'name' => $wpdb->get_var( $wpdb->prepare( 'SELECT name FROM ' . $table_name_imap . ' WHERE id = %d', $wpdb->insert_id ) ) . ' (copy)',
		),
		array( 'id' => $wpdb->insert_id )
	);

	return true;
}

/**
 * To Show styled messages
 *
 * @param [string] $msg
 * @return void
 */
function i_world_map_message( $msg ) {

	return sprintf( '<div id="message" class="updated"><p>%s</p></div>', $msg );
}

/**
 * To Show styled messages
 *
 * @param [string] $msg
 * @return void
 */
function i_world_map_message_red( $msg ) {

	return sprintf( '<div id="message" class="error"><p>%s</p></div>', $msg );

}

/**
 * Build select option
 *
 * @param [string] $name
 * @param [strig]  $selected
 * @param [string] $onchange
 * @return void
 */
function i_world_map_build_actions_select_options( $name, $selected, $onchange ) {

	$actions = array(
		array(
			'name'  => __( 'None', 'interactive-world-maps' ),
			'value' => 'none',
		),
		array(
			'name'  => __( 'Open URL (same window)', 'interactive-world-maps' ),
			'value' => 'i_map_action_open_url',
		),
		array(
			'name'  => __( 'Open URL (new window)', 'interactive-world-maps' ),
			'value' => 'i_map_action_open_url_new',
		),
		array(
			'name'  => __( 'Alert Message', 'interactive-world-maps' ),
			'value' => 'i_map_action_alert',
		),
		array(
			'name'  => __( 'Display Content Above Map', 'interactive-world-maps' ),
			'value' => 'i_map_action_content_above',
		),
		array(
			'name'  => __( 'Display Content Below Map', 'interactive-world-maps' ),
			'value' => 'i_map_action_content_below',
		),
		array(
			'name'  => __( 'Display Content Below & scroll', 'interactive-world-maps' ),
			'value' => 'i_map_action_content_below_scroll',
		),
		array(
			'name'  => __( 'Display Content to the Right (1/3)', 'interactive-world-maps' ),
			'value' => 'i_map_action_content_right_1_3',
		),
		array(
			'name'  => __( 'Display Content to the Right (1/4)', 'interactive-world-maps' ),
			'value' => 'i_map_action_content_right_1_4',
		),
		array(
			'name'  => __( 'Display Content to the Right (1/2)', 'interactive-world-maps' ),
			'value' => 'i_map_action_content_right_1_2',
		),
		array(
			'name'  => __( 'Display Content in Lightbox', 'interactive-world-maps' ),
			'value' => 'i_map_action_colorbox_content',
		),
		array(
			'name'  => __( 'Display URL in Lightbox (iframe)', 'interactive-world-maps' ),
			'value' => 'i_map_action_colorbox_iframe',
		),
		array(
			'name'  => __( 'Display Image in Lightbox', 'interactive-world-maps' ),
			'value' => 'i_map_action_colorbox_image',
		),
		array(
			'name'  => __( 'Display inline content in Lightbox', 'interactive-world-maps' ),
			'value' => 'i_map_action_colorbox_inline',
		),
		array(
			'name'  => __( 'Custom Action', 'interactive-world-maps' ),
			'value' => 'i_map_action_custom',
		),
	);
	?>
			<select name="<?php echo $name; ?>"
									<?php
									if ( $onchange !== '' ) {
										echo 'onchange="' . $onchange . '"';}
									?>
			>
				<?php
				foreach ( $actions as $action ) {
					?>
				<option value="<?php echo esc_attr( $action['value'] ); ?>"
										<?php
										if ( $selected === $action['value'] ) {
											echo "selected='selected'";}
										?>
				><?php echo esc_html( $action['name'] ); ?></option>
				<?php } ?>
				</select>
				<?php

}

/**
 * Build select region dropdown
 *
 * @param [string] $name
 * @param [string] $selected
 * @param [string] $onchange
 * @return void
 */
function i_world_map_build_region_select_options( $name, $selected, $onchange ) {
				$regions = array(
					array(
						'name'  => 'World',
						'value' => 'world,countries',
					),
					array(
						'name'  => 'World - Continent Regions',
						'value' => 'world,continents',
					),
					array(
						'name'  => 'World - Subcontinents Regions',
						'value' => 'world,subcontinents',
					),
					array(
						'name'  => 'Africa',
						'value' => '002,countries',
					),
					array(
						'name'  => 'Africa - Subcontinents Regions',
						'value' => '002,subcontinents',
					),
					array(
						'name'  => 'Africa - Northern Africa',
						'value' => '015,countries',
					),
					array(
						'name'  => 'Africa - Western Africa',
						'value' => '011,countries',
					),
					array(
						'name'  => 'Africa - Middle Africa',
						'value' => '017,countries',
					),
					array(
						'name'  => 'Africa - Eastern Africa',
						'value' => '014,countries',
					),
					array(
						'name'  => 'Africa - Southern Africa',
						'value' => '018,countries',
					),
					array(
						'name'  => 'Europe',
						'value' => '150,countries',
					),
					array(
						'name'  => 'Europe - Subcontinents Regions',
						'value' => '150,subcontinents',
					),
					array(
						'name'  => 'Europe - Northern Europe',
						'value' => '154,countries',
					),
					array(
						'name'  => 'Europe - Western Europe',
						'value' => '155,countries',
					),
					array(
						'name'  => 'Europe - Eastern Europe',
						'value' => '151,countries',
					),
					array(
						'name'  => 'Europe - Southern Europe',
						'value' => '039,countries',
					),
					array(
						'name'  => 'Americas',
						'value' => '019,countries',
					),
					array(
						'name'  => 'Americas - Subcontinents Regions',
						'value' => '019,subcontinents',
					),
					array(
						'name'  => 'Americas - Northern America',
						'value' => '021,countries',
					),
					array(
						'name'  => 'Americas - Caribbean',
						'value' => '029,countries',
					),
					array(
						'name'  => 'Americas - Central America',
						'value' => '013,countries',
					),
					array(
						'name'  => 'Americas - South America',
						'value' => '005,countries',
					),
					array(
						'name'  => 'Asia',
						'value' => '142,countries',
					),
					array(
						'name'  => 'Asia - Subcontinents Regions',
						'value' => '142,subcontinents',
					),
					array(
						'name'  => 'Asia - Central Asia',
						'value' => '143,countries',
					),
					array(
						'name'  => 'Asia - Eastern Asia',
						'value' => '030,countries',
					),
					array(
						'name'  => 'Asia - Southern Asia',
						'value' => '034,countries',
					),
					array(
						'name'  => 'Asia - South-Eastern Asia',
						'value' => '035,countries',
					),
					array(
						'name'  => 'Asia - Western Asia',
						'value' => '145,countries',
					),
					array(
						'name'  => 'Oceania',
						'value' => '009,countries',
					),
					array(
						'name'  => 'Oceania - Subcontinents Regions',
						'value' => '009,subcontinents',
					),
					array(
						'name'  => 'Oceania - Australia and New Zealand',
						'value' => '053,countries',
					),
					array(
						'name'  => 'Oceania - Melanesia',
						'value' => '054,countries',
					),
					array(
						'name'  => 'Oceania - Micronesia',
						'value' => '057,countries',
					),
					array(
						'name'  => 'Oceania - Polynesia',
						'value' => '061,countries',
					),
					array(
						'name'  => 'United States of America',
						'value' => 'US,countries',
					),
					array(
						'name'  => 'United States of America - States',
						'value' => 'US,provinces',
					),
					array(
						'name'  => 'United States of America - Metropolitan Areas',
						'value' => 'US,metros',
					),
					array(
						'name'  => 'USA - Alabama - Metropolitan Areas',
						'value' => 'US-AL,metros',
					),
					array(
						'name'  => 'USA - Alabama State',
						'value' => 'US-AL,provinces',
					),
					array(
						'name'  => 'USA - Alaska - Metropolitan Areas',
						'value' => 'US-AK,metros',
					),
					array(
						'name'  => 'USA - Alaska State',
						'value' => 'US-AK,provinces',
					),
					array(
						'name'  => 'USA - Arizona - Metropolitan Areas',
						'value' => 'US-AZ,metros',
					),
					array(
						'name'  => 'USA - Arizona State',
						'value' => 'US-AZ,provinces',
					),
					array(
						'name'  => 'USA - Arkansas - Metropolitan Areas',
						'value' => 'US-AR,metros',
					),
					array(
						'name'  => 'USA - Arkansas State',
						'value' => 'US-AR,provinces',
					),
					array(
						'name'  => 'USA - California - Metropolitan Areas',
						'value' => 'US-CA,metros',
					),
					array(
						'name'  => 'USA - California State',
						'value' => 'US-CA,provinces',
					),
					array(
						'name'  => 'USA - Colorado - Metropolitan Areas',
						'value' => 'US-CO,metros',
					),
					array(
						'name'  => 'USA - Colorado State',
						'value' => 'US-CO,provinces',
					),
					array(
						'name'  => 'USA - Connecticut - Metropolitan Areas',
						'value' => 'US-CT,metros',
					),
					array(
						'name'  => 'USA - Connecticut State',
						'value' => 'US-CT,provinces',
					),
					array(
						'name'  => 'USA - Delaware - Metropolitan Areas',
						'value' => 'US-DE,metros',
					),
					array(
						'name'  => 'USA - Delaware State',
						'value' => 'US-DE,provinces',
					),
					array(
						'name'  => 'USA - District of Columbia - Metropolitan Areas',
						'value' => 'US-DC,metros',
					),
					array(
						'name'  => 'USA - District of Columbia',
						'value' => 'US-DC,provinces',
					),
					array(
						'name'  => 'USA - Florida - Metropolitan Areas',
						'value' => 'US-FL,metros',
					),
					array(
						'name'  => 'USA - Florida State',
						'value' => 'US-FL,provinces',
					),
					array(
						'name'  => 'USA - Georgia - Metropolitan Areas',
						'value' => 'US-GA,metros',
					),
					array(
						'name'  => 'USA - Georgia State',
						'value' => 'US-GA,provinces',
					),
					array(
						'name'  => 'USA - Hawaii - Metropolitan Areas',
						'value' => 'US-HI,metros',
					),
					array(
						'name'  => 'USA - Hawaii State',
						'value' => 'US-HI,provinces',
					),
					array(
						'name'  => 'USA - Idaho - Metropolitan Areas',
						'value' => 'US-ID,metros',
					),
					array(
						'name'  => 'USA - Idaho State',
						'value' => 'US-ID,provinces',
					),
					array(
						'name'  => 'USA - Illinois - Metropolitan Areas',
						'value' => 'US-IL,metros',
					),
					array(
						'name'  => 'USA - Illinois State',
						'value' => 'US-IL,provinces',
					),
					array(
						'name'  => 'USA - Indiana - Metropolitan Areas',
						'value' => 'US-IN,metros',
					),
					array(
						'name'  => 'USA - Indiana State',
						'value' => 'US-IN,provinces',
					),
					array(
						'name'  => 'USA - Iowa - Metropolitan Areas',
						'value' => 'US-IA,metros',
					),
					array(
						'name'  => 'USA - Iowa State',
						'value' => 'US-IA,provinces',
					),
					array(
						'name'  => 'USA - Kansas - Metropolitan Areas',
						'value' => 'US-KS,metros',
					),
					array(
						'name'  => 'USA - Kansas State',
						'value' => 'US-KS,provinces',
					),
					array(
						'name'  => 'USA - Kentucky - Metropolitan Areas',
						'value' => 'US-KY,metros',
					),
					array(
						'name'  => 'USA - Kentucky State',
						'value' => 'US-KY,provinces',
					),
					array(
						'name'  => 'USA - Louisiana - Metropolitan Areas',
						'value' => 'US-LA,metros',
					),
					array(
						'name'  => 'USA - Louisiana State',
						'value' => 'US-LA,provinces',
					),
					array(
						'name'  => 'USA - Maine - Metropolitan Areas',
						'value' => 'US-ME,metros',
					),
					array(
						'name'  => 'USA - Maine State',
						'value' => 'US-ME,provinces',
					),
					array(
						'name'  => 'USA - Maryland - Metropolitan Areas',
						'value' => 'US-MD,metros',
					),
					array(
						'name'  => 'USA - Maryland State',
						'value' => 'US-MD,provinces',
					),
					array(
						'name'  => 'USA - Massachusetts - Metropolitan Areas',
						'value' => 'US-MA,metros',
					),
					array(
						'name'  => 'USA - Massachusetts State',
						'value' => 'US-MA,provinces',
					),
					array(
						'name'  => 'USA - Michigan - Metropolitan Areas',
						'value' => 'US-MI,metros',
					),
					array(
						'name'  => 'USA - Michigan State',
						'value' => 'US-MI,provinces',
					),
					array(
						'name'  => 'USA - Minnesota - Metropolitan Areas',
						'value' => 'US-MN,metros',
					),
					array(
						'name'  => 'USA - Minnesota State',
						'value' => 'US-MN,provinces',
					),
					array(
						'name'  => 'USA - Mississippi - Metropolitan Areas',
						'value' => 'US-MS,metros',
					),
					array(
						'name'  => 'USA - Mississippi State',
						'value' => 'US-MS,provinces',
					),
					array(
						'name'  => 'USA - Missouri - Metropolitan Areas',
						'value' => 'US-MO,metros',
					),
					array(
						'name'  => 'USA - Missouri State',
						'value' => 'US-MO,provinces',
					),
					array(
						'name'  => 'USA - Montana - Metropolitan Areas',
						'value' => 'US-MT,metros',
					),
					array(
						'name'  => 'USA - Montana State',
						'value' => 'US-MT,provinces',
					),
					array(
						'name'  => 'USA - Nebraska - Metropolitan Areas',
						'value' => 'US-NE,metros',
					),
					array(
						'name'  => 'USA - Nebraska State',
						'value' => 'US-NE,provinces',
					),
					array(
						'name'  => 'USA - Nevada - Metropolitan Areas',
						'value' => 'US-NV,metros',
					),
					array(
						'name'  => 'USA - Nevada State',
						'value' => 'US-NV,provinces',
					),
					array(
						'name'  => 'USA - New Hampshire - Metropolitan Areas',
						'value' => 'US-NH,metros',
					),
					array(
						'name'  => 'USA - New Hampshire State',
						'value' => 'US-NH,provinces',
					),
					array(
						'name'  => 'USA - New Jersey - Metropolitan Areas',
						'value' => 'US-NJ,metros',
					),
					array(
						'name'  => 'USA - New Jersey State',
						'value' => 'US-NJ,provinces',
					),
					array(
						'name'  => 'USA - New Mexico - Metropolitan Areas',
						'value' => 'US-NM,metros',
					),
					array(
						'name'  => 'USA - New Mexico State',
						'value' => 'US-NM,provinces',
					),
					array(
						'name'  => 'USA - New York - Metropolitan Areas',
						'value' => 'US-NY,metros',
					),
					array(
						'name'  => 'USA - New York State',
						'value' => 'US-NY,provinces',
					),
					array(
						'name'  => 'USA - North Carolina - Metropolitan Areas',
						'value' => 'US-NC,metros',
					),
					array(
						'name'  => 'USA - North Carolina State',
						'value' => 'US-NC,provinces',
					),
					array(
						'name'  => 'USA - North Dakota - Metropolitan Areas',
						'value' => 'US-ND,metros',
					),
					array(
						'name'  => 'USA - North Dakota State',
						'value' => 'US-ND,provinces',
					),
					array(
						'name'  => 'USA - Ohio - Metropolitan Areas',
						'value' => 'US-OH,metros',
					),
					array(
						'name'  => 'USA - Ohio State',
						'value' => 'US-OH,provinces',
					),
					array(
						'name'  => 'USA - Oklahoma - Metropolitan Areas',
						'value' => 'US-OK,metros',
					),
					array(
						'name'  => 'USA - Oklahoma State',
						'value' => 'US-OK,provinces',
					),
					array(
						'name'  => 'USA - Oregon - Metropolitan Areas',
						'value' => 'US-OR,metros',
					),
					array(
						'name'  => 'USA - Oregon State',
						'value' => 'US-OR,provinces',
					),
					array(
						'name'  => 'USA - Pennsylvania - Metropolitan Areas',
						'value' => 'US-PA,metros',
					),
					array(
						'name'  => 'USA - Pennsylvania State',
						'value' => 'US-PA,provinces',
					),
					array(
						'name'  => 'USA - Rhode Island - Metropolitan Areas',
						'value' => 'US-RI,metros',
					),
					array(
						'name'  => 'USA - Rhode Island State',
						'value' => 'US-RI,provinces',
					),
					array(
						'name'  => 'USA - South Carolina - Metropolitan Areas',
						'value' => 'US-SC,metros',
					),
					array(
						'name'  => 'USA - South Carolina State',
						'value' => 'US-SC,provinces',
					),
					array(
						'name'  => 'USA - South Dakota - Metropolitan Areas',
						'value' => 'US-SD,metros',
					),
					array(
						'name'  => 'USA - South Dakota State',
						'value' => 'US-SD,provinces',
					),
					array(
						'name'  => 'USA - Tennessee - Metropolitan Areas',
						'value' => 'US-TN,metros',
					),
					array(
						'name'  => 'USA - Tennessee State',
						'value' => 'US-TN,provinces',
					),
					array(
						'name'  => 'USA - Texas - Metropolitan Areas',
						'value' => 'US-TX,metros',
					),
					array(
						'name'  => 'USA - Texas State',
						'value' => 'US-TX,provinces',
					),
					array(
						'name'  => 'USA - Utah - Metropolitan Areas',
						'value' => 'US-UT,metros',
					),
					array(
						'name'  => 'USA - Utah State',
						'value' => 'US-UT,provinces',
					),
					array(
						'name'  => 'USA - Vermont - Metropolitan Areas',
						'value' => 'US-VT,metros',
					),
					array(
						'name'  => 'USA - Vermont State',
						'value' => 'US-VT,provinces',
					),
					array(
						'name'  => 'USA - Virginia - Metropolitan Areas',
						'value' => 'US-VA,metros',
					),
					array(
						'name'  => 'USA - Virginia State',
						'value' => 'US-VA,provinces',
					),
					array(
						'name'  => 'USA - Washington - Metropolitan Areas',
						'value' => 'US-WA,metros',
					),
					array(
						'name'  => 'USA - Washington State',
						'value' => 'US-WA,provinces',
					),
					array(
						'name'  => 'USA - West Virginia - Metropolitan Areas',
						'value' => 'US-WV,metros',
					),
					array(
						'name'  => 'USA - West Virginia State',
						'value' => 'US-WV,provinces',
					),
					array(
						'name'  => 'USA - Wisconsin - Metropolitan Areas',
						'value' => 'US-WI,metros',
					),
					array(
						'name'  => 'USA - Wisconsin State',
						'value' => 'US-WI,provinces',
					),
					array(
						'name'  => 'USA - Wyoming - Metropolitan Areas',
						'value' => 'US-WY,metros',
					),
					array(
						'name'  => 'USA - Wyoming State',
						'value' => 'US-WY,provinces',
					),
					array(
						'name'  => 'Afghanistan',
						'value' => 'AF,countries',
					),
					array(
						'name'  => 'Afghanistan - Provinces',
						'value' => 'AF,provinces',
					),
					array(
						'name'  => 'Aland Islands',
						'value' => 'AX,countries',
					),
					array(
						'name'  => 'Aland Islands - Provinces',
						'value' => 'AX,provinces',
					),
					array(
						'name'  => 'Albania',
						'value' => 'AL,countries',
					),
					array(
						'name'  => 'Albania - Provinces',
						'value' => 'AL,provinces',
					),
					array(
						'name'  => 'Algeria',
						'value' => 'DZ,countries',
					),
					array(
						'name'  => 'Algeria - Provinces',
						'value' => 'DZ,provinces',
					),
					array(
						'name'  => 'American Samoa',
						'value' => 'AS,countries',
					),
					array(
						'name'  => 'American Samoa - Provinces',
						'value' => 'AS,provinces',
					),
					array(
						'name'  => 'Andorra',
						'value' => 'AD,countries',
					),
					array(
						'name'  => 'Andorra - Provinces',
						'value' => 'AD,provinces',
					),
					array(
						'name'  => 'Angola',
						'value' => 'AO,countries',
					),
					array(
						'name'  => 'Angola - Provinces',
						'value' => 'AO,provinces',
					),
					array(
						'name'  => 'Anguilla',
						'value' => 'AI,countries',
					),
					array(
						'name'  => 'Anguilla - Provinces',
						'value' => 'AI,provinces',
					),
					array(
						'name'  => 'Antigua and Barbuda',
						'value' => 'AG,countries',
					),
					array(
						'name'  => 'Antigua and Barbuda - Provinces',
						'value' => 'AG,provinces',
					),
					array(
						'name'  => 'Argentina',
						'value' => 'AR,countries',
					),
					array(
						'name'  => 'Argentina - Provinces',
						'value' => 'AR,provinces',
					),
					array(
						'name'  => 'Armenia',
						'value' => 'AM,countries',
					),
					array(
						'name'  => 'Armenia - Provinces',
						'value' => 'AM,provinces',
					),
					array(
						'name'  => 'Aruba',
						'value' => 'AW,countries',
					),
					array(
						'name'  => 'Aruba - Provinces',
						'value' => 'AW,provinces',
					),
					array(
						'name'  => 'Australia',
						'value' => 'AU,countries',
					),
					array(
						'name'  => 'Australia - Provinces',
						'value' => 'AU,provinces',
					),
					array(
						'name'  => 'Austria',
						'value' => 'AT,countries',
					),
					array(
						'name'  => 'Austria - Provinces',
						'value' => 'AT,provinces',
					),
					array(
						'name'  => 'Azerbaijan',
						'value' => 'AZ,countries',
					),
					array(
						'name'  => 'Azerbaijan - Provinces',
						'value' => 'AZ,provinces',
					),
					array(
						'name'  => 'Bahamas',
						'value' => 'BS,countries',
					),
					array(
						'name'  => 'Bahamas - Provinces',
						'value' => 'BS,provinces',
					),
					array(
						'name'  => 'Bahrain',
						'value' => 'BH,countries',
					),
					array(
						'name'  => 'Bahrain - Provinces',
						'value' => 'BH,provinces',
					),
					array(
						'name'  => 'Bangladesh',
						'value' => 'BD,countries',
					),
					array(
						'name'  => 'Bangladesh - Provinces',
						'value' => 'BD,provinces',
					),
					array(
						'name'  => 'Barbados',
						'value' => 'BB,countries',
					),
					array(
						'name'  => 'Barbados - Provinces',
						'value' => 'BB,provinces',
					),
					array(
						'name'  => 'Belarus',
						'value' => 'BY,countries',
					),
					array(
						'name'  => 'Belarus - Provinces',
						'value' => 'BY,provinces',
					),
					array(
						'name'  => 'Belgium',
						'value' => 'BE,countries',
					),
					array(
						'name'  => 'Belgium - Provinces',
						'value' => 'BE,provinces',
					),
					array(
						'name'  => 'Belize',
						'value' => 'BZ,countries',
					),
					array(
						'name'  => 'Belize - Provinces',
						'value' => 'BZ,provinces',
					),
					array(
						'name'  => 'Benin',
						'value' => 'BJ,countries',
					),
					array(
						'name'  => 'Benin - Provinces',
						'value' => 'BJ,provinces',
					),
					array(
						'name'  => 'Bermuda',
						'value' => 'BM,countries',
					),
					array(
						'name'  => 'Bermuda - Provinces',
						'value' => 'BM,provinces',
					),
					array(
						'name'  => 'Bhutan',
						'value' => 'BT,countries',
					),
					array(
						'name'  => 'Bhutan - Provinces',
						'value' => 'BT,provinces',
					),
					array(
						'name'  => 'Bolivia, Plurinational State of',
						'value' => 'BO,countries',
					),
					array(
						'name'  => 'Bolivia, Plurinational State of - Provinces',
						'value' => 'BO,provinces',
					),
					array(
						'name'  => 'Bonaire, Sint Eustatius and Saba',
						'value' => 'BQ,countries',
					),
					array(
						'name'  => 'Bonaire, Sint Eustatius and Saba - Provinces',
						'value' => 'BQ,provinces',
					),
					array(
						'name'  => 'Bosnia and Herzegovina',
						'value' => 'BA,countries',
					),
					array(
						'name'  => 'Bosnia and Herzegovina - Provinces',
						'value' => 'BA,provinces',
					),
					array(
						'name'  => 'Botswana',
						'value' => 'BW,countries',
					),
					array(
						'name'  => 'Botswana - Provinces',
						'value' => 'BW,provinces',
					),
					array(
						'name'  => 'Bouvet Island',
						'value' => 'BV,countries',
					),
					array(
						'name'  => 'Bouvet Island - Provinces',
						'value' => 'BV,provinces',
					),
					array(
						'name'  => 'Brazil',
						'value' => 'BR,countries',
					),
					array(
						'name'  => 'Brazil - Provinces',
						'value' => 'BR,provinces',
					),
					array(
						'name'  => 'British Indian Ocean Territory',
						'value' => 'IO,countries',
					),
					array(
						'name'  => 'British Indian Ocean Territory - Provinces',
						'value' => 'IO,provinces',
					),
					array(
						'name'  => 'Brunei Darussalam',
						'value' => 'BN,countries',
					),
					array(
						'name'  => 'Brunei Darussalam - Provinces',
						'value' => 'BN,provinces',
					),
					array(
						'name'  => 'Bulgaria',
						'value' => 'BG,countries',
					),
					array(
						'name'  => 'Bulgaria - Provinces',
						'value' => 'BG,provinces',
					),
					array(
						'name'  => 'Burkina Faso',
						'value' => 'BF,countries',
					),
					array(
						'name'  => 'Burkina Faso - Provinces',
						'value' => 'BF,provinces',
					),
					array(
						'name'  => 'Burundi',
						'value' => 'BI,countries',
					),
					array(
						'name'  => 'Burundi - Provinces',
						'value' => 'BI,provinces',
					),
					array(
						'name'  => 'Cambodia',
						'value' => 'KH,countries',
					),
					array(
						'name'  => 'Cambodia - Provinces',
						'value' => 'KH,provinces',
					),
					array(
						'name'  => 'Cameroon',
						'value' => 'CM,countries',
					),
					array(
						'name'  => 'Cameroon - Provinces',
						'value' => 'CM,provinces',
					),
					array(
						'name'  => 'Canada',
						'value' => 'CA,countries',
					),
					array(
						'name'  => 'Canada - Provinces',
						'value' => 'CA,provinces',
					),
					array(
						'name'  => 'Cape Verde',
						'value' => 'CV,countries',
					),
					array(
						'name'  => 'Cape Verde - Provinces',
						'value' => 'CV,provinces',
					),
					array(
						'name'  => 'Cayman Islands',
						'value' => 'KY,countries',
					),
					array(
						'name'  => 'Cayman Islands - Provinces',
						'value' => 'KY,provinces',
					),
					array(
						'name'  => 'Central African Republic',
						'value' => 'CF,countries',
					),
					array(
						'name'  => 'Central African Republic - Provinces',
						'value' => 'CF,provinces',
					),
					array(
						'name'  => 'Chad',
						'value' => 'TD,countries',
					),
					array(
						'name'  => 'Chad - Provinces',
						'value' => 'TD,provinces',
					),
					array(
						'name'  => 'Chile',
						'value' => 'CL,countries',
					),
					array(
						'name'  => 'Chile - Provinces',
						'value' => 'CL,provinces',
					),
					array(
						'name'  => 'China',
						'value' => 'CN,countries',
					),
					array(
						'name'  => 'China - Provinces',
						'value' => 'CN,provinces',
					),
					array(
						'name'  => 'Christmas Island',
						'value' => 'CX,countries',
					),
					array(
						'name'  => 'Christmas Island - Provinces',
						'value' => 'CX,provinces',
					),
					array(
						'name'  => 'Cocos (Keeling) Islands',
						'value' => 'CC,countries',
					),
					array(
						'name'  => 'Cocos (Keeling) Islands - Provinces',
						'value' => 'CC,provinces',
					),
					array(
						'name'  => 'Colombia',
						'value' => 'CO,countries',
					),
					array(
						'name'  => 'Colombia - Provinces',
						'value' => 'CO,provinces',
					),
					array(
						'name'  => 'Comoros',
						'value' => 'KM,countries',
					),
					array(
						'name'  => 'Comoros - Provinces',
						'value' => 'KM,provinces',
					),
					array(
						'name'  => 'Congo',
						'value' => 'CG,countries',
					),
					array(
						'name'  => 'Congo - Provinces',
						'value' => 'CG,provinces',
					),
					array(
						'name'  => 'Congo, the Democratic Republic of the',
						'value' => 'CD,countries',
					),
					array(
						'name'  => 'Congo, the Democratic Republic of the - Provinces',
						'value' => 'CD,provinces',
					),
					array(
						'name'  => 'Cook Islands',
						'value' => 'CK,countries',
					),
					array(
						'name'  => 'Cook Islands - Provinces',
						'value' => 'CK,provinces',
					),
					array(
						'name'  => 'Costa Rica',
						'value' => 'CR,countries',
					),
					array(
						'name'  => 'Costa Rica - Provinces',
						'value' => 'CR,provinces',
					),
					array(
						'name'  => 'Cote d\'Ivoire ',
						'value' => 'CI,countries',
					),
					array(
						'name'  => 'Cote d\'Ivoire  - Provinces',
						'value' => 'CI,provinces',
					),
					array(
						'name'  => 'Croatia',
						'value' => 'HR,countries',
					),
					array(
						'name'  => 'Croatia - Provinces',
						'value' => 'HR,provinces',
					),
					array(
						'name'  => 'Cuba',
						'value' => 'CU,countries',
					),
					array(
						'name'  => 'Cuba - Provinces',
						'value' => 'CU,provinces',
					),
					array(
						'name'  => 'Curaçao',
						'value' => 'CW,countries',
					),
					array(
						'name'  => 'Curaçao - Provinces',
						'value' => 'CW,provinces',
					),
					array(
						'name'  => 'Cyprus',
						'value' => 'CY,countries',
					),
					array(
						'name'  => 'Cyprus - Provinces',
						'value' => 'CY,provinces',
					),
					array(
						'name'  => 'Czech Republic',
						'value' => 'CZ,countries',
					),
					array(
						'name'  => 'Czech Republic - Provinces',
						'value' => 'CZ,provinces',
					),
					array(
						'name'  => 'Denmark',
						'value' => 'DK,countries',
					),
					array(
						'name'  => 'Denmark - Provinces',
						'value' => 'DK,provinces',
					),
					array(
						'name'  => 'Djibouti',
						'value' => 'DJ,countries',
					),
					array(
						'name'  => 'Djibouti - Provinces',
						'value' => 'DJ,provinces',
					),
					array(
						'name'  => 'Dominica',
						'value' => 'DM,countries',
					),
					array(
						'name'  => 'Dominica - Provinces',
						'value' => 'DM,provinces',
					),
					array(
						'name'  => 'Dominican Republic',
						'value' => 'DO,countries',
					),
					array(
						'name'  => 'Dominican Republic - Provinces',
						'value' => 'DO,provinces',
					),
					array(
						'name'  => 'Ecuador',
						'value' => 'EC,countries',
					),
					array(
						'name'  => 'Ecuador - Provinces',
						'value' => 'EC,provinces',
					),
					array(
						'name'  => 'Egypt',
						'value' => 'EG,countries',
					),
					array(
						'name'  => 'Egypt - Provinces',
						'value' => 'EG,provinces',
					),
					array(
						'name'  => 'El Salvador',
						'value' => 'SV,countries',
					),
					array(
						'name'  => 'El Salvador - Provinces',
						'value' => 'SV,provinces',
					),
					array(
						'name'  => 'Equatorial Guinea',
						'value' => 'GQ,countries',
					),
					array(
						'name'  => 'Equatorial Guinea - Provinces',
						'value' => 'GQ,provinces',
					),
					array(
						'name'  => 'Eritrea',
						'value' => 'ER,countries',
					),
					array(
						'name'  => 'Eritrea - Provinces',
						'value' => 'ER,provinces',
					),
					array(
						'name'  => 'Estonia',
						'value' => 'EE,countries',
					),
					array(
						'name'  => 'Estonia - Provinces',
						'value' => 'EE,provinces',
					),
					array(
						'name'  => 'Ethiopia',
						'value' => 'ET,countries',
					),
					array(
						'name'  => 'Ethiopia - Provinces',
						'value' => 'ET,provinces',
					),
					array(
						'name'  => 'Falkland Islands (Malvinas)',
						'value' => 'FK,countries',
					),
					array(
						'name'  => 'Falkland Islands (Malvinas) - Provinces',
						'value' => 'FK,provinces',
					),
					array(
						'name'  => 'Faroe Islands',
						'value' => 'FO,countries',
					),
					array(
						'name'  => 'Faroe Islands - Provinces',
						'value' => 'FO,provinces',
					),
					array(
						'name'  => 'Fiji',
						'value' => 'FJ,countries',
					),
					array(
						'name'  => 'Fiji - Provinces',
						'value' => 'FJ,provinces',
					),
					array(
						'name'  => 'Finland',
						'value' => 'FI,countries',
					),
					array(
						'name'  => 'Finland - Provinces',
						'value' => 'FI,provinces',
					),
					array(
						'name'  => 'France',
						'value' => 'FR,countries',
					),
					array(
						'name'  => 'France - Provinces',
						'value' => 'FR,provinces',
					),
					array(
						'name'  => 'French Guiana',
						'value' => 'GF,countries',
					),
					array(
						'name'  => 'French Guiana - Provinces',
						'value' => 'GF,provinces',
					),
					array(
						'name'  => 'French Polynesia',
						'value' => 'PF,countries',
					),
					array(
						'name'  => 'French Polynesia - Provinces',
						'value' => 'PF,provinces',
					),
					array(
						'name'  => 'French Southern Territories',
						'value' => 'TF,countries',
					),
					array(
						'name'  => 'French Southern Territories - Provinces',
						'value' => 'TF,provinces',
					),
					array(
						'name'  => 'Gabon',
						'value' => 'GA,countries',
					),
					array(
						'name'  => 'Gabon - Provinces',
						'value' => 'GA,provinces',
					),
					array(
						'name'  => 'Gambia',
						'value' => 'GM,countries',
					),
					array(
						'name'  => 'Gambia - Provinces',
						'value' => 'GM,provinces',
					),
					array(
						'name'  => 'Georgia',
						'value' => 'GE,countries',
					),
					array(
						'name'  => 'Georgia - Provinces',
						'value' => 'GE,provinces',
					),
					array(
						'name'  => 'Germany',
						'value' => 'DE,countries',
					),
					array(
						'name'  => 'Germany - Provinces',
						'value' => 'DE,provinces',
					),
					array(
						'name'  => 'Ghana',
						'value' => 'GH,countries',
					),
					array(
						'name'  => 'Ghana - Provinces',
						'value' => 'GH,provinces',
					),
					array(
						'name'  => 'Gibraltar',
						'value' => 'GI,countries',
					),
					array(
						'name'  => 'Gibraltar - Provinces',
						'value' => 'GI,provinces',
					),
					array(
						'name'  => 'Greece',
						'value' => 'GR,countries',
					),
					array(
						'name'  => 'Greece - Provinces',
						'value' => 'GR,provinces',
					),
					array(
						'name'  => 'Greenland',
						'value' => 'GL,countries',
					),
					array(
						'name'  => 'Greenland - Provinces',
						'value' => 'GL,provinces',
					),
					array(
						'name'  => 'Grenada',
						'value' => 'GD,countries',
					),
					array(
						'name'  => 'Grenada - Provinces',
						'value' => 'GD,provinces',
					),
					array(
						'name'  => 'Guadeloupe',
						'value' => 'GP,countries',
					),
					array(
						'name'  => 'Guadeloupe - Provinces',
						'value' => 'GP,provinces',
					),
					array(
						'name'  => 'Guam',
						'value' => 'GU,countries',
					),
					array(
						'name'  => 'Guam - Provinces',
						'value' => 'GU,provinces',
					),
					array(
						'name'  => 'Guatemala',
						'value' => 'GT,countries',
					),
					array(
						'name'  => 'Guatemala - Provinces',
						'value' => 'GT,provinces',
					),
					array(
						'name'  => 'Guernsey',
						'value' => 'GG,countries',
					),
					array(
						'name'  => 'Guernsey - Provinces',
						'value' => 'GG,provinces',
					),
					array(
						'name'  => 'Guinea',
						'value' => 'GN,countries',
					),
					array(
						'name'  => 'Guinea - Provinces',
						'value' => 'GN,provinces',
					),
					array(
						'name'  => 'Guinea-Bissau',
						'value' => 'GW,countries',
					),
					array(
						'name'  => 'Guinea-Bissau - Provinces',
						'value' => 'GW,provinces',
					),
					array(
						'name'  => 'Guyana',
						'value' => 'GY,countries',
					),
					array(
						'name'  => 'Guyana - Provinces',
						'value' => 'GY,provinces',
					),
					array(
						'name'  => 'Haiti',
						'value' => 'HT,countries',
					),
					array(
						'name'  => 'Haiti - Provinces',
						'value' => 'HT,provinces',
					),
					array(
						'name'  => 'Heard Island and McDonald Islands',
						'value' => 'HM,countries',
					),
					array(
						'name'  => 'Heard Island and McDonald Islands - Provinces',
						'value' => 'HM,provinces',
					),
					array(
						'name'  => 'Holy See (Vatican City State)',
						'value' => 'VA,countries',
					),
					array(
						'name'  => 'Honduras',
						'value' => 'HN,countries',
					),
					array(
						'name'  => 'Honduras - Provinces',
						'value' => 'HN,provinces',
					),
					array(
						'name'  => 'Hong Kong',
						'value' => 'HK,countries',
					),
					array(
						'name'  => 'Hong Kong - Provinces',
						'value' => 'HK,provinces',
					),
					array(
						'name'  => 'Hungary',
						'value' => 'HU,countries',
					),
					array(
						'name'  => 'Hungary - Provinces',
						'value' => 'HU,provinces',
					),
					array(
						'name'  => 'Iceland',
						'value' => 'IS,countries',
					),
					array(
						'name'  => 'Iceland - Provinces',
						'value' => 'IS,provinces',
					),
					array(
						'name'  => 'India',
						'value' => 'IN,countries',
					),
					array(
						'name'  => 'India - Provinces',
						'value' => 'IN,provinces',
					),
					array(
						'name'  => 'Indonesia',
						'value' => 'ID,countries',
					),
					array(
						'name'  => 'Indonesia - Provinces',
						'value' => 'ID,provinces',
					),
					array(
						'name'  => 'Iran, Islamic Republic of',
						'value' => 'IR,countries',
					),
					array(
						'name'  => 'Iran, Islamic Republic of - Provinces',
						'value' => 'IR,provinces',
					),
					array(
						'name'  => 'Iraq',
						'value' => 'IQ,countries',
					),
					array(
						'name'  => 'Iraq - Provinces',
						'value' => 'IQ,provinces',
					),
					array(
						'name'  => 'Ireland',
						'value' => 'IE,countries',
					),
					array(
						'name'  => 'Ireland - Provinces',
						'value' => 'IE,provinces',
					),
					array(
						'name'  => 'Isle of Man',
						'value' => 'IM,countries',
					),
					array(
						'name'  => 'Isle of Man - Provinces',
						'value' => 'IM,provinces',
					),
					array(
						'name'  => 'Israel',
						'value' => 'IL,countries',
					),
					array(
						'name'  => 'Israel - Provinces',
						'value' => 'IL,provinces',
					),
					array(
						'name'  => 'Italy',
						'value' => 'IT,countries',
					),
					array(
						'name'  => 'Italy - Provinces',
						'value' => 'IT,provinces',
					),
					array(
						'name'  => 'Jamaica',
						'value' => 'JM,countries',
					),
					array(
						'name'  => 'Jamaica - Provinces',
						'value' => 'JM,provinces',
					),
					array(
						'name'  => 'Japan',
						'value' => 'JP,countries',
					),
					array(
						'name'  => 'Japan - Provinces',
						'value' => 'JP,provinces',
					),
					array(
						'name'  => 'Jersey',
						'value' => 'JE,countries',
					),
					array(
						'name'  => 'Jersey - Provinces',
						'value' => 'JE,provinces',
					),
					array(
						'name'  => 'Jordan',
						'value' => 'JO,countries',
					),
					array(
						'name'  => 'Jordan - Provinces',
						'value' => 'JO,provinces',
					),
					array(
						'name'  => 'Kazakhstan',
						'value' => 'KZ,countries',
					),
					array(
						'name'  => 'Kazakhstan - Provinces',
						'value' => 'KZ,provinces',
					),
					array(
						'name'  => 'Kenya',
						'value' => 'KE,countries',
					),
					array(
						'name'  => 'Kenya - Provinces',
						'value' => 'KE,provinces',
					),
					array(
						'name'  => 'Kiribati',
						'value' => 'KI,countries',
					),
					array(
						'name'  => 'Kiribati - Provinces',
						'value' => 'KI,provinces',
					),
					array(
						'name'  => 'Korea, Democratic People\'s Republic of',
						'value' => 'KP,countries',
					),
					array(
						'name'  => 'Korea, Democratic People\'s Republic of - Provinces',
						'value' => 'KP,provinces',
					),
					array(
						'name'  => 'Korea, Republic of',
						'value' => 'KR,countries',
					),
					array(
						'name'  => 'Korea, Republic of - Provinces',
						'value' => 'KR,provinces',
					),
					array(
						'name'  => 'Kosovo',
						'value' => 'XK,countries',
					),
					array(
						'name'  => 'Kuwait',
						'value' => 'KW,countries',
					),
					array(
						'name'  => 'Kuwait - Provinces',
						'value' => 'KW,provinces',
					),
					array(
						'name'  => 'Kyrgyzstan',
						'value' => 'KG,countries',
					),
					array(
						'name'  => 'Kyrgyzstan - Provinces',
						'value' => 'KG,provinces',
					),
					array(
						'name'  => 'Lao People\'s Democratic Republic',
						'value' => 'LA,countries',
					),
					array(
						'name'  => 'Lao People\'s Democratic Republic - Provinces',
						'value' => 'LA,provinces',
					),
					array(
						'name'  => 'Latvia',
						'value' => 'LV,countries',
					),
					array(
						'name'  => 'Latvia - Provinces',
						'value' => 'LV,provinces',
					),
					array(
						'name'  => 'Lebanon',
						'value' => 'LB,countries',
					),
					array(
						'name'  => 'Lebanon - Provinces',
						'value' => 'LB,provinces',
					),
					array(
						'name'  => 'Lesotho',
						'value' => 'LS,countries',
					),
					array(
						'name'  => 'Lesotho - Provinces',
						'value' => 'LS,provinces',
					),
					array(
						'name'  => 'Liberia',
						'value' => 'LR,countries',
					),
					array(
						'name'  => 'Liberia - Provinces',
						'value' => 'LR,provinces',
					),
					array(
						'name'  => 'Libya',
						'value' => 'LY,countries',
					),
					array(
						'name'  => 'Libya - Provinces',
						'value' => 'LY,provinces',
					),
					array(
						'name'  => 'Liechtenstein',
						'value' => 'LI,countries',
					),
					array(
						'name'  => 'Liechtenstein - Provinces',
						'value' => 'LI,provinces',
					),
					array(
						'name'  => 'Lithuania',
						'value' => 'LT,countries',
					),
					array(
						'name'  => 'Lithuania - Provinces',
						'value' => 'LT,provinces',
					),
					array(
						'name'  => 'Luxembourg',
						'value' => 'LU,countries',
					),
					array(
						'name'  => 'Luxembourg - Provinces',
						'value' => 'LU,provinces',
					),
					array(
						'name'  => 'Macao',
						'value' => 'MO,countries',
					),
					array(
						'name'  => 'Macao - Provinces',
						'value' => 'MO,provinces',
					),
					array(
						'name'  => 'Macedonia, the former Yugoslav Republic of',
						'value' => 'MK,countries',
					),
					array(
						'name'  => 'Macedonia, the former Yugoslav Republic of - Provinces',
						'value' => 'MK,provinces',
					),
					array(
						'name'  => 'Madagascar',
						'value' => 'MG,countries',
					),
					array(
						'name'  => 'Madagascar - Provinces',
						'value' => 'MG,provinces',
					),
					array(
						'name'  => 'Malawi',
						'value' => 'MW,countries',
					),
					array(
						'name'  => 'Malawi - Provinces',
						'value' => 'MW,provinces',
					),
					array(
						'name'  => 'Malaysia',
						'value' => 'MY,countries',
					),
					array(
						'name'  => 'Malaysia - Provinces',
						'value' => 'MY,provinces',
					),
					array(
						'name'  => 'Maldives',
						'value' => 'MV,countries',
					),
					array(
						'name'  => 'Maldives - Provinces',
						'value' => 'MV,provinces',
					),
					array(
						'name'  => 'Mali',
						'value' => 'ML,countries',
					),
					array(
						'name'  => 'Mali - Provinces',
						'value' => 'ML,provinces',
					),
					array(
						'name'  => 'Malta',
						'value' => 'MT,countries',
					),
					// array( 'name' => 'Malta - Provinces', 'value' => 'MT,provinces' ),
					array(
						'name'  => 'Marshall Islands',
						'value' => 'MH,countries',
					),
					array(
						'name'  => 'Marshall Islands - Provinces',
						'value' => 'MH,provinces',
					),
					array(
						'name'  => 'Martinique',
						'value' => 'MQ,countries',
					),
					array(
						'name'  => 'Martinique - Provinces',
						'value' => 'MQ,provinces',
					),
					array(
						'name'  => 'Mauritania',
						'value' => 'MR,countries',
					),
					array(
						'name'  => 'Mauritania - Provinces',
						'value' => 'MR,provinces',
					),
					array(
						'name'  => 'Mauritius',
						'value' => 'MU,countries',
					),
					array(
						'name'  => 'Mauritius - Provinces',
						'value' => 'MU,provinces',
					),
					array(
						'name'  => 'Mayotte',
						'value' => 'YT,countries',
					),
					array(
						'name'  => 'Mayotte - Provinces',
						'value' => 'YT,provinces',
					),
					array(
						'name'  => 'Mexico',
						'value' => 'MX,countries',
					),
					array(
						'name'  => 'Mexico - Provinces',
						'value' => 'MX,provinces',
					),
					array(
						'name'  => 'Micronesia, Federated States of',
						'value' => 'FM,countries',
					),
					array(
						'name'  => 'Micronesia, Federated States of - Provinces',
						'value' => 'FM,provinces',
					),
					array(
						'name'  => 'Moldova, Republic of',
						'value' => 'MD,countries',
					),
					array(
						'name'  => 'Moldova, Republic of - Provinces',
						'value' => 'MD,provinces',
					),
					array(
						'name'  => 'Monaco',
						'value' => 'MC,countries',
					),
					array(
						'name'  => 'Monaco - Provinces',
						'value' => 'MC,provinces',
					),
					array(
						'name'  => 'Mongolia',
						'value' => 'MN,countries',
					),
					array(
						'name'  => 'Mongolia - Provinces',
						'value' => 'MN,provinces',
					),
					array(
						'name'  => 'Montenegro',
						'value' => 'ME,countries',
					),
					array(
						'name'  => 'Montenegro - Provinces',
						'value' => 'ME,provinces',
					),
					array(
						'name'  => 'Montserrat',
						'value' => 'MS,countries',
					),
					array(
						'name'  => 'Montserrat - Provinces',
						'value' => 'MS,provinces',
					),
					array(
						'name'  => 'Morocco',
						'value' => 'MA,countries',
					),
					array(
						'name'  => 'Morocco - Provinces',
						'value' => 'MA,provinces',
					),
					array(
						'name'  => 'Mozambique',
						'value' => 'MZ,countries',
					),
					array(
						'name'  => 'Mozambique - Provinces',
						'value' => 'MZ,provinces',
					),
					array(
						'name'  => 'Myanmar',
						'value' => 'MM,countries',
					),
					array(
						'name'  => 'Myanmar - Provinces',
						'value' => 'MM,provinces',
					),
					array(
						'name'  => 'Namibia',
						'value' => 'NA,countries',
					),
					array(
						'name'  => 'Namibia - Provinces',
						'value' => 'NA,provinces',
					),
					array(
						'name'  => 'Nauru',
						'value' => 'NR,countries',
					),
					array(
						'name'  => 'Nauru - Provinces',
						'value' => 'NR,provinces',
					),
					array(
						'name'  => 'Nepal',
						'value' => 'NP,countries',
					),
					array(
						'name'  => 'Nepal - Provinces',
						'value' => 'NP,provinces',
					),
					array(
						'name'  => 'Netherlands',
						'value' => 'NL,countries',
					),
					array(
						'name'  => 'Netherlands - Provinces',
						'value' => 'NL,provinces',
					),
					array(
						'name'  => 'New Caledonia',
						'value' => 'NC,countries',
					),
					array(
						'name'  => 'New Caledonia - Provinces',
						'value' => 'NC,provinces',
					),
					array(
						'name'  => 'New Zealand',
						'value' => 'NZ,countries',
					),
					array(
						'name'  => 'New Zealand - Provinces',
						'value' => 'NZ,provinces',
					),
					array(
						'name'  => 'Nicaragua',
						'value' => 'NI,countries',
					),
					array(
						'name'  => 'Nicaragua - Provinces',
						'value' => 'NI,provinces',
					),
					array(
						'name'  => 'Niger',
						'value' => 'NE,countries',
					),
					array(
						'name'  => 'Niger - Provinces',
						'value' => 'NE,provinces',
					),
					array(
						'name'  => 'Nigeria',
						'value' => 'NG,countries',
					),
					array(
						'name'  => 'Nigeria - Provinces',
						'value' => 'NG,provinces',
					),
					array(
						'name'  => 'Niue',
						'value' => 'NU,countries',
					),
					array(
						'name'  => 'Niue - Provinces',
						'value' => 'NU,provinces',
					),
					array(
						'name'  => 'Norfolk Island',
						'value' => 'NF,countries',
					),
					array(
						'name'  => 'Norfolk Island - Provinces',
						'value' => 'NF,provinces',
					),
					array(
						'name'  => 'Northern Mariana Islands',
						'value' => 'MP,countries',
					),
					array(
						'name'  => 'Northern Mariana Islands - Provinces',
						'value' => 'MP,provinces',
					),
					array(
						'name'  => 'Norway',
						'value' => 'NO,countries',
					),
					array(
						'name'  => 'Norway - Provinces',
						'value' => 'NO,provinces',
					),
					array(
						'name'  => 'Oman',
						'value' => 'OM,countries',
					),
					array(
						'name'  => 'Oman - Provinces',
						'value' => 'OM,provinces',
					),
					array(
						'name'  => 'Pakistan',
						'value' => 'PK,countries',
					),
					array(
						'name'  => 'Pakistan - Provinces',
						'value' => 'PK,provinces',
					),
					array(
						'name'  => 'Palau',
						'value' => 'PW,countries',
					),
					array(
						'name'  => 'Palau - Provinces',
						'value' => 'PW,provinces',
					),
					array(
						'name'  => 'Palestinian Territory, Occupied',
						'value' => 'PS,countries',
					),
					array(
						'name'  => 'Palestinian Territory, Occupied - Provinces',
						'value' => 'PS,provinces',
					),
					array(
						'name'  => 'Panama',
						'value' => 'PA,countries',
					),
					array(
						'name'  => 'Panama - Provinces',
						'value' => 'PA,provinces',
					),
					array(
						'name'  => 'Papua New Guinea',
						'value' => 'PG,countries',
					),
					array(
						'name'  => 'Papua New Guinea - Provinces',
						'value' => 'PG,provinces',
					),
					array(
						'name'  => 'Paraguay',
						'value' => 'PY,countries',
					),
					array(
						'name'  => 'Paraguay - Provinces',
						'value' => 'PY,provinces',
					),
					array(
						'name'  => 'Peru',
						'value' => 'PE,countries',
					),
					array(
						'name'  => 'Peru - Provinces',
						'value' => 'PE,provinces',
					),
					array(
						'name'  => 'Philippines',
						'value' => 'PH,countries',
					),
					array(
						'name'  => 'Philippines - Provinces',
						'value' => 'PH,provinces',
					),
					array(
						'name'  => 'Pitcairn',
						'value' => 'PN,countries',
					),
					array(
						'name'  => 'Pitcairn - Provinces',
						'value' => 'PN,provinces',
					),
					array(
						'name'  => 'Poland',
						'value' => 'PL,countries',
					),
					array(
						'name'  => 'Poland - Provinces',
						'value' => 'PL,provinces',
					),
					array(
						'name'  => 'Portugal',
						'value' => 'PT,countries',
					),
					array(
						'name'  => 'Portugal - Provinces',
						'value' => 'PT,provinces',
					),
					array(
						'name'  => 'Puerto Rico',
						'value' => 'PR,countries',
					),
					array(
						'name'  => 'Puerto Rico - Provinces',
						'value' => 'PR,provinces',
					),
					array(
						'name'  => 'Qatar',
						'value' => 'QA,countries',
					),
					array(
						'name'  => 'Qatar - Provinces',
						'value' => 'QA,provinces',
					),
					array(
						'name'  => 'Reunion !Réunion',
						'value' => 'RE,countries',
					),
					array(
						'name'  => 'Reunion !Réunion - Provinces',
						'value' => 'RE,provinces',
					),
					array(
						'name'  => 'Romania',
						'value' => 'RO,countries',
					),
					array(
						'name'  => 'Romania - Provinces',
						'value' => 'RO,provinces',
					),
					array(
						'name'  => 'Russian Federation',
						'value' => 'RU,countries',
					),
					array(
						'name'  => 'Russian Federation - Provinces',
						'value' => 'RU,provinces',
					),
					array(
						'name'  => 'Rwanda',
						'value' => 'RW,countries',
					),
					array(
						'name'  => 'Rwanda - Provinces',
						'value' => 'RW,provinces',
					),
					array(
						'name'  => 'Saint Barthélemy',
						'value' => 'BL,countries',
					),
					array(
						'name'  => 'Saint Barthélemy - Provinces',
						'value' => 'BL,provinces',
					),
					array(
						'name'  => 'Saint Helena, Ascension and Tristan da Cunha',
						'value' => 'SH,countries',
					),
					array(
						'name'  => 'Saint Helena, Ascension and Tristan da Cunha - Provinces',
						'value' => 'SH,provinces',
					),
					array(
						'name'  => 'Saint Kitts and Nevis',
						'value' => 'KN,countries',
					),
					array(
						'name'  => 'Saint Kitts and Nevis - Provinces',
						'value' => 'KN,provinces',
					),
					array(
						'name'  => 'Saint Lucia',
						'value' => 'LC,countries',
					),
					array(
						'name'  => 'Saint Lucia - Provinces',
						'value' => 'LC,provinces',
					),
					array(
						'name'  => 'Saint Martin (French part)',
						'value' => 'MF,countries',
					),
					array(
						'name'  => 'Saint Martin (French part) - Provinces',
						'value' => 'MF,provinces',
					),
					array(
						'name'  => 'Saint Pierre and Miquelon',
						'value' => 'PM,countries',
					),
					array(
						'name'  => 'Saint Pierre and Miquelon - Provinces',
						'value' => 'PM,provinces',
					),
					array(
						'name'  => 'Saint Vincent and the Grenadines',
						'value' => 'VC,countries',
					),
					array(
						'name'  => 'Saint Vincent and the Grenadines - Provinces',
						'value' => 'VC,provinces',
					),
					array(
						'name'  => 'Samoa',
						'value' => 'WS,countries',
					),
					array(
						'name'  => 'Samoa - Provinces',
						'value' => 'WS,provinces',
					),
					array(
						'name'  => 'San Marino',
						'value' => 'SM,countries',
					),
					array(
						'name'  => 'San Marino - Provinces',
						'value' => 'SM,provinces',
					),
					array(
						'name'  => 'Sao Tome and Principe',
						'value' => 'ST,countries',
					),
					array(
						'name'  => 'Sao Tome and Principe - Provinces',
						'value' => 'ST,provinces',
					),
					array(
						'name'  => 'Saudi Arabia',
						'value' => 'SA,countries',
					),
					array(
						'name'  => 'Saudi Arabia - Provinces',
						'value' => 'SA,provinces',
					),
					array(
						'name'  => 'Senegal',
						'value' => 'SN,countries',
					),
					array(
						'name'  => 'Senegal - Provinces',
						'value' => 'SN,provinces',
					),
					array(
						'name'  => 'Serbia',
						'value' => 'RS,countries',
					),
					array(
						'name'  => 'Serbia - Provinces',
						'value' => 'RS,provinces',
					),
					array(
						'name'  => 'Seychelles',
						'value' => 'SC,countries',
					),
					array(
						'name'  => 'Seychelles - Provinces',
						'value' => 'SC,provinces',
					),
					array(
						'name'  => 'Sierra Leone',
						'value' => 'SL,countries',
					),
					array(
						'name'  => 'Sierra Leone - Provinces',
						'value' => 'SL,provinces',
					),
					array(
						'name'  => 'Singapore',
						'value' => 'SG,countries',
					),
					array(
						'name'  => 'Singapore - Provinces',
						'value' => 'SG,provinces',
					),
					array(
						'name'  => 'Sint Maarten (Dutch part)',
						'value' => 'SX,countries',
					),
					array(
						'name'  => 'Sint Maarten (Dutch part) - Provinces',
						'value' => 'SX,provinces',
					),
					array(
						'name'  => 'Slovakia',
						'value' => 'SK,countries',
					),
					array(
						'name'  => 'Slovakia - Provinces',
						'value' => 'SK,provinces',
					),
					array(
						'name'  => 'Slovenia',
						'value' => 'SI,countries',
					),
					array(
						'name'  => 'Slovenia - Provinces',
						'value' => 'SI,provinces',
					),
					array(
						'name'  => 'Solomon Islands',
						'value' => 'SB,countries',
					),
					array(
						'name'  => 'Solomon Islands - Provinces',
						'value' => 'SB,provinces',
					),
					array(
						'name'  => 'Somalia',
						'value' => 'SO,countries',
					),
					array(
						'name'  => 'Somalia - Provinces',
						'value' => 'SO,provinces',
					),
					array(
						'name'  => 'South Africa',
						'value' => 'ZA,countries',
					),
					array(
						'name'  => 'South Africa - Provinces',
						'value' => 'ZA,provinces',
					),
					array(
						'name'  => 'South Georgia and the South Sandwich Islands',
						'value' => 'GS,countries',
					),
					array(
						'name'  => 'South Georgia and the South Sandwich Islands - Provinces',
						'value' => 'GS,provinces',
					),
					array(
						'name'  => 'South Sudan',
						'value' => 'SS,countries',
					),
					array(
						'name'  => 'South Sudan - Provinces',
						'value' => 'SS,provinces',
					),
					array(
						'name'  => 'Spain',
						'value' => 'ES,countries',
					),
					array(
						'name'  => 'Spain - Provinces',
						'value' => 'ES,provinces',
					),
					array(
						'name'  => 'Sri Lanka',
						'value' => 'LK,countries',
					),
					array(
						'name'  => 'Sri Lanka - Provinces',
						'value' => 'LK,provinces',
					),
					array(
						'name'  => 'Sudan',
						'value' => 'SD,countries',
					),
					array(
						'name'  => 'Sudan - Provinces',
						'value' => 'SD,provinces',
					),
					array(
						'name'  => 'Suriname',
						'value' => 'SR,countries',
					),
					array(
						'name'  => 'Suriname - Provinces',
						'value' => 'SR,provinces',
					),
					array(
						'name'  => 'Svalbard and Jan Mayen',
						'value' => 'SJ,countries',
					),
					array(
						'name'  => 'Svalbard and Jan Mayen - Provinces',
						'value' => 'SJ,provinces',
					),
					array(
						'name'  => 'Swaziland',
						'value' => 'SZ,countries',
					),
					array(
						'name'  => 'Swaziland - Provinces',
						'value' => 'SZ,provinces',
					),
					array(
						'name'  => 'Sweden',
						'value' => 'SE,countries',
					),
					array(
						'name'  => 'Sweden - Provinces',
						'value' => 'SE,provinces',
					),
					array(
						'name'  => 'Switzerland',
						'value' => 'CH,countries',
					),
					array(
						'name'  => 'Switzerland - Provinces',
						'value' => 'CH,provinces',
					),
					array(
						'name'  => 'Syrian Arab Republic',
						'value' => 'SY,countries',
					),
					array(
						'name'  => 'Syrian Arab Republic - Provinces',
						'value' => 'SY,provinces',
					),
					array(
						'name'  => 'Taiwan, Province of China',
						'value' => 'TW,countries',
					),
					array(
						'name'  => 'Taiwan, Province of China - Provinces',
						'value' => 'TW,provinces',
					),
					array(
						'name'  => 'Tajikistan',
						'value' => 'TJ,countries',
					),
					array(
						'name'  => 'Tajikistan - Provinces',
						'value' => 'TJ,provinces',
					),
					array(
						'name'  => 'Tanzania, United Republic of',
						'value' => 'TZ,countries',
					),
					array(
						'name'  => 'Tanzania, United Republic of - Provinces',
						'value' => 'TZ,provinces',
					),
					array(
						'name'  => 'Thailand',
						'value' => 'TH,countries',
					),
					array(
						'name'  => 'Thailand - Provinces',
						'value' => 'TH,provinces',
					),
					array(
						'name'  => 'Timor-Leste',
						'value' => 'TL,countries',
					),
					array(
						'name'  => 'Timor-Leste - Provinces',
						'value' => 'TL,provinces',
					),
					array(
						'name'  => 'Togo',
						'value' => 'TG,countries',
					),
					array(
						'name'  => 'Togo - Provinces',
						'value' => 'TG,provinces',
					),
					array(
						'name'  => 'Tokelau',
						'value' => 'TK,countries',
					),
					array(
						'name'  => 'Tokelau - Provinces',
						'value' => 'TK,provinces',
					),
					array(
						'name'  => 'Tonga',
						'value' => 'TO,countries',
					),
					array(
						'name'  => 'Tonga - Provinces',
						'value' => 'TO,provinces',
					),
					array(
						'name'  => 'Trinidad and Tobago',
						'value' => 'TT,countries',
					),
					array(
						'name'  => 'Trinidad and Tobago - Provinces',
						'value' => 'TT,provinces',
					),
					array(
						'name'  => 'Tunisia',
						'value' => 'TN,countries',
					),
					array(
						'name'  => 'Tunisia - Provinces',
						'value' => 'TN,provinces',
					),
					array(
						'name'  => 'Turkey',
						'value' => 'TR,countries',
					),
					array(
						'name'  => 'Turkey - Provinces',
						'value' => 'TR,provinces',
					),
					array(
						'name'  => 'Turkmenistan',
						'value' => 'TM,countries',
					),
					array(
						'name'  => 'Turkmenistan - Provinces',
						'value' => 'TM,provinces',
					),
					array(
						'name'  => 'Turks and Caicos Islands',
						'value' => 'TC,countries',
					),
					array(
						'name'  => 'Turks and Caicos Islands - Provinces',
						'value' => 'TC,provinces',
					),
					array(
						'name'  => 'Tuvalu',
						'value' => 'TV,countries',
					),
					array(
						'name'  => 'Tuvalu - Provinces',
						'value' => 'TV,provinces',
					),
					array(
						'name'  => 'Uganda',
						'value' => 'UG,countries',
					),
					array(
						'name'  => 'Uganda - Provinces',
						'value' => 'UG,provinces',
					),
					array(
						'name'  => 'Ukraine',
						'value' => 'UA,countries',
					),
					array(
						'name'  => 'Ukraine - Provinces',
						'value' => 'UA,provinces',
					),
					array(
						'name'  => 'United Arab Emirates',
						'value' => 'AE,countries',
					),
					array(
						'name'  => 'United Arab Emirates - Provinces',
						'value' => 'AE,provinces',
					),
					array(
						'name'  => 'United Kingdom',
						'value' => 'GB,countries',
					),
					array(
						'name'  => 'United Kingdom - ( sub)Countries',
						'value' => 'GB,provinces',
					),
					array(
						'name'  => 'United States of America',
						'value' => 'US,countries',
					),
					array(
						'name'  => 'United States of America - States',
						'value' => 'US,provinces',
					),
					array(
						'name'  => 'United States Minor Outlying Islands',
						'value' => 'UM,countries',
					),
					array(
						'name'  => 'United States Minor Outlying Islands - Provinces',
						'value' => 'UM,provinces',
					),
					array(
						'name'  => 'Uruguay',
						'value' => 'UY,countries',
					),
					array(
						'name'  => 'Uruguay - Provinces',
						'value' => 'UY,provinces',
					),
					array(
						'name'  => 'Uzbekistan',
						'value' => 'UZ,countries',
					),
					array(
						'name'  => 'Uzbekistan - Provinces',
						'value' => 'UZ,provinces',
					),
					array(
						'name'  => 'Vanuatu',
						'value' => 'VU,countries',
					),
					array(
						'name'  => 'Vanuatu - Provinces',
						'value' => 'VU,provinces',
					),
					array(
						'name'  => 'Venezuela, Bolivarian Republic of',
						'value' => 'VE,countries',
					),
					array(
						'name'  => 'Venezuela, Bolivarian Republic of - Provinces',
						'value' => 'VE,provinces',
					),
					array(
						'name'  => 'Viet Nam',
						'value' => 'VN,countries',
					),
					array(
						'name'  => 'Viet Nam - Provinces',
						'value' => 'VN,provinces',
					),
					array(
						'name'  => 'Virgin Islands, British',
						'value' => 'VG,countries',
					),
					array(
						'name'  => 'Virgin Islands, British - Provinces',
						'value' => 'VG,provinces',
					),
					array(
						'name'  => 'Virgin Islands, U.S.',
						'value' => 'VI,countries',
					),
					array(
						'name'  => 'Virgin Islands, U.S. - Provinces',
						'value' => 'VI,provinces',
					),
					array(
						'name'  => 'Wallis and Futuna',
						'value' => 'WF,countries',
					),
					array(
						'name'  => 'Wallis and Futuna - Provinces',
						'value' => 'WF,provinces',
					),
					array(
						'name'  => 'Western Sahara',
						'value' => 'EH,countries',
					),
					array(
						'name'  => 'Western Sahara - Provinces',
						'value' => 'EH,provinces',
					),
					array(
						'name'  => 'Yemen',
						'value' => 'YE,countries',
					),
					array(
						'name'  => 'Yemen - Provinces',
						'value' => 'YE,provinces',
					),
					array(
						'name'  => 'Zambia',
						'value' => 'ZM,countries',
					),
					array(
						'name'  => 'Zambia - Provinces',
						'value' => 'ZM,provinces',
					),
					array(
						'name'  => 'Zimbabwe',
						'value' => 'ZW,countries',
					),
					array(
						'name'  => 'Zimbabwe - Provinces',
						'value' => 'ZW,provinces',
					),

				);
				?>
				<select name="<?php echo $name; ?>" id="<?php echo $name; ?>"
										<?php
										if ( $onchange !== '' ) {
											echo 'onchange="' . $onchange . '"';}
										?>
				>
				<?php
				foreach ( $regions as $region ) {
					?>
				<option value="<?php echo $region['value']; ?>"
										<?php
										if ( $selected === $region['value'] ) {
											echo "selected='selected'";}
										?>
				><?php echo $region['name']; ?></option>
				<?php } ?>
				</select>
				<?php

}


// Add Settings link to active plugins menu
add_filter( 'plugin_action_links', 'i_world_map_action_links', 10, 2 );

/**
 * Add Settings link to active plugins menu
 *
 * @param [string] $links
 * @param [string] $file
 * @return void
 */
function i_world_map_action_links( $links, $file ) {
	static $this_plugin;

	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	if ( $file === $this_plugin ) {
		$settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=iwm_settings">Settings</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}



/**
 * Manage Maps Screen
 *
 * @return void
 */
function i_world_map_manage() {

	$capability = i_world_map_user_cap();

	if ( ! is_admin() || ! current_user_can( $capability ) ) {
		return;
	}

	$alert    = '';
	$alertred = '';

	if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'delete' ) ) {
		$nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
		$action = i_world_map_delete( sanitize_key( $_GET['map'] ), $nonce );
		if( $action ){
			$alert = __( 'Map Deleted', 'interactive-world-maps' );
		}
	}

	if ( isset( $_GET['action'] ) && ( $_GET['action'] === 'duplicate' ) ) {
		$nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
		$action = i_world_map_duplicate( sanitize_key( $_GET['map'] ), $nonce );
		if( $action ){
			$alert = __( 'Map Duplicated', 'interactive-world-maps' );
		}

	}

	$dbversion      = get_option( 'i_world_map_db_version' );
	$iwm_db_version = 6;

	if ( $iwm_db_version !== intval( $dbversion ) ) {
		$alertred = __( 'Seems you might have changed the plugin files. Please desactivate and activate the plugin again to make sure there are no errors.', 'interactive-world-maps' );
	}

	$iwmaptable = new i_world_map_manage_table();
	$iwmaptable->prepare_items();

	?>
	<div class="wrap">
		<div id="interactive-world-maps" class="icon32"></div>
		<h2><?php _e( 'Manage Maps', 'interactive-world-maps' ); ?></h2>
		<?php
		if ( $alert !== '' ) {
			echo i_world_map_message( $alert );}
		?>
		<?php
		if ( $alertred !== '' ) {
			echo i_world_map_message_red( $alertred );}
		?>

		<form id="iwm-filter" method="get">

			<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />

			<?php $iwmaptable->display(); ?>

		</form>

		<br />
		<br />
		<?php $iwm_blog_id = get_current_blog_id(); ?>
		<a href="<?php echo get_admin_url( $iwm_blog_id, 'admin.php?page=iwm_add' ); ?>" class="button-primary">Add New Map</a>
		<br />
		<br />

	</div>
	<?php
}


/**
 * To add the code button to the editor
 *
 * @param [array] $plugins
 * @return void
 */
function i_world_map_tinymce_external_plugins( $plugins ) {

	$plugins['code'] = plugins_url( '/includes/js/tinymce.code.js', dirname( __FILE__ ) );
	return $plugins;
}
add_filter( 'mce_external_plugins', 'i_world_map_tinymce_external_plugins' );


// ADMIN MENU
// create custom plugin settings menu
add_action( 'admin_menu', 'i_world_map_create_menu' );


/**
 * Create admin menus
 *
 * @return void
 */
function i_world_map_create_menu() {

	if ( ! is_admin() ) {
		return;
	}

	// you can change capibility here
	$capability = i_world_map_user_cap();

	if ( current_user_can( $capability ) ) {
		// Add the top-level admin menu
		$page_title = __( 'Interactive World Maps', 'interactive-world-maps' );
		$menu_title = __( 'Interactive Maps', 'interactive-world-maps' );

		$menu_slug = 'i_world_map_menu';
		$function  = 'i_world_map_manage';
		$mainp     = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, '', '61.15' );

		// sub menu main
		$sub_menu_title = __( 'Manage Maps', 'interactive-world-maps' );
		$managep        = add_submenu_page( $menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function );

		$submenu_page_title = __( 'Add New', 'interactive-world-maps' );
		$submenu_title      = __( 'Add New Map', 'interactive-world-maps' );
		$submenu_slug       = 'iwm_add';
		$submenu_function   = 'i_world_map_add_new';
		$addp               = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		$submenu_page_title = __( 'Settings', 'interactive-world-maps' );
		$submenu_title      = __( 'Settings', 'interactive-world-maps' );
		$submenu_slug       = 'iwm_settings';
		$submenu_function   = 'i_world_map_settings_page';
		$defaultp           = add_submenu_page( $menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function );

		// call register settings function
		add_action( 'admin_init', 'i_world_map_register_settings' );
		add_action( $addp, 'i_world_map_includes_add' );
		add_action( $defaultp, 'i_world_map_includes_def' );
	}
}


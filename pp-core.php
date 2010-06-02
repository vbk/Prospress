<?php
/**
 * @package Prospress
 * @author Brent Shepherd
 * @version 0.1
 */

if( !defined( 'PP_CORE_DIR' ) )
	define( 'PP_CORE_DIR', PP_PLUGIN_DIR . '/pp-core' );
if( !defined( 'PP_CORE_URL' ) )
	define( 'PP_CORE_URL', PP_PLUGIN_URL . '/pp-core' );

/**
 * {@internal Missing Short Description}}
 *
 * @package Prospress
 * @since 0.1
 * @global $wpdb WordPress DB access object.
 * @uses is_site_admin() returns true if the current user is a site admin, false if not
 * @uses add_submenu_page() WP function to add a submenu item
 *
 * @return unknown
 */
function pp_core_install(){
	error_log('*** in pp_maybe_install ***');
	if( !get_option( 'currency_type' ) )
		update_option( 'currency_type', 'USD' );
	if( !get_option( 'currency_sign_location' ) )
		update_option( 'currency_sign_location', '1' );
}
add_action( 'pp_activation', 'pp_core_install' );


/**
 * Adds the Prospress admin menu item to the Site Admin tab.
 *
 * @package Prospress
 * @since 0.1
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @global $wpdb WordPress DB access object.
 * @uses is_site_admin() returns true if the current user is a site admin, false if not
 * @uses add_submenu_page() WP function to add a submenu item
 */
function pp_add_core_admin_menu() {
	global $pp_core_admin_page;

	$pp_core_admin_page = add_menu_page( __( 'Prospress', 'prospress' ), __( 'Prospress', 'prospress' ), 10, 'Prospress', '', PP_PLUGIN_URL . '/images/prospress-16x16.png', 3 );
	$pp_core_settings_page = add_submenu_page( 'Prospress', __( 'Prospress Settings', 'prospress' ), __( 'General Settings', 'prospress' ), 10, 'Prospress', 'pp_settings_page' );
}
add_action( 'admin_menu', 'pp_add_core_admin_menu' );

/**
 * The core component only knows about a few settings required for Prospress to run. This functions outputs those settings as a
 * central Prospress settings administration page and saves settings when it is submitted. 
 *
 * Other components, and potentially plugins for Prospress, can output their own settings on this page with the 'pp_core_settings_page'
 * hook. They can also save these by adding them to the 'pp_options_whitelist' filter. This filter works in the same was the Wordpress
 * settings page filter of the similar name.
 *
 * @package Prospress
 * @since 0.1
 *
 * @global $currencies Prospress currency list.
 */
function pp_settings_page(){
	global $currencies, $currency;

	if( isset( $_POST[ 'submit' ] ) && $_POST[ 'submit' ] == 'Save' ){

		$pp_options_whitelist = apply_filters( 'pp_options_whitelist', array( 'general' => array( 'currency_type' ) ) );

		foreach ( $pp_options_whitelist[ 'general' ] as $option ) {
			$option = trim($option);
			$value = null;
			if ( isset( $_POST[ $option ] ) )
				$value = $_POST[ $option ];
			if ( !is_array( $value ) )
				$value = trim( $value );
			$value = stripslashes_deep( $value );
			
			update_option( $option, $value );
			
			if( $option == 'currency_type' )
				$currency = $value;
		}
		$updated_message = __( 'Settings Updated.' );
	}
	?>
	<div class="wrap">
		<?php screen_icon( 'prospress' ); ?>
		<h2><?php _e( 'Prospress Settings', 'prospress' ) ?></h2>
		<?php if( isset( $updated_message ) ) { ?>
			<div id='message' class='updated fade'>
				<p><?php echo $updated_message; ?></p>
			</div>
		<?php } ?>
		<form action="" method="post">
			<h3><?php _e( 'Currency', 'prospress' )?></h3>
			<p><?php _e( 'Please choose a default currency for all transactions in your marketplace.', 'prospress' ); ?></p>

			<label for='currency_type'>
				<?php _e('Currency:' , 'prospress' );?>
				<select id='currency_type' name='currency_type'>
				<?php foreach( $currencies as $code => $details ) { ?>
					<option value='<?php echo $code; ?>' <?php selected( $currency, $code ); ?> >
						<?php echo $details[ 'currency_name' ]; ?> (<?php echo $code . ', ' . $details[ 'symbol' ]; ?>)
					</option>
				<?php } ?>
				</select>
			</label>
		<?php do_action( 'pp_core_settings_page' ); ?>
		<p class="submit">
			<input type="submit" value="Save" class="button-primary" name="submit">
		</p>
		</form>
	</div>
	<?php
}


/** 
 * Create and set global currency variables for sharing all currencies available in the marketplace and the currently 
 * selected currency type and symbol.
 * 
 * To make a new currency available, simply add an array to this variable. The key for this array must be the currency's 
 * ISO 4217 code. The array must contain the currency name and symbol. 
 * e.g. $currencies['CAD'] = array( 'currency' => __('Canadian Dollar', 'prospress' ), 'symbol' => '&#36;' ).
 * 
 * Once added, the currency will be available for selection from the admin page.
 * 
 * @package Prospress
 * @since 0.1
 * @global $currencies Prospress currency list. 
 * @global $currency The currency chosen for the marketplace. 
 * @global $currency_symbol Symbol of the marketplace's chosen currency, eg. $. 
 */
function pp_set_currency(){
	global $currencies, $currency, $currency_symbol;

	$currencies = array(
		'AUD' => array( 'currency_name' => __('Australian Dollar', 'prospress' ), 'symbol' => '&#36;' ),
		'GBP' => array( 'currency_name' => __('British Pound', 'prospress' ), 'symbol' => '&#163;' ),
		'CNY' => array( 'currency_name' => __('Chinese Yuan', 'prospress' ), 'symbol' => '&#165;' ),
		'EUR' => array( 'currency_name' => __('Euro', 'prospress' ), 'symbol' => '&#8364;' ),
		'INR' => array( 'currency_name' => __('Indian Rupee', 'prospress' ), 'symbol' => 'Rs' ),
		'JPY' => array( 'currency_name' => __('Japanese Yen', 'prospress' ), 'symbol' => '&#165;' ),
		'USD' => array( 'currency_name' => __('United States Dollar', 'prospress' ), 'symbol' => '&#36;' )
		);

	$currency = get_option( 'currency_type' );

	$currency_symbol = $currencies[ $currency ][ 'symbol' ];
}
add_action( 'init', 'pp_set_currency' );


/** 
 * For displaying monetary numbers, it's important to transform the number to include the currency symbol and correct number of decimals. 
 * 
 * @param number int | float
 * @param decimals int | float optional number of decimal places
 * @param currency string optional ISO 4217 code representing the currency. eg. for Japanese Yen, $currency == 'JPY'.
 **/
function pp_money_format( $number, $decimals = 2, $currency = '' ){
	global $currencies, $currency_symbol;

	$currency = strtoupper( $currency );

	if( empty( $currency ) || !array_key_exists( $currency, $currencies ) )
		$currency_sym = $currency_symbol;
	else
		$currency_sym = $currencies[ $currency ][ 'symbol' ];

	return $currency_sym . ' ' . number_format_i18n( $number, $decimals );
}


/**
 * The default WordPress admin menu icon has nothing on the Prospress jumping koi. This function adds 
 * the fish to menu pages under the Prospress top-level admin menu.
 *
 * @package Prospress
 * @since 0.1
 */
function pp_add_icon_css() {

	if ( strpos( $_SERVER['REQUEST_URI'], 'Prospress' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'custom_taxonomy_manage' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'invoice_settings' ) !== false ) {
		echo "<style type='text/css'>";
		echo "#icon-prospress{background: url(" . PP_PLUGIN_URL . "/images/prospress-35x35.png) no-repeat center transparent}";
		echo "</style>";
	}
}
add_action( 'admin_head', 'pp_add_icon_css' );


/** 
 * Add admin style and scripts that are required by more than one component. 
 * 
 * @package Prospress
 * @since 0.1
 */
function pp_core_admin_head() {

	if( strpos( $_SERVER['REQUEST_URI'], 'custom_taxonomy_manage' ) !== false  || strpos( $_SERVER['REQUEST_URI'], 'completed' ) !== false  || strpos( $_SERVER['REQUEST_URI'], 'bids' ) !== false )
		wp_enqueue_style( 'prospress-admin',  PP_CORE_URL . '/prospress-admin.css' );
}
add_action('admin_menu', 'pp_core_admin_head');
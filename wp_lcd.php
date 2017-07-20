<?php
/*
 * Plugin Name: Lead Champion discover
 * Plugin URI: http://www.leadchampion.com
 * Description: This plugin allows an easy integration of Lead Champion discover on sites running WordPress.
 * Version: 1.2.3
 * Author: Lead Champion team
 * Text Domain: lead-champion-discover
 * Domain Path: /i18n/
 * Author URI: http://www.leadchampion.com
 * Copyright 2016  Lead Champion  (email : tech@leadchampion.com)
 * License: GPL
 */


/*
 * Settings
 */
define( 'key_lcd_site_id', 'key_lcd_site_id', true );
define( 'lcd_default_site_id', '', true );

add_option( key_lcd_site_id, lcd_default_site_id, __('Lead Champion discover site ID to use','lead-champion-discover') );

function leadchampion_load_plugin_textdomain() {
    load_plugin_textdomain( 'lead-champion-discover', FALSE, basename( dirname( __FILE__ ) ) . '/i18n/' );
}
add_action( 'plugins_loaded', 'leadchampion_load_plugin_textdomain' );

/* Sanitization function for checkbox */
function lcd_sanitize_checkbox( $value ) {
	return $value ? 'on' : '';
}

function lcd_validate_site_id( $value ) {
	if (strlen($value) > 3)
		return $value;
	return '';
}	

// Create a option page for settings
add_action('admin_menu', 'add_lcd_option_page');

/*
 * Hook in the options page function
 */
function add_lcd_option_page() {
	global $wpdb;
	add_menu_page(__('Lead Champion discover Options','lead-champion-discover'), 'Lead Champion', 8, basename(__FILE__), 'lcd_option_page', plugins_url( 'images/lcd_favicon.png', __FILE__ ),61);
}

function lcd_option_page() {
	if( isset($_POST['lcd_update']) ) {
		if ( wp_verify_nonce($_POST['lcd-nonce-key'], 'wp_lcd') ) {
			$lcd_site_id = lcd_validate_site_id(sanitize_key($_POST[key_lcd_site_id]));
			update_option( key_lcd_site_id, $lcd_site_id );
		}
		// Give an updated message
		echo '<div class="updated fade"><p><strong>';
		_e('Lead Champion discover - Settings successfully saved!','lead-champion-discover');
		echo '</strong></p></div>';		
	}
	// Output the options page
	?>
	<div class="wrap">
		<form method="post" action="admin.php?page=wp_lcd.php">
			<input type="hidden" name="lcd-nonce-key" value="<?php echo wp_create_nonce('wp_lcd'); ?>" />
			<h2><img style="vertical-align: middle;padding-right: 15px; height: 35px;" src="<?php echo plugins_url('images/logoLCd.png', __FILE__ );?>"/><?php echo __('Settings','lead-champion-discover'); ?></h2>
			<?php
			if( get_option(key_lcd_site_id) == lcd_default_site_id ) {
				echo '<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">';
				_e('Lead Champion discover is active, but you do not enter a valid siteID. Lead Champion discover does <strong>NOT</strong> monitor your site.',
				   'lead-champion-discover'
				);
				echo '</div>';
			}?>

			<h3><?php
				printf(	__('In order to activate Lead Champion discover you have to enter the ID of your site. You can find it in your account at the path <a href="http://discover.leadchampion.com/#/configProperties" target="_blank">Settings/Properties</a> labelled as "Site ID".<br><br>You need an active license. Not yet a customer? Try our demo <a href="http://discover.leadchampion.com/demo.html" target="_blank">here</a>.',
						'lead-champion-discover'
					)
				);
			?></h3>

			<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
			<tr>
				<th valign="top" style="padding-top: 10px;width:100px;">
					<label for="<?php echo key_lcd_site_id; ?>"><?php _e('Your Site ID','lead-champion-discover');?>:</label>
				</th>
				<td>
					<?php
						printf ('<input type="text" size="50" name="%s" id="%s" value="%s" />',
							key_lcd_site_id,
							key_lcd_site_id,
							get_option(key_lcd_site_id)
						);
					?>
					<p style="margin: 5px 10px;"><?php _e('Enter your Lead Champion discover site ID.','lead-champion-discover');?></p>
				</td>
			</tr>
			</table>
			<p class="submit">
				<input class="button button-primary" type="submit" name="lcd_update" value="<?php _e('Save','lead-champion-discover');?>" />
			</p>
		</form>
	</div>
<?php
}

function insert_lcd_script() {
	echo '<!-- Begin Lead Champion discover tag -->';
	echo '<script type="text/javascript"> window._lcSiteid ='. esc_html(get_option(key_lcd_site_id)) .';';
	echo 'var _lcScript = document.createElement("script");_lcScript.src="//cdn.leadchampion.com/discover.js";';
	echo '_lcScript.async=1;if(document.body){document.body.appendChild(_lcScript);}else{document.getElementsByTagName("head")[0].appendChild(_lcScript);}';
	echo '</script>';
	echo '<!-- Lead Champion discover tag -->';
}

if (get_option(key_lcd_site_id) != lcd_default_site_id) {
	add_action('wp_head', 'insert_lcd_script');
}
?>

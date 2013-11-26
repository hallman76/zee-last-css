<?php
/**
 * Plugin Name: Zee Last CSS Loader
 * Plugin URI: http://github.om/hallman76/zee-last-css
 * Description: Loads CSS after other plugins have loaded to allow developers to overwrite styles defined in plugins 
 * Version: 0.9
 * Author: Steve Hallman
 * Author URI: http://github.com/hallman76/zee-last-css
 * License: GPL2
 */
function zee_last_enqueue_scripts() {

	$zee_uri = get_option('zee-css-file-uri', get_bloginfo('template_directory') . '/zee-last.css' );
	
	wp_enqueue_style( 'zee-last', $zee_uri);
}



/**

Some plugin developers load their CSS using the wp_print_styles (wrong) action instead of the wp_enqueue_scripts action. They either:
- don't know what they're doing
- are trying to make sure their styles load last (hack)

This plugin can optionally be loaded in the hack way to (hopefully) override plugin developers who use wp_print_styles().

 */
$print_css_hack = get_option('zee_last_print_hack', 'false' );

if ('true' == $print_css_hack) {
	add_action('wp_print_styles', 'zee_last_enqueue_scripts', 9999);
} else {
	add_action('wp_enqueue_scripts', 'zee_last_enqueue_scripts', 9999);
}



/**
	ADMIN OPTIONS
*/

add_action('admin_menu', 'zee_last_admin_menu');


//the admin menu
function zee_last_admin_menu() {
    add_options_page( 'Zee Last CSS', 'Zee Last CSS', 'manage_options', 'zee-last-css', 'zee_last_options' );
    
    //call register settings function
    add_action('admin_init', 'zee_last_register_settings');    
}

// Add settings link on plugin page
function zee_last_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=zee-last-css">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'zee_last_settings_link' );


function zee_last_register_settings() {
	register_setting( 'zee_last_settings', 'zee_last_print_hack' );
	register_setting( 'zee_last_settings', 'zee_last_file_uri' );
}


//plugin options
function zee_last_options() {
    //do not have privileges
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this plugins settings.' ) );
    }
    
    ?>
    
    <div class="wrap">
    <h2>Zee Last CSS Options</h2>
    
    <p>This plugin loads CSS after other plugins have loaded to allow developers to overwrite styles defined in plugins.</p>

    <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    
    	<table  class="form-table">
    		<tr valign="top">
    			<th scope="row">
    				<label for="zee_css_file_uri">CSS File</label>
    			</th>
    			<td >
    				<input name="zee_last_file_uri" type="text" id="zee_last_file_uri" value="<?php echo get_option('zee_last_file_uri'); ?>" /><br/>
    				<em>default: <?php echo get_bloginfo('template_directory') . '/zee-last.css' ?></em>
    			</td>
    		</tr>
    		<tr valign="top">
    			<th scope="row">
    				<label for="zee_last_print_hack">Print Hack*</label>
    			</th>
    			<td >
    				<input name="zee_last_print_hack" type="checkbox" id="zee_last_print_hack" value="true" <?php checked( get_option('zee_last_print_hack'), 'true', true ); ?> />
    			</td>
    		</tr>	
    					
    	</table>
  
    
    	<input type="hidden" name="action" value="update" />
	    <input name="page_options" type="hidden" value="zee_last_file_uri,zee_last_print_hack" />
    
    	<p>
    		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    	</p>
    	
    	<p></p>
    	<p>* Some plugin developers load their CSS using the wp_print_styles action (wrong) instead of the wp_enqueue_scripts action. 
    	They are trying to make sure their styles load last, which is a bit of a hack. Checking the "hack" box above loads the specified CSS file using the same method.</p>
    
    </div>
    
    
    
    </form>
    </div>    
    <?php 
}






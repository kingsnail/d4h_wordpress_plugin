<?php 
/*
Plugin Name: D4H Import Incidents
Plugin URI: http://woodheadmrt.org/
Description: Plug-in to transfer D4H incidents to custom Wordpress Post Type
Version: 1.1
Author: Mark Pearce
Author URI: http://silkstone-technology.co.uk/
License: GPLv3
*/

/* Copyright 2018 Mark Pearce (email: mpearce@woodheadmrt.org)

    This file is part of the D4H-Worpress Importer Plugin.

    D4H-Worpress Importer Plugin is free software: you can redistribute it 
    and/or modify it under the terms of the GNU General Public License as 
    published by the Free Software Foundation, either version 3 of the 
    License, or (at your option) any later version.

    D4H-Worpress Importer Plugin is distributed in the hope that it will be 
    useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along 
    with D4H-Worpress Importer Plugin.  If not, see <https://www.gnu.org/licenses/>.
*/

require 'include/D4H_get_incidents.php';
require 'include/D4H_plugin_options.php';
require 'include/D4H_api.php';

register_activation_hook(__FILE__,'D4H_incidents_install');
register_deactivation_hook(__FILE__, 'D4H_incidents_deactivate');

// This shortcode can be used to test and verify the operation of the 
// plugin - add it to the content of a page and it will perform a fetch 
// of the D4H data and output a log as it progresses.
add_shortcode('D4H_get_incidents','D4H_sync_incidents');

// add the admin settings and such
add_action('admin_init', 'UDMD4H_admin_init');

// add the admin options page
add_action('admin_menu', 'UDMD4H_admin_add_page');

function UDMD4H_admin_add_page() {
   add_options_page('D4H Importer Settings', 'D4H Importer Settings', 'manage_options', 'D4H-plugin', 'UDMD4H_options_page');
}

function D4H_incidents_install() {
	// Installation actions...
    
    // Define the array of cofiguration settings for this plug-in
    $UDMD4H_incidents_options = array(
		'API_Key'            => '',                    // The API key is the D4H API Access Key
        'Incident_Post_Type' => 'incident',            // Allows incidents to be posted as a custom post type.
                                                       // Set this to 'post' if no custom type is being used.
        'Post_Author_ID'     => '1',                   // This allows the incidents to be posted by a specific author.
        'Base_URL'           => 
           'https://api.eu.d4h.org/v2/team/incidents', // This is the base URL that is 
                                                       // used to generate API calls. The default is for EU region.
        'META_date'          => 'incident_date_time',  // Wordpress Metadata field for the incident date/time.
        'META_lat'           => 'incident_latitude',   // Wordpress Metadata field for the incident latitude.
        'META_long'          => 'incident_longitude',  // Wordpress Metadata field for the incident longitude.
        'META_cat'           => 'incident_type',       // Wordpress Metadata field for the incident type.
        'META_ref'           => 'incident_id'          // Wordpress Metadata field for the incident reference number.
	);
    
    update_option('UDMD4H_incidents_options', $UDMD4H_incidents_options);
    
    if (! wp_next_scheduled ( 'D4H_Timed_Sync' )) {
	wp_schedule_event(time(), 'hourly', 'D4H_Timed_Sync');
    }
	
}

add_action('D4H_Timed_Sync', 'do_this_hourly');

function do_this_hourly() {
    $r = D4H_get_incidents();
}

function D4H_incidents_deactivate() {
	wp_clear_scheduled_hook('D4H_Timed_Sync');
	}

function D4H_sync_incidents(){
    echo D4H_get_incidents();
	return;	
}

// Add the 'settings' link to the plug-in entry on the plugins page
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'UDMD4H_add_plugin_page_settings_link');
function UDMD4H_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'options-general.php?page=D4H-plugin' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}

?>
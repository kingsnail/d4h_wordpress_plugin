<?php
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
function UDMD4H_options_page(){
?>
   <div>
   <h2>D4H Incident Import</h2>
   Options relating to the D4H Importer Plugin.
   <form action="options.php" method="post">
   <?php settings_fields('UDMD4H_incident_import_options'); ?>
   <?php do_settings_sections('UDMD4H_plugin'); ?>
 
   <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
   </form></div>
 
<?php
}?>
<?php
function UDMD4H_admin_init(){
   register_setting(    'UDMD4H_incident_import_options', 'UDMD4H_incident_import_options', 'UDMD4H_incident_import_options_validate' );

   add_settings_section('plugin_main',           'Main Settings', 'plugin_section_text',    'UDMD4H_plugin');
   add_settings_field(  'UDMD4H_API_Key',        'API Key',       'UDMD4H_API_Key_input',   'UDMD4H_plugin', 'plugin_main');
   add_settings_field(  'UDMD4H_Post_Type',      'Post Type',     'UDMD4H_Post_Type_input', 'UDMD4H_plugin', 'plugin_main');
   add_settings_field(  'UDMD4H_Post_Author_ID', 'Post Author ID','UDMD4H_Author_ID_input', 'UDMD4H_plugin', 'plugin_main');
   add_settings_field(  'UDMD4H_Base_URL',       'Base URL',      'UDMD4H_Base_URL_input',  'UDMD4H_plugin', 'plugin_main');

   add_settings_section('plugin_meta',           'Meta-data Field Settings', 'plugin_meta_text',    'UDMD4H_plugin');
   add_settings_field(  'UDMD4H_META_date',      'Date field',               'UDMD4H_META_date_input', 'UDMD4H_plugin', 'plugin_meta');
   add_settings_field(  'UDMD4H_META_lat',       'Latitude field',           'UDMD4H_META_lat_input',  'UDMD4H_plugin', 'plugin_meta');
   add_settings_field(  'UDMD4H_META_long',      'Longitude field',          'UDMD4H_META_long_input', 'UDMD4H_plugin', 'plugin_meta');
   add_settings_field(  'UDMD4H_META_cat',       'Incident Type field',      'UDMD4H_META_cat_input',  'UDMD4H_plugin', 'plugin_meta');
   add_settings_field(  'UDMD4H_META_ref',       'Incident reference field', 'UDMD4H_META_ref_input',  'UDMD4H_plugin', 'plugin_meta');
}

function plugin_section_text() {
echo '<p>Ensure all of these settings contain valid data.</p>';
}
 
function plugin_meta_text() {
echo '<p>Ensure all of these settings contain valid data.</p>';
}

function UDMD4H_API_Key_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_API_Key_string' name='UDMD4H_incident_import_options[API_Key]' size='120' type='text' value='{$options['API_Key']}' />";
} 

function UDMD4H_Post_Type_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_Post_Type_string' name='UDMD4H_incident_import_options[Post_Type]' size='40' type='text' value='{$options['Post_Type']}' />";
} 

function UDMD4H_Author_ID_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_Post_Author_ID_string' name='UDMD4H_incident_import_options[Post_Author_ID]' size='5' type='text' value='{$options['Post_Author_ID']}' />";
} 

function UDMD4H_Base_URL_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_Base_URL_string' name='UDMD4H_incident_import_options[Base_URL]' size='120' type='text' value='{$options['Base_URL']}' />";
}

function UDMD4H_META_date_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_META_date_string' name='UDMD4H_incident_import_options[META_date]' size='40' type='text' value='{$options['META_date']}' />";
}

function UDMD4H_META_lat_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_META_lat_string' name='UDMD4H_incident_import_options[META_lat]' size='40' type='text' value='{$options['META_lat']}' />";
} 

function UDMD4H_META_long_input() {
   $options = get_option('UDMD4H_incident_import_options');
   echo "<input id='UDM_D4H_META_long_string' name='UDMD4H_incident_import_options[META_long]' size='40' type='text' value='{$options['META_long']}' />";
}

function UDMD4H_META_cat_input() {
  $options = get_option('UDMD4H_incident_import_options');
  echo "<input id='UDM_D4H_META_cat_string' name='UDMD4H_incident_import_options[META_cat]' size='40' type='text' value='{$options['META_cat']}' />";
} 

function UDMD4H_META_ref_input() {
  $options = get_option('UDMD4H_incident_import_options');
  echo "<input id='UDM_D4H_META_ref_string' name='UDMD4H_incident_import_options[META_ref]' size='40' type='text' value='{$options['META_ref']}' />";
} 

function UDMD4H_incident_import_options_validate($input) {
   $newinput['API_Key'] = trim($input['API_Key']);
   if(!preg_match('/^[a-zA-Z0-9]+$/i', $newinput['API_Key'])) {
      $newinput['API_Key'] = '';
   }
   $newinput['Post_Type'] = trim($input['Post_Type']);
   if(!preg_match('/^[a-zA-Z0-9_\-]+$/i', $newinput['Post_Type'])){
      $newinput['Post_Type'] = '';
   }
   $newinput['Post_Author_ID'] = trim($input['Post_Author_ID']);
   if(!preg_match('/^[a-zA-Z0-9_\-]+$/i', $newinput['Post_Author_ID'])){
      $newinput['Post_Author_ID'] = '';
   }
   $newinput['Base_URL'] = trim($input['Base_URL']);
   if(!preg_match('/^[a-zA-Z0-9\_\-\.\/\?\:]+$/i', $newinput['Base_URL'])){
      $newinput['Base_URL'] = '';
   }
   $newinput['META_date'] = trim($input['META_date']);
   if(!preg_match('/^[a-zA-Z0-9\_\-]+$/i', $newinput['META_date'])){
      $newinput['META_date'] = '';
   }
   $newinput['META_lat'] = trim($input['META_lat']);
   if(!preg_match('/^[a-zA-Z0-9\_\-]+$/i', $newinput['META_lat'])){
      $newinput['META_lat'] = '';
   }
   $newinput['META_long'] = trim($input['META_long']);
   if(!preg_match('/^[a-zA-Z0-9\_\-]+$/i', $newinput['META_long'])){
      $newinput['META_long'] = '';
   }
   $newinput['META_cat'] = trim($input['META_cat']);
   if(!preg_match('/^[a-zA-Z0-9\_\-]+$/i', $newinput['META_cat'])){
      $newinput['META_cat'] = '';
   }
   $newinput['META_ref'] = trim($input['META_ref']);
   if(!preg_match('/^[a-zA-Z0-9\_\-]+$/i', $newinput['META_ref'])){
      $newinput['META_ref'] = '';
   }
return $newinput;
}
?>
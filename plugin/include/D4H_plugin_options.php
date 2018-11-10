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
}
?>

<?php function plugin_section_text() {
echo '<p>Ensure all of these settings contain valid data.</p>';
} ?>

<?php function UDMD4H_API_Key_input() {
$options = get_option('UDMD4H_incident_import_options');
echo "<input id='UDM_D4H_API_Key_string' name='UDMD4H_incident_import_options[API_Key]' size='40' type='text' value='{$options['API_Key']}' />";
} ?>

<?php function UDMD4H_Post_Type_input() {
$options = get_option('UDMD4H_incident_import_options');
echo "<input id='UDM_D4H_Post_Type_string' name='UDMD4H_incident_import_options[Post_Type]' size='40' type='text' value='{$options['Post_Type']}' />";
} ?>

<?php function UDMD4H_Author_ID_input() {
$options = get_option('UDMD4H_incident_import_options');
echo "<input id='UDM_D4H_Post_Author_ID_string' name='UDMD4H_incident_import_options[Post_Author_ID]' size='40' type='text' value='{$options['Post_Author_ID']}' />";
} ?>

<?php
function UDMD4H_incident_import_options_validate($input) {
   $newinput['API_Key'] = trim($input['API_Key']);
   if(!preg_match('/^[a-zA-Z0-9]+$/i', $newinput['API_Key'])) {
      $newinput['API_Key'] = '';
   }
   $newinput['Post_Type'] = trim($input['Post_Type']);
   if(!preg_match('/^[a-zA-Z0-9]+$/i', $newinput['Post_Type'])){
      $newinput['Post_Type'] = '';
   }
   $newinput['Post_Author_ID'] = trim($input['Post_Author_ID']);
   if(!preg_match('/^[a-zA-Z0-9]+$/i', $newinput['Post_Author_ID'])){
      $newinput['Post_Author_ID'] = '';
   }
return $newinput;
}
?>
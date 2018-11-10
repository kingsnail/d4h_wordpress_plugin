<?php
// If uninstall is not called from WordPress then exit
if( !defined( 'WP_UNINSTALL_PLUGIN'))
	exit();
delete_option('trunce_utilities_options');

?>

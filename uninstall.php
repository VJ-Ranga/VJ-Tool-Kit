<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://vjranga.com/
 * @since      2.0.3
 *
 * @package    VJ_Tool_Kit
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clean up any plugin options or data
delete_option( 'vj_toolkit_options' );

// If you added any transients, you can delete them here
delete_transient( 'vj_toolkit_transient_data' );

// If you created any custom database tables, you would remove them here
// global $wpdb;
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}vj_toolkit_table" );

// You could also clean up any post meta data you added
// delete_post_meta_by_key( 'vj_toolkit_meta_key' );

// Clean up any user meta if you added any
// delete_metadata( 'user', 0, 'vj_toolkit_user_meta', '', true );
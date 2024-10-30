<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    Jamp
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Perform a few security checks.
$is_plugin_valid = ( isset( $_REQUEST['plugin'] ) && strpos( sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ), 'jamp-notes' ) !== false ) ? true : false;
$is_slug_valid   = ( isset( $_REQUEST['slug'] ) && strpos( sanitize_text_field( wp_unslash( $_REQUEST['slug'] ) ), 'jamp-notes' ) !== false ) ? true : false;
$is_user_allowed = current_user_can( 'delete_plugins' );

if ( ! $is_plugin_valid || ! $is_slug_valid || ! $is_user_allowed ) {
	exit;
}

// Check if plugin settings and data must be removed.
$option_delete_data_on_uninstall = get_option( 'jamp_delete_data_on_uninstall' );

if ( '1' === $option_delete_data_on_uninstall ) {

	// Delete notes in all statuses.
	$post_statuses = array_keys( get_post_stati() );

	$notes_args = array(
		'post_type'      => 'jamp_note',
		'post_status'    => $post_statuses,
		'posts_per_page' => -1,
	);

	$notes = get_posts( $notes_args );

	foreach ( $notes as $note ) {

		wp_delete_post( $note->ID, true );

	}

	// Delete options.
	$options = array(
		'jamp_delete_data_on_uninstall',
		'jamp_permissions',
		'jamp_enabled_target_types',
		'jamp_column_notes_closed',
	);

	foreach ( $options as $option ) {

		if ( get_option( $option ) ) {

			delete_option( $option );

		}
	}

	// Delete custom capabilities from all roles.
	require_once dirname( __FILE__ ) . '/includes/class-jamp.php';
	$capabilities = Jamp::$capabilities;
	$roles        = get_editable_roles();

	foreach ( $roles as $role_slug => $role_details ) {

		$current_role = get_role( $role_slug );

		foreach ( $capabilities as $capability ) {

			$current_role->remove_cap( $capability );

		}
	}

	// Delete transients for all users.
	$users = get_users();
	foreach ( $users as $user ) {

		$transient_name = 'jamp_session_user_' . $user->ID;

		if ( get_transient( $transient_name ) ) {

			delete_transient( $transient_name );

		}
	}
}

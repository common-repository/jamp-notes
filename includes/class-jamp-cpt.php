<?php
/**
 * Custom Post Types registration.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/includes
 */

/**
 * Custom Post Types registration.
 *
 * This class defines all code necessary to register Custom Post Types.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/includes
 * @author     Andrea Porotti
 */
class Jamp_CPT {

	/**
	 * Registers the custom post type.
	 *
	 * @since    1.0.0
	 */
	public static function register() {
		$labels = array(
			'name'                  => esc_html_x( 'Notes', 'Post Type General Name', 'jamp' ),
			'singular_name'         => esc_html_x( 'Note', 'Post Type Singular Name', 'jamp' ),
			'menu_name'             => esc_html__( 'Notes', 'jamp' ),
			'name_admin_bar'        => esc_html__( 'Note', 'jamp' ),
			'archives'              => esc_html__( 'Note Archives', 'jamp' ),
			'attributes'            => esc_html__( 'Note Attributes', 'jamp' ),
			'parent_item_colon'     => esc_html__( 'Parent Note:', 'jamp' ),
			'all_items'             => esc_html__( 'All Notes', 'jamp' ),
			'add_new_item'          => esc_html__( 'Add New Note', 'jamp' ),
			'add_new'               => esc_html__( 'Add New', 'jamp' ),
			'new_item'              => esc_html__( 'New Note', 'jamp' ),
			'edit_item'             => esc_html__( 'Edit Note', 'jamp' ),
			'update_item'           => esc_html__( 'Update Note', 'jamp' ),
			'view_item'             => esc_html__( 'View Note', 'jamp' ),
			'view_items'            => esc_html__( 'View Notes', 'jamp' ),
			'search_items'          => esc_html__( 'Search Notes', 'jamp' ),
			'not_found'             => esc_html__( 'Not found', 'jamp' ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'jamp' ),
			'featured_image'        => esc_html__( 'Featured image', 'jamp' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'jamp' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'jamp' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'jamp' ),
			'insert_into_item'      => esc_html__( 'Insert into Note', 'jamp' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this Note', 'jamp' ),
			'items_list'            => esc_html__( 'Notes list', 'jamp' ),
			'items_list_navigation' => esc_html__( 'Notes list navigation', 'jamp' ),
			'filter_items_list'     => esc_html__( 'Filter Notes list', 'jamp' ),
		);

		$args = array(
			'label'               => esc_html__( 'Notes', 'jamp' ),
			'labels'              => $labels,
			'description'         => esc_html__( 'A note you can attach to some WordPress dashboard elements.', 'jamp' ),
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-pressthis',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => array( 'jamp_note', 'jamp_notes' ),
			'map_meta_cap'        => true,
			'show_in_rest'        => false,
		);

		register_post_type( 'jamp_note', $args );
	}

}

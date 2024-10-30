<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Jamp
 * @subpackage Jamp/admin
 * @author     Andrea Porotti
 */
class Jamp_Admin {

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The name of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The list of all dashboard side menu items.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array     $sections_list    The list of all dashboard side menu items.
	 */
	private $sections_list = array();

	/**
	 * The list of all supported target types.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array     $target_types_list    The list of all supported target types.
	 */
	private $target_types_list = array();

	/**
	 * The ID of the note being processed.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      int       $current_note    The ID of the note being processed.
	 */
	private $current_note_id;

	/**
	 * The meta of the note being processed.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      mixed     $current_note    The meta of the note being processed.
	 */
	private $current_note_meta;

	/**
	 * Initializes the class and sets its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name    The name of this plugin.
	 * @param    string $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Registers the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Use uncompressed file if debug is enabled (remove .min from filename).
			$min = ( WP_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'jamp-admin-style', plugin_dir_url( __FILE__ ) . 'css/jamp-admin' . $min . '.css', array( 'wp-jquery-ui-dialog' ), $this->version, 'all' );

		}

	}

	/**
	 * Registers the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Use uncompressed file if debug is enabled (remove .min from filename).
			$min = ( WP_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'jamp-admin-script', plugin_dir_url( __FILE__ ) . 'js/jamp-admin' . $min . '.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, false );

			wp_localize_script(
				'jamp-admin-script',
				'jamp_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( $this->plugin_name ),
				)
			);

			wp_localize_script(
				'jamp-admin-script',
				'jamp_strings',
				array(
					'get_entities_list_error' => esc_html__( 'An error occurred while loading the items list. Please reload the page and try again.', 'jamp' ),
					'move_to_trash_error'     => esc_html__( 'An error occurred while moving the note to the trash. Please reload the page and try again.', 'jamp' ),
				)
			);

		}

	}

	/**
	 * Creates a list of all dashboard side menu items.
	 *
	 * @since    1.0.0
	 */
	public function build_sections_list() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			global $menu, $submenu;

			// The sections placed on the first level menu.
			$first_level_sections = array();

			// Menu items to not insert in the sections list.
			$menu_items_to_skip = array(
				'wp-menu-separator',
				'menu-top menu-icon-links',
			);

			// Gets sections placed on the first level menu.
			foreach ( $menu as $menu_item ) {
				if ( ! in_array( $menu_item[4], $menu_items_to_skip, true ) ) {
					// Gets section name removing unwanted HTML content and HTML code surrounding the section name.
					$name = trim( sanitize_text_field( ( strpos( $menu_item[0], '<' ) > 0 ) ? strstr( $menu_item[0], '<', true ) : $menu_item[0] ) );

					if ( ! empty( $name ) ) {
						// Gets section file without the "return" parameter.
						$file = remove_query_arg( 'return', wp_kses_decode_entities( $menu_item[2] ) );

						// Generate section url.
						if ( strpos( $menu_item[2], '.php' ) !== false ) {
							// The item contains a file name.
							$url = wp_specialchars_decode( admin_url( $menu_item[2] ) );
						} else {
							// Use admin.php if no file name has been found.
							$url = wp_specialchars_decode( add_query_arg( 'page', $menu_item[2], admin_url( '/admin.php' ) ) );
						}

						$first_level_sections[] = array(
							'name'       => $name,
							'file'       => $file,
							'url'        => $url,
							'is_submenu' => false,
							'is_enabled' => false, // Assuming it contains sub-items, a first level item is disabled by default.
						);
					}
				}
			}

			// Build complete sections list.
			foreach ( $first_level_sections as $section ) {
				// Add current first level section to the list.
				$this->sections_list[] = $section;

				// Check if there are sub sections of current first level section.
				if ( isset( $submenu[ $section['file'] ] ) ) {

					// Gets the sub sections of current first level section from the sub menu.
					foreach ( $submenu[ $section['file'] ] as $submenu_item ) {
						// Gets section name removing unwanted HTML content and HTML code surrounding the section name.
						$name = trim( sanitize_text_field( ( strpos( $submenu_item[0], '<' ) > 0 ) ? strstr( $submenu_item[0], '<', true ) : $submenu_item[0] ) );

						if ( ! empty( $name ) ) {
							// Gets section file without the "return" parameter.
							$file = remove_query_arg( 'return', wp_kses_decode_entities( $submenu_item[2] ) );

							// Generate section url.
							if ( strpos( $submenu_item[2], '.php' ) !== false ) {
								// The item contains a file name.
								$url = wp_specialchars_decode( admin_url( $submenu_item[2] ) );
							} elseif ( strpos( $section['file'], '.php' ) !== false ) {
								// The item parent contains a file name.
								$url = wp_specialchars_decode( add_query_arg( 'page', $submenu_item[2], admin_url( $section['file'] ) ) );
							} else {
								// Use admin.php if no file name has been found.
								$url = wp_specialchars_decode( add_query_arg( 'page', $submenu_item[2], admin_url( '/admin.php' ) ) );
							}

							$this->sections_list[] = array(
								'name'        => '-- ' . $name,
								'file'        => $file,
								'url'         => $url,
								'is_submenu'  => true,
								'is_enabled'  => true, // A sub item is enabled by default.
								'parent_url'  => $section['url'],
								'parent_name' => $section['name'],
							);
						}
					}
				} else {

					// Enable last inserted first level section because it must be selectable.
					end( $this->sections_list );
					$this->sections_list[ key( $this->sections_list ) ]['is_enabled'] = true;

				}
			}
		}

	}

	/**
	 * Creates a list of all supported target types.
	 *
	 * @since    1.0.0
	 * @param    boolean $filtered If true returns only the enabled target types.
	 * @param    boolean $return   If true returns the target types array.
	 */
	public function build_target_types_list( $filtered = true, $return = false ) {

		// Reset the target types list.
		$this->target_types_list = array();

		// Get enabled target types.
		$enabled_target_types = get_option( 'jamp_enabled_target_types', array() );

		// ---------------------------------------
		// Add the post types to the target types.
		// ---------------------------------------

		// Get post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		// Set post types to be ignored.
		$post_types_to_skip = array(
			'attachment',
			'jamp_note',
		);

		foreach ( $post_types as $post_type ) {

			if ( ! in_array( $post_type->name, $post_types_to_skip, true ) ) {

				if ( ( ! $filtered ) || ( $filtered && in_array( $post_type->name, $enabled_target_types, true ) ) ) {
					$this->target_types_list[] = array(
						'name'          => $post_type->name,
						'label'         => $post_type->label,
						'singular_name' => $post_type->labels->singular_name,
					);
				}
			}
		}

		// ------------------------------------
		// Add users to the target types.
		// ------------------------------------

		if ( ( ! $filtered ) || ( $filtered && in_array( 'users', $enabled_target_types, true ) ) ) {
			$this->target_types_list[] = array(
				'name'          => 'users',
				'label'         => esc_html__( 'Users' ),
				'singular_name' => esc_html__( 'User' ),
			);
		}

		// ------------------------------------
		// Add plugins to the target types.
		// ------------------------------------

		if ( ( ! $filtered ) || ( $filtered && in_array( 'plugins', $enabled_target_types, true ) ) ) {
			$this->target_types_list[] = array(
				'name'          => 'plugins',
				'label'         => esc_html__( 'Plugins' ),
				'singular_name' => esc_html__( 'Plugin' ),
			);
		}

		if ( $return ) {
			return $this->target_types_list;
		}

	}

	/**
	 * Creates a list of all entities of a target type.
	 * It's used by ajax calls.
	 *
	 * @since    1.0.0
	 */
	public function build_targets_list() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Checks the nonce is valid.
			check_ajax_referer( $this->plugin_name );

			$target_type = ( isset( $_POST['target_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['target_type'] ) ) : '';

			if ( ! empty( $target_type ) ) {

				$targets = array();

				// Check if the target type is a post type or a screen.
				if ( post_type_exists( $target_type ) ) { // It's a post type.

					// Gets posts as objects.
					$posts_objects = get_posts(
						array(
							'post_type'      => $target_type,
							'posts_per_page' => -1,
							'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash' ),
						)
					);

					// Add posts to the targets array.
					foreach ( $posts_objects as $post ) {
						$post_title      = ( ! empty( $post->post_title ) ) ? $post->post_title : esc_html__( '(no title)' );
						$post_status_obj = get_post_status_object( $post->post_status );

						$targets[] = array(
							'id'     => $post->ID,
							'title'  => $post_title,
							'status' => $post_status_obj->label,
						);
					}
				} else { // It's a screen.

					// Plugins page.
					if ( 'plugins' === $target_type ) {

						if ( ! function_exists( 'get_plugins' ) ) {
							require_once ABSPATH . 'wp-admin/includes/plugin.php';
						}

						$plugins        = get_plugins();
						$active_plugins = get_option( 'active_plugins' );

						// Add plugins to the targets array.
						foreach ( $plugins as $key => $plugin ) {
							$plugin_status = ( in_array( $key, $active_plugins, true ) ) ? esc_html__( 'Active', 'jamp' ) : esc_html__( 'Inactive', 'jamp' );

							$targets[] = array(
								'id'     => $key,
								'title'  => $plugin['Name'],
								'status' => $plugin_status,
							);
						}
					}

					// Users page.
					if ( 'users' === $target_type ) {

						$users = get_users();

						// Add users to the targets array.
						foreach ( $users as $user ) {
							$id           = $user->data->ID;
							$display_name = $user->data->display_name;

							// Get translated role name (first letter must be capitalized).
							$role = translate_user_role( ucfirst( $user->roles[0] ) );

							$targets[] = array(
								'id'     => $id,
								'title'  => $display_name,
								'status' => $role,
							);
						}
					}
				}

				wp_send_json_success( $targets );

			} else {

				wp_send_json_error( '' );

			}
		} else {

			wp_send_json_error( '' );

		}

	}

	/**
	 * Adds the note settings meta box.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_box() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Get admin menu items.
			do_action( 'adminmenu' );

			// Get enabled target types.
			$target_types = $this->build_target_types_list( true, true );

			// Get all target types.
			$target_types_full = $this->build_target_types_list( false, true );

			$screens = array( 'jamp_note' );
			foreach ( $screens as $screen ) {
				add_meta_box(
					'jamp_meta_box',
					esc_html__( 'Note Settings', 'jamp' ),
					array( $this, 'meta_box_html_cb' ),
					$screen,
					'side',
					'default',
					array(
						'sections'          => $this->sections_list,
						'target_types'      => $target_types,
						'target_types_full' => $target_types_full,
					)
				);
			}
		}

	}

	/**
	 * Outputs the note settings meta box HTML.
	 *
	 * @since    1.0.0
	 * @param    object $post Current post.
	 * @param    array  $args Variables coming from the registered meta box.
	 */
	public static function meta_box_html_cb( $post, $args ) {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/jamp-admin-meta-box.php';

	}

	/**
	 * Adds the view notes meta box.
	 *
	 * @since    1.1.0
	 */
	public function add_meta_box_view_notes() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			global $current_screen;

			// Do not add metabox when creating a new item.
			if ( 'add' !== $current_screen->action ) {

				$enabled_post_types = array_column( $this->target_types_list, 'name' );

				add_meta_box(
					'jamp_meta_box_view_notes',
					esc_html__( 'Notes', 'jamp' ),
					array( $this, 'meta_box_view_notes_html_cb' ),
					$enabled_post_types,
					'normal',
					'default',
					array(
						'admin' => $this,
					)
				);

			}
		}

	}

	/**
	 * Outputs the view notes meta box HTML.
	 *
	 * @since    1.1.0
	 * @param    object $post Current post.
	 * @param    array  $args Variables coming from the registered meta box.
	 */
	public static function meta_box_view_notes_html_cb( $post, $args ) {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/jamp-admin-meta-box-view-notes.php';

	}

	/**
	 * Saves meta data.
	 *
	 * @since    1.0.0
	 * @param    int $post_id Current post ID.
	 */
	public function save_meta_data( $post_id ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Checks save status and nonce.
			$is_autosave    = wp_is_post_autosave( $post_id );
			$is_revision    = wp_is_post_revision( $post_id );
			$is_nonce_valid = ( isset( $_POST['jamp-meta-box-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jamp-meta-box-nonce'] ) ), 'jamp_meta_box_nonce_secret_action' ) ) ? true : false;

			// Exits script depending on save status and nonce.
			if ( $is_autosave || $is_revision || ! $is_nonce_valid ) {
				return;
			}

			// Check for field values and save them.
			if ( isset( $_POST['scope'] ) ) {
				update_post_meta( $post_id, 'jamp_scope', sanitize_text_field( wp_unslash( $_POST['scope'] ) ) );

				if ( 'global' === $_POST['scope'] ) {
					update_post_meta( $post_id, 'jamp_target_type', 'global' );
					update_post_meta( $post_id, 'jamp_target', 'global' );
				}

				if ( 'section' === $_POST['scope'] ) {
					if ( isset( $_POST['section'] ) ) {
						update_post_meta( $post_id, 'jamp_target_type', 'section' );
						update_post_meta( $post_id, 'jamp_target', sanitize_text_field( wp_unslash( $_POST['section'] ) ) );
					}
				}

				if ( 'entity' === $_POST['scope'] ) {
					if ( isset( $_POST['target-type'] ) ) {
						update_post_meta( $post_id, 'jamp_target_type', sanitize_text_field( wp_unslash( $_POST['target-type'] ) ) );
					}

					if ( isset( $_POST['target'] ) ) {
						update_post_meta( $post_id, 'jamp_target', sanitize_text_field( wp_unslash( $_POST['target'] ) ) );
					}
				}

				if ( isset( $_POST['color'] ) ) {
					update_post_meta( $post_id, 'jamp_color', sanitize_text_field( wp_unslash( $_POST['color'] ) ) );
				}
			}
		}

	}

	/**
	 * Adds a custom column to an admin page.
	 *
	 * @since    1.0.0
	 * @param    array $columns List of table columns.
	 */
	public function manage_columns_headers( $columns ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			$post_type = $this->get_current_post_type();

			if ( 'jamp_note' === $post_type ) { // Notes admin page.

				// Get all target types.
				$this->build_target_types_list( false );

				// Get Date column label and remove the column.
				$date_column_label = $columns['date'];
				unset( $columns['date'] );

				// Adds custom columns for notes page.
				$columns['jamp_author']   = esc_html__( 'Author', 'jamp' );
				$columns['jamp_location'] = esc_html__( 'Location', 'jamp' );
				$columns['jamp_color']    = esc_html__( 'Color', 'jamp' );

				// Re-add Date column at the end.
				$columns['date'] = $date_column_label;

			} else { // Other admin pages.

				// Get enabled target types.
				$this->build_target_types_list();

				// Create a list of target types names.
				$enabled_target_types_names = array_column( $this->target_types_list, 'name' );

				// Get the current target type.
				$target_type = '';

				if ( ! empty( $post_type ) ) { // It's a post type admin page.
					$target_type = $post_type;
				} else { // It's another admin page.
					$screen      = get_current_screen();
					$target_type = $screen->id;
				}

				// Adds custom columns for target type.
				if ( in_array( $target_type, $enabled_target_types_names, true ) ) {
					$columns['jamp_note'] = esc_html__( 'Notes', 'jamp' );
				}
			}
		}

		return $columns;

	}

	/**
	 * Shows the column content.
	 *
	 * @since    1.0.0
	 * @param    string $column_name Current table column name.
	 * @param    int    $post_id     Current post ID.
	 */
	public function manage_columns_content( $column_name, $post_id ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Load the file if the column name contains the word 'jamp'.
			if ( strpos( $column_name, 'jamp' ) !== false ) {

				// If current post is a note and it's the first time we get this post, get note's meta.
				if ( 'jamp_note' === get_post_type( $post_id ) && $this->current_note_id !== $post_id ) {
					// Save current note.
					$this->current_note_id = $post_id;

					// Get note's meta.
					$this->current_note_meta = get_post_meta( $post_id );
				}

				$jamp_meta = $this->current_note_meta;
				require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/jamp-admin-column.php';

			}
		}

	}

	/**
	 * Shows the column content in the Users page.
	 *
	 * @since    1.3.0
	 * @param    string $output      Custom column output.
	 * @param    string $column_name Current table column name.
	 * @param    int    $user_id     Current user ID.
	 */
	public function manage_users_columns_content( $output, $column_name, $user_id ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Load the file if the column name contains the word 'jamp'.
			if ( strpos( $column_name, 'jamp' ) !== false ) {

				$column_content = '';

				require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/jamp-admin-column-users.php';

				return $column_content;

			}
		}

	}

	/**
	 * Generates current page url.
	 *
	 * @since    1.0.0
	 * @param    boolean $encode Set to true to replace some characters in the url.
	 */
	private static function get_current_page_url( $encode = false ) {

		$url = '';

		$request_scheme = ( isset( $_SERVER['REQUEST_SCHEME'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_SCHEME'] ) ) : '';
		$http_host      = ( isset( $_SERVER['HTTP_HOST'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$script_name    = ( isset( $_SERVER['SCRIPT_NAME'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ) : '';
		$query_string   = ( isset( $_SERVER['QUERY_STRING'] ) ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';

		if ( ! empty( $request_scheme ) && ! empty( $http_host ) && ! empty( $script_name ) ) {
			$url .= $request_scheme . '://' . $http_host . $script_name;

			if ( ! empty( $query_string ) ) {
				$url .= '?' . $query_string;
			}
		}

		// Remove a parameter added after WordPress settings save.
		$url = remove_query_arg( 'settings-updated', $url );

		// If current url is the admin url, let's add "index.php" to it so it's equal to the "Home" link in the sidebar menu.
		if ( admin_url() === $url ) {
			$url .= 'index.php';
		}

		// Replace '&' with '|' to prevent parse errors on wp_parse_url.
		if ( $encode ) {
			$url = str_replace( '&', '|', $url );
		}

		return $url;

	}

	/**
	 * Checks if current section is supported by the plugin.
	 *
	 * @since    1.0.0
	 */
	private function is_section_supported() {

		$current_section_url = $this->get_current_page_url();

		foreach ( $this->sections_list as $section ) {

			if ( $section['url'] === $current_section_url ) {

				return true;

			}
		}

		return false;

	}

	/**
	 * Adds an item to the admin bar.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_bar_menu_item() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			global $wp_admin_bar;

			// Global notes.
			$global_notes_args  = array(
				'post_type'      => 'jamp_note',
				'posts_per_page' => -1,
				'meta_key'       => 'jamp_scope',
				'meta_compare'   => '=',
				'meta_value'     => 'global',
			);
			$global_notes       = get_posts( $global_notes_args );
			$global_notes_count = count( $global_notes );

			// Section notes.
			if ( $this->is_section_supported() ) {
				$section_notes_args  = array(
					'post_type'      => 'jamp_note',
					'posts_per_page' => -1,
					'meta_key'       => 'jamp_target',
					'meta_compare'   => '=',
					'meta_value'     => $this->get_current_page_url(),
				);
				$section_notes       = get_posts( $section_notes_args );
				$section_notes_count = count( $section_notes );
			} else {
				$section_notes_count = 0;
			}

			// Main node.
			$main_node_html = '<span class="ab-icon"></span>' . esc_html__( 'Notes', 'jamp' );

			if ( ! empty( $global_notes_count ) ) {
				$main_node_html .= '<span class="notes-count global-notes-count" title="' . esc_attr__( 'Global Notes', 'jamp' ) . '">' . $global_notes_count . '</span>';
			}

			if ( ! empty( $section_notes_count ) ) {
				$main_node_html .= '<span class="notes-count section-notes-count" title="' . esc_attr__( 'Notes for this section', 'jamp' ) . '">' . $section_notes_count . '</span>';
			}

			$wp_admin_bar->add_node(
				array(
					'id'    => 'jamp',
					'title' => $main_node_html,
					'href'  => '#',
				)
			);

			// Content node.
			$wp_admin_bar->add_node(
				array(
					'id'     => 'jamp-content',
					'parent' => 'jamp',
					'title'  => require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/jamp-admin-admin-bar.php',
				)
			);

		}

	}

	/**
	 * Saves referer as return url when loading the new or edit post pages.
	 *
	 * @since    1.0.0
	 */
	public function note_form_page() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Extract parameters from querystring.
			$current_url = $this->get_current_page_url();
			$querystring = wp_parse_url( $current_url, PHP_URL_QUERY );

			if ( ! empty( $querystring ) ) {
				parse_str( $querystring, $params );
			}

			// Get post type.
			$post_type      = '';
			$current_screen = get_current_screen();

			if ( 'add' === $current_screen->action ) { // Creating a new note.

				if ( isset( $params['post_type'] ) ) {

					$post_type = $params['post_type'];

				}
			} else { // Editing a note.

				if ( isset( $params['post'] ) ) {

					$post      = get_post( $params['post'] );
					$post_type = $post->post_type;

				}
			}

			// Save referer in session if current post is a note.
			if ( 'jamp_note' === $post_type ) {

				$referer = wp_get_referer();
				$this->set_session( 'return_url', $referer );

			}
		}

	}

	/**
	 * Returns to the previous page after note save.
	 *
	 * @since    1.0.0
	 * @param    string $location Destination url.
	 */
	public function redirect_after_save( $location ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			global $post;

			// Perform redirection only for notes.
			if ( 'jamp_note' === $post->post_type ) {

				$return_url = $this->get_session( 'return_url' );

				if ( ! empty( $return_url ) ) {

					// Extract the "message" parameter value from querystring.
					$querystring = wp_parse_url( $location, PHP_URL_QUERY );
					parse_str( $querystring, $params );
					$message = $params['message'];

					// Save message id in session.
					$this->set_session( 'message_id', $message );

					return $return_url;

				}
			}
		}

		// Fallback to default location.
		return $location;

	}

	/**
	 * Shows custom notices in admin pages.
	 *
	 * @since    1.0.0
	 */
	public function show_admin_notices() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			$message_id = $this->get_session( 'message_id' );

			if ( ! empty( $message_id ) ) {

				// Set custom feedback messages.
				$messages['jamp_note'] = array(
					0  => '', // Unused. Messages start at index 1.
					1  => esc_html__( 'Note updated.', 'jamp' ),
					2  => esc_html__( 'Custom field updated.', 'jamp' ),
					3  => esc_html__( 'Custom field deleted.', 'jamp' ),
					4  => esc_html__( 'Note updated.', 'jamp' ),
					5  => '', // Unused. Revisions are disabled.
					6  => esc_html__( 'Note published.', 'jamp' ),
					7  => esc_html__( 'Note saved.', 'jamp' ),
					8  => esc_html__( 'Note submitted.', 'jamp' ),
					9  => esc_html__( 'Note scheduled.', 'jamp' ),
					10 => esc_html__( 'Note draft updated.', 'jamp' ),
				);

				?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo esc_html( $messages['jamp_note'][ $message_id ] ); ?></p>
					</div>
				<?php

				// Delete the whole session after the notice is displayed.
				$this->delete_session();

			}
		}

	}

	/**
	 * Adds custom notices for bulk actions.
	 *
	 * @since    1.0.0
	 * @param    array $bulk_messages Array of messages displayed in the notices.
	 * @param    array $bulk_counts   Array of item counts for each message.
	 */
	public function manage_default_bulk_notices( $bulk_messages, $bulk_counts ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			$bulk_messages['jamp_note'] = array(
				// translators: %s is the number of updated notes.
				'updated'   => esc_html( _n( '%s note updated.', '%s notes updated.', $bulk_counts['updated'], 'jamp' ) ),
				// translators: %s is the number of locked notes.
				'locked'    => esc_html( _n( '%s note not updated, somebody is editing it.', '%s notes not updated, somebody is editing them.', $bulk_counts['locked'], 'jamp' ) ),
				// translators: %s is the number of deleted notes.
				'deleted'   => esc_html( _n( '%s note permanently deleted.', '%s notes permanently deleted.', $bulk_counts['deleted'], 'jamp' ) ),
				// translators: %s is the number of trashed notes.
				'trashed'   => esc_html( _n( '%s note moved to the Trash.', '%s notes moved to the Trash.', $bulk_counts['trashed'], 'jamp' ) ),
				// translators: %s is the number of untrashed notes.
				'untrashed' => esc_html( _n( '%s note restored from the Trash.', '%s notes restored from the Trash.', $bulk_counts['untrashed'], 'jamp' ) ),
			);

		}

		return $bulk_messages;

	}

	/**
	 * Moves a note to trash.
	 * It's used by ajax calls.
	 *
	 * @since    1.0.0
	 */
	public function move_to_trash() {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Checks the nonce is valid.
			check_ajax_referer( $this->plugin_name );

			$note_id = ( isset( $_POST['note'] ) ) ? intval( wp_unslash( $_POST['note'] ) ) : 0;

			if ( ! empty( $note_id ) && current_user_can( 'delete_post', $note_id ) ) {

				$note = wp_trash_post( $note_id );

				if ( ! empty( $note ) ) {

					wp_send_json_success();

				} else {

					wp_send_json_error();

				}
			}
		} else {

			wp_send_json_error();

		}

	}

	/**
	 * Creates a TinyMCE custom configuration when editing notes.
	 *
	 * @since    1.0.0
	 * @param    array $mce_init An array with TinyMCE config.
	 */
	public function tiny_mce_before_init( $mce_init ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			if ( 'jamp_note' === $this->get_current_post_type() ) {

				unset( $mce_init['toolbar1'] );
				unset( $mce_init['toolbar2'] );
				unset( $mce_init['toolbar3'] );
				unset( $mce_init['toolbar4'] );

				$mce_init['wpautop']  = false;
				$mce_init['toolbar1'] = 'bold,italic,alignleft,aligncenter,alignright,link,strikethrough,hr,forecolor,pastetext,removeformat,charmap,undo,redo,wp_help';

			}
		}

		return $mce_init;

	}

	/**
	 * Adds the notes to the post types to be deleted when deleting a user.
	 *
	 * @since    1.0.0
	 * @param    array $post_types_to_delete Array of post types to delete.
	 * @param    int   $id                   User ID.
	 */
	public function post_types_to_delete_with_user( $post_types_to_delete, $id ) {

		if ( current_user_can( 'publish_jamp_notes' ) ) {

			$post_types_to_delete[] = 'jamp_note';

		}

		return $post_types_to_delete;

	}

	/**
	 * Gets current post type.
	 *
	 * @since    1.0.0
	 */
	private static function get_current_post_type() {

		global $post, $typenow, $current_screen;

		if ( $post && $post->post_type ) {
			return $post->post_type;
		} elseif ( $typenow ) {
			return $typenow;
		} elseif ( $current_screen && $current_screen->post_type ) {
			return $current_screen->post_type;
		} elseif ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}

		return null;

	}

	/**
	 * Gets a value from the plugin custom session.
	 *
	 * @since    1.0.0
	 * @param    string $key The name of the value.
	 */
	private static function get_session( $key ) {

		$transient = get_transient( self::get_transient_name() );

		if ( false === $transient ) {

			return null;

		} else {

			$value = null;

			if ( isset( $transient[ $key ] ) ) {
				$value = $transient[ $key ];
			}

			return $value;

		}

	}

	/**
	 * Saves a value to the plugin custom session.
	 *
	 * @since    1.0.0
	 * @param    string $key   The name of the value.
	 * @param    string $value The actual value to save.
	 */
	private static function set_session( $key, $value ) {

		$transient = get_transient( self::get_transient_name() );

		if ( false === $transient ) {
			$transient = array();
		}

		$transient[ $key ] = $value;

		set_transient( self::get_transient_name(), $transient, HOUR_IN_SECONDS );

	}

	/**
	 * Deletes the plugin custom session.
	 *
	 * @since    1.0.0
	 */
	private static function delete_session() {

		delete_transient( self::get_transient_name() );

	}

	/**
	 * Generates the name of the transient used by the plugin custom session.
	 *
	 * @since    1.0.0
	 */
	private static function get_transient_name() {

		$user = wp_get_current_user();
		return 'jamp_session_user_' . $user->ID;

	}

	/**
	 * Sets previous status for the untrashed notes and the new status
	 * for the other post types as per WordPress 5.6 default.
	 *
	 * @since    1.3.1
	 * @param    string $new_status      The new status of the post being restored.
	 * @param    int    $post_id         The ID of the post being restored.
	 * @param    string $previous_status The status of the post at the point where it was trashed.
	 * @return   string                  The new status of the post.
	 */
	public function untrash_notes_status( $new_status, $post_id, $previous_status ) {

		$current_post_type = $this->get_current_post_type();

		if ( 'jamp_note' === $current_post_type ) {
			return $previous_status;
		} else {
			return $new_status;
		}

	}

	/**
	 * Checks if a target type is enabled to have notes.
	 *
	 * @since    1.3.2
	 * @param    string $target_type The target type name.
	 */
	private static function is_target_type_enabled( $target_type ) {

		// If target type is missing.
		if ( empty( $target_type ) ) {
			return false;
		}

		// If passed target type is one of the default types.
		if ( in_array( $target_type, array( 'global', 'section' ), true ) ) {
			return true;
		}

		// If passed target type is one of the enabled types.
		if ( in_array( $target_type, get_option( 'jamp_enabled_target_types', array() ), true ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Before deleting a post with attached notes, save the post name in a note custom field.
	 *
	 * @since    1.3.2
	 * @param    int     $postid The post ID.
	 * @param    WP_Post $post The post object.
	 */
	public function before_delete_post( $postid, $post ) {

		// Skip post revisions.
		if ( 'revision' !== $post->post_type ) {
			// Look for notes attached to this post.
			$notes_args = array(
				'post_type'      => 'jamp_note',
				'posts_per_page' => -1,
				'meta_key'       => 'jamp_target',
				'meta_compare'   => '=',
				'meta_value'     => $post->ID,
			);
			$notes      = get_posts( $notes_args );

			if ( ! empty( $notes ) ) {
				foreach ( $notes as $note ) {
					update_post_meta( $note->ID, 'jamp_deleted_target_name', $post->post_title );
				}
			}
		}

	}

	/**
	 * Before deleting a user with attached notes, save the user name in a note custom field.
	 *
	 * @since    1.3.2
	 * @param    int      $id The user ID.
	 * @param    int|null $reassign ID of the user to reassign posts.
	 * @param    WP_User  $user The user object.
	 */
	public function delete_user( $id, $reassign, $user ) {

		// Look for notes attached to this user.
		$notes_args = array(
			'post_type'      => 'jamp_note',
			'posts_per_page' => -1,
			'meta_key'       => 'jamp_target',
			'meta_compare'   => '=',
			'meta_value'     => $id,
		);
		$notes      = get_posts( $notes_args );

		if ( ! empty( $notes ) ) {
			// Get user's display_name.
			$display_name = $user->data->display_name;

			foreach ( $notes as $note ) {
				update_post_meta( $note->ID, 'jamp_deleted_target_name', $display_name );
			}
		}

	}

	/**
	 * Before deleting a plugin with attached notes, save the plugin name in a note custom field.
	 *
	 * @since    1.3.2
	 * @param    string $plugin_file Path to the plugin file.
	 */
	public function delete_plugin( $plugin_file ) {

		// Look for notes attached to this user.
		$notes_args = array(
			'post_type'      => 'jamp_note',
			'posts_per_page' => -1,
			'meta_key'       => 'jamp_target',
			'meta_compare'   => '=',
			'meta_value'     => $plugin_file,
		);
		$notes      = get_posts( $notes_args );

		if ( ! empty( $notes ) ) {
			// Get plugin name.
			$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

			if ( file_exists( $plugin_path ) ) {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$plugin_data = get_plugin_data( $plugin_path, false, true );

				if ( ! empty( $plugin_data ) ) {
					foreach ( $notes as $note ) {
						update_post_meta( $note->ID, 'jamp_deleted_target_name', $plugin_data['Name'] );
					}
				}
			}
		}

	}
}

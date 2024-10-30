<?php
/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/includes
 * @author     Andrea Porotti
 */
class Jamp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Jamp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * List of plugin capabilities.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $capabilities    List of plugin capabilities.
	 */
	public static $capabilities = array(
		'read_private_jamp_notes',
		'edit_jamp_notes',
		'edit_others_jamp_notes',
		'edit_published_jamp_notes',
		'edit_private_jamp_notes',
		'publish_jamp_notes',
		'delete_jamp_notes',
		'delete_others_jamp_notes',
		'delete_published_jamp_notes',
		'delete_private_jamp_notes',
	);

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'JAMP_VERSION' ) ) {
			$this->version = JAMP_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		if ( defined( 'JAMP_PLUGIN_NAME' ) ) {
			$this->plugin_name = JAMP_PLUGIN_NAME;
		} else {
			$this->plugin_name = 'JAMP Notes';
		}

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register_custom_post_types();

	}

	/**
	 * Loads the required dependencies for this plugin.
	 *
	 * Includes the following files that make up the plugin:
	 *
	 * - Jamp_Loader. Orchestrates the hooks of the plugin.
	 * - Jamp_i18n. Defines internationalization functionality.
	 * - Jamp_Admin. Defines all hooks for the admin area.
	 * - Jamp_Public. Defines all hooks for the public side of the site.
	 *
	 * Creates an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jamp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jamp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jamp-admin.php';

		/**
		 * The class responsible for building the options page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jamp-options.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jamp-public.php';

		/**
		 * The class responsible for registering Custom Post Types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jamp-cpt.php';

		$this->loader = new Jamp_Loader();

	}

	/**
	 * Defines the locale for this plugin for internationalization.
	 *
	 * Uses the Jamp_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Jamp_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Registers all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $wp_version;

		$plugin_admin = new Jamp_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'adminmenu', $plugin_admin, 'build_sections_list' );

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_box' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_box_view_notes' );
		$this->loader->add_action( 'save_post_jamp_note', $plugin_admin, 'save_meta_data' );

		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'add_admin_bar_menu_item', 999999 );

		$this->loader->add_filter( 'tiny_mce_before_init', $plugin_admin, 'tiny_mce_before_init' );

		$this->loader->add_action( 'load-post-new.php', $plugin_admin, 'note_form_page' );
		$this->loader->add_action( 'load-post.php', $plugin_admin, 'note_form_page' );
		$this->loader->add_filter( 'redirect_post_location', $plugin_admin, 'redirect_after_save' );

		// Elements deletion.
		$this->loader->add_filter( 'post_types_to_delete_with_user', $plugin_admin, 'post_types_to_delete_with_user', 10, 2 );
		$this->loader->add_action( 'before_delete_post', $plugin_admin, 'before_delete_post', 10, 2 );
		$this->loader->add_action( 'delete_user', $plugin_admin, 'delete_user', 10, 3 );
		$this->loader->add_action( 'delete_plugin', $plugin_admin, 'delete_plugin' );

		// Hook to this filter starting from WordPress 5.6 to change the new untrash behaviour.
		if ( version_compare( $wp_version, '5.6-beta1-49262' ) >= 0 ) {
			$this->loader->add_filter( 'wp_untrash_post_status', $plugin_admin, 'untrash_notes_status', 10, 3 );
		}

		// Ajax.
		$this->loader->add_action( 'wp_ajax_build_targets_list', $plugin_admin, 'build_targets_list' );
		$this->loader->add_action( 'wp_ajax_move_to_trash', $plugin_admin, 'move_to_trash' );

		// Notices.
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_notices' );
		$this->loader->add_filter( 'bulk_post_updated_messages', $plugin_admin, 'manage_default_bulk_notices', 10, 2 );

		// Custom column.
		$this->loader->add_filter( 'manage_posts_columns', $plugin_admin, 'manage_columns_headers' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'manage_columns_content', 10, 2 );
		$this->loader->add_filter( 'manage_page_posts_columns', $plugin_admin, 'manage_columns_headers' );
		$this->loader->add_action( 'manage_page_posts_custom_column', $plugin_admin, 'manage_columns_content', 10, 2 );
		$this->loader->add_filter( 'manage_plugins_columns', $plugin_admin, 'manage_columns_headers' );
		$this->loader->add_action( 'manage_plugins_custom_column', $plugin_admin, 'manage_columns_content', 10, 2 );
		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'manage_columns_headers' );
		$this->loader->add_action( 'manage_users_custom_column', $plugin_admin, 'manage_users_columns_content', 10, 3 );

		// Plugin options.
		$plugin_options = new Jamp_Options();

		$this->loader->add_action( 'admin_menu', $plugin_options, 'options_menu' );
		$this->loader->add_action( 'admin_init', $plugin_options, 'options_init' );

	}

	/**
	 * Registers all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Jamp_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Registers all of the hooks related to the custom post types.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_custom_post_types() {

		$plugin_custom_post_types = new Jamp_CPT( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_custom_post_types, 'register' );

	}

	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Jamp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

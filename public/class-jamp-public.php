<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Jamp
 * @subpackage Jamp/public
 * @author     Andrea Porotti
 */
class Jamp_Public {

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
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
	 * Initializes the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name  The name of this plugin.
	 * @param    string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Registers the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// Load admin styles if current user can use the notes.
		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Use uncompressed file if debug is enabled (remove .min from filename).
			$min = ( WP_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'jamp-admin-style', plugin_dir_url( __FILE__ ) . '../admin/css/jamp-admin' . $min . '.css', array( 'wp-jquery-ui-dialog' ), $this->version, 'all' );

		}

	}

	/**
	 * Registers the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Load admin scripts if current user can use the notes.
		if ( current_user_can( 'publish_jamp_notes' ) ) {

			// Use uncompressed file if debug is enabled (remove .min from filename).
			$min = ( WP_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'jamp-admin-script', plugin_dir_url( __FILE__ ) . '../admin/js/jamp-admin' . $min . '.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, false );

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

}

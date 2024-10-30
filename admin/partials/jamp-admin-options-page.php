<?php
/**
 * Provides HTML code for the options page.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/admin/partials
 */

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="post">
	<?php
		// Output security fields for the settings page.
		settings_fields( 'jamp_options' );

		// Output settings sections and fields.
		do_settings_sections( 'jamp_options' );

		// Output save button.
		submit_button();
	?>
	</form>
</div>

<?php
/**
 * Provides HTML code for the meta box.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/admin/partials
 */

?>

<?php
// Add nonce field.
wp_nonce_field( 'jamp_meta_box_nonce_secret_action', 'jamp-meta-box-nonce' );

$screen           = get_current_screen();
$is_form_disabled = false;

// Build array for the meta box fields.
if ( 'add' === $screen->action ) { // Creating a new note.

	// Extract parameters from querystring.
	$current_url = self::get_current_page_url();
	$querystring = wp_parse_url( $current_url, PHP_URL_QUERY );

	if ( ! empty( $querystring ) ) {
		parse_str( $querystring, $params );
	}

	$jamp_meta                     = array();
	$jamp_meta['jamp_scope']       = array( ( isset( $params['jamp_scope'] ) ) ? $params['jamp_scope'] : '' );
	$jamp_meta['jamp_target_type'] = array( ( isset( $params['jamp_target_type'] ) ) ? $params['jamp_target_type'] : '' );
	$jamp_meta['jamp_target']      = array( ( isset( $params['jamp_target'] ) ) ? $params['jamp_target'] : '' );
	$jamp_meta['jamp_color']       = array( ( isset( $params['jamp_color'] ) ) ? $params['jamp_color'] : '' );

	// Replace '|' with '&' to re-build the correct url.
	if ( isset( $jamp_meta['jamp_target'][0] ) ) {
		$jamp_meta['jamp_target'][0] = str_replace( '|', '&', $jamp_meta['jamp_target'][0] );
	}

	// The form is disabled when creating an already configured note (if at least one setting exists).
	foreach ( $jamp_meta as $key => $value ) {
		if ( ! empty( $value[0] ) ) {
			$is_form_disabled = true;
			break;
		}
	}

	// At least the scope must be set. If it's empty or wrong, let's set it to "global".
	if ( ! in_array( $jamp_meta['jamp_scope'][0], array( 'global', 'section', 'entity' ), true ) ) {
		$jamp_meta['jamp_scope'] = array( 'global' );
	}

	// Set default color.
	if ( empty( $jamp_meta['jamp_color'][0] ) ) {
		$jamp_meta['jamp_color'] = array( 'yellow' );
	}
} else { // Editing a note.

	// Get post meta data.
	$jamp_meta = get_post_meta( $post->ID );

	// If this note's target type is not enabled.
	if ( ! self::is_target_type_enabled( $jamp_meta['jamp_target_type'][0] ) ) {
		// Get the label of this note's target type.
		$target_types_names = array_column( $args['args']['target_types_full'], 'name' );
		$target_type_id     = array_search( $jamp_meta['jamp_target_type'][0], $target_types_names, true );
		$target_type_label  = $args['args']['target_types_full'][ $target_type_id ]['label'];

		$message = sprintf(
			// translators: %s is the item type name.
			esc_html__( 'Please be aware that the Item Type of this note (%s) is not enabled in JAMP settings. Some of the above fields may be empty, but you can edit this note anyway.', 'jamp' ),
			esc_html( $target_type_label )
		);
	}

	// If editing a note without the color meta, set the default color.
	if ( empty( $jamp_meta['jamp_color'][0] ) ) {
		$jamp_meta['jamp_color'] = array( 'yellow' );
	}
}
?>

<input type="hidden" id="saved-target" value="<?php echo ( isset( $jamp_meta['jamp_target'] ) ) ? esc_attr( $jamp_meta['jamp_target'][0] ) : ''; ?>">

<?php // If form is disabled, create hidden fields in place of the actual fields. ?>
<?php if ( $is_form_disabled ) : ?>
	<input type="hidden" name="scope" value="<?php echo esc_attr( $jamp_meta['jamp_scope'][0] ); ?>">
	<input type="hidden" name="section" value="<?php echo esc_attr( $jamp_meta['jamp_target'][0] ); ?>">
	<input type="hidden" name="target-type" value="<?php echo esc_attr( $jamp_meta['jamp_target_type'][0] ); ?>">
	<input type="hidden" name="target" value="<?php echo esc_attr( $jamp_meta['jamp_target'][0] ); ?>">
<?php endif; ?>

<fieldset <?php disabled( $is_form_disabled, true ); ?>>
	<div class="meta-field meta-scope no-margin-top">
		<span><?php esc_html_e( 'Select the note location.', 'jamp' ); ?></span>
		<br>
		<label for="scope-global">
			<input type="radio" name="scope" id="scope-global" value="global" <?php ( isset( $jamp_meta['jamp_scope'] ) ) ? checked( $jamp_meta['jamp_scope'][0], 'global' ) : ''; ?>>
			<?php esc_html_e( 'Global', 'jamp' ); ?>
		</label>
		<br>
		<label for="scope-section">
			<input type="radio" name="scope" id="scope-section" value="section" <?php ( isset( $jamp_meta['jamp_scope'] ) ) ? checked( $jamp_meta['jamp_scope'][0], 'section' ) : ''; ?>>
			<?php esc_html_e( 'Section', 'jamp' ); ?>
		</label>
		<br>
		<label for="scope-entity">
			<input type="radio" name="scope" id="scope-entity" value="entity" <?php ( isset( $jamp_meta['jamp_scope'] ) ) ? checked( $jamp_meta['jamp_scope'][0], 'entity' ) : ''; ?>>
			<?php esc_html_e( 'Item', 'jamp' ); ?>
		</label>
	</div>

	<div class="meta-field meta-section">
		<label for="section" class="display-block"><?php esc_html_e( 'Select the Section.', 'jamp' ); ?></label>
		<select name="section" id="section">
			<option value=""><?php esc_html_e( 'select...', 'jamp' ); ?></option>

			<?php foreach ( $args['args']['sections'] as $section ) : ?>

				<option value="<?php echo esc_attr( $section['url'] ); ?>" <?php ( isset( $jamp_meta['jamp_target'] ) ) ? selected( $jamp_meta['jamp_target'][0], $section['url'] ) : ''; ?> <?php echo( ! $section['is_enabled'] ) ? 'disabled' : ''; ?>>
					<?php echo esc_html( $section['name'] ); ?>
				</option>

			<?php endforeach; ?>
		</select>
	</div>

	<div class="meta-field meta-target-type">
		<label for="target-type" class="display-block"><?php esc_html_e( 'Select the Item type.', 'jamp' ); ?></label>
		<select name="target-type" id="target-type">
			<option value=""><?php esc_html_e( 'select...', 'jamp' ); ?></option>

			<?php foreach ( $args['args']['target_types'] as $target_type ) : ?>

				<option value="<?php echo esc_attr( $target_type['name'] ); ?>" <?php ( isset( $jamp_meta['jamp_target_type'] ) ) ? selected( $jamp_meta['jamp_target_type'][0], $target_type['name'] ) : ''; ?>>
					<?php echo esc_html( $target_type['label'] ); ?>
				</option>

			<?php endforeach; ?>
		</select>
	</div>

	<div class="meta-field meta-target">
		<label for="target" class="display-block"><?php esc_html_e( 'Select the Item.', 'jamp' ); ?></label>
		<select name="target" id="target">
			<option value=""><?php esc_html_e( 'select...', 'jamp' ); ?></option>
		</select>
	</div>
</fieldset>

<div class="meta-field meta-color">
	<span><?php esc_html_e( 'Select the note color.', 'jamp' ); ?></span>
	<br>
	<label for="color-none">
		<input type="radio" name="color" id="color-none" value="" <?php ( isset( $jamp_meta['jamp_color'] ) ) ? checked( $jamp_meta['jamp_color'][0], '' ) : ''; ?>>
		<span class="jamp-color-preview" title="<?php esc_attr_e( 'Color: ', 'jamp' ); ?> <?php esc_attr_e( 'none', 'jamp' ); ?>"></span>
		(<?php esc_html_e( 'none', 'jamp' ); ?>)
	</label>
	<br>
	<label for="color-blue">
		<input type="radio" name="color" id="color-blue" value="blue" <?php ( isset( $jamp_meta['jamp_color'] ) ) ? checked( $jamp_meta['jamp_color'][0], 'blue' ) : ''; ?>>
		<span class="jamp-color-preview jamp-note--color-blue" title="<?php esc_attr_e( 'Color: ', 'jamp' ); ?> <?php esc_attr_e( 'blue', 'jamp' ); ?>"></span>
		<?php esc_html_e( 'blue', 'jamp' ); ?>
	</label>
	<br>
	<label for="color-green">
		<input type="radio" name="color" id="color-green" value="green" <?php ( isset( $jamp_meta['jamp_color'] ) ) ? checked( $jamp_meta['jamp_color'][0], 'green' ) : ''; ?>>
		<span class="jamp-color-preview jamp-note--color-green" title="<?php esc_attr_e( 'Color: ', 'jamp' ); ?> <?php esc_attr_e( 'green', 'jamp' ); ?>"></span>
		<?php esc_html_e( 'green', 'jamp' ); ?>
	</label>
	<br>
	<label for="color-purple">
		<input type="radio" name="color" id="color-purple" value="purple" <?php ( isset( $jamp_meta['jamp_color'] ) ) ? checked( $jamp_meta['jamp_color'][0], 'purple' ) : ''; ?>>
		<span class="jamp-color-preview jamp-note--color-purple" title="<?php esc_attr_e( 'Color: ', 'jamp' ); ?> <?php esc_attr_e( 'purple', 'jamp' ); ?>"></span>
		<?php esc_html_e( 'purple', 'jamp' ); ?>
	</label>
	<br>
	<label for="color-red">
		<input type="radio" name="color" id="color-red" value="red" <?php ( isset( $jamp_meta['jamp_color'] ) ) ? checked( $jamp_meta['jamp_color'][0], 'red' ) : ''; ?>>
		<span class="jamp-color-preview jamp-note--color-red" title="<?php esc_attr_e( 'Color: ', 'jamp' ); ?> <?php esc_attr_e( 'red', 'jamp' ); ?>"></span>
		<?php esc_html_e( 'red', 'jamp' ); ?>
	</label>
	<br>
	<label for="color-yellow">
		<input type="radio" name="color" id="color-yellow" value="yellow" <?php ( isset( $jamp_meta['jamp_color'] ) ) ? checked( $jamp_meta['jamp_color'][0], 'yellow' ) : ''; ?>>
		<span class="jamp-color-preview jamp-note--color-yellow" title="<?php esc_attr_e( 'Color: ', 'jamp' ); ?> <?php esc_attr_e( 'yellow', 'jamp' ); ?>"></span>
		<?php esc_html_e( 'yellow', 'jamp' ); ?>
	</label>
</div>

<?php if ( isset( $message ) && ! empty( $message ) ) : ?>
	<div class="meta-field meta-message">
		<strong><?php echo esc_html( $message ); ?></strong>
	</div>
<?php endif; ?>

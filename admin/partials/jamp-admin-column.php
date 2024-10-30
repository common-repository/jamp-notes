<?php
/**
 * Provides HTML code for the custom column added to the lists of items.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/admin/partials
 */

?>

<?php
// Columns on enabled target types pages.
if ( 'jamp_note' === $column_name ) {

	// Get the current target type.
	$target_type       = '';
	$current_post_type = $this->get_current_post_type();

	if ( ! empty( $current_post_type ) ) { // It's a post type admin page.
		$target_type = $current_post_type;
	} else { // It's another admin page.
		$screen      = get_current_screen();
		$target_type = $screen->id;
	}

	// Get notes.
	$notes_args = array(
		'post_type'      => 'jamp_note',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'   => 'jamp_target',
				'value' => $post_id,
			),
			array(
				'key'   => 'jamp_target_type',
				'value' => $target_type,
			),
		),
	);

	$notes = get_posts( $notes_args );

	if ( ! empty( $notes ) ) {

		// Define date/time format.
		$date_time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		// Get notes appearance.
		$column_notes_closed             = get_option( 'jamp_column_notes_closed' );
		$column_notes_closed_class       = ( $column_notes_closed ) ? 'jamp-column-note--close' : '';
		$column_notes_closed_arrow_class = ( $column_notes_closed ) ? 'rotate-180' : '';

		foreach ( $notes as $note ) {

			$note_author        = get_userdata( $note->post_author );
			$note_modified_date = date_i18n( $date_time_format, strtotime( $note->post_modified ) );
			$note_created_date  = date_i18n( $date_time_format, strtotime( $note->post_date ) );
			$note_title         = ( ! empty( $note->post_title ) ? $note->post_title : __( '(no title)' ) );

			$jamp_meta        = get_post_meta( $note->ID );
			$note_color_class = 'jamp-note--color-yellow';
			if ( isset( $jamp_meta['jamp_color'][0] ) && ! empty( $jamp_meta['jamp_color'][0] ) ) {
				$note_color_class = 'jamp-note--color-' . $jamp_meta['jamp_color'][0];
			}

			?>
			<div class="jamp-column-note <?php echo esc_attr( $note_color_class ); ?> <?php echo esc_attr( $column_notes_closed_class ); ?>" data-note="<?php echo esc_attr( $note->ID ); ?>">
				<button class="jamp-column-note__title">
					<span><?php echo esc_html( $note_title ); ?></span>
					<span class="jamp-column-note__arrow <?php echo esc_attr( $column_notes_closed_arrow_class ); ?>" aria-hidden="true">&#9650;</span>
					<span class="screen-reader-text"><?php echo esc_html__( 'Click to open or close the note', 'jamp' ); ?></span>
				</button>
				<div class="jamp-column-note__container">
					<div class="jamp-column-note__content"><?php echo wp_kses_post( $note->post_content ); ?></div>
					<div class="jamp-column-note__note-actions">
						<a href="#" class="jamp-column-note__note-info-action jamp-note-info-tooltip"><?php echo esc_html__( 'Info', 'jamp' ); ?>
						<span class="jamp-note-info-tooltip__content jamp-note-info-tooltip__content--top">
							<span class="jamp-note-info-tooltip__label"><?php echo esc_html__( 'Author', 'jamp' ); ?></span>
							<span class="jamp-note-info-tooltip__field"><?php echo esc_html( $note_author->display_name ); ?></span>
							<span class="jamp-note-info-tooltip__label"><?php echo esc_html__( 'Last edit', 'jamp' ); ?></span>
							<span class="jamp-note-info-tooltip__field"><?php echo esc_html( $note_modified_date ); ?></span>
							<span class="jamp-note-info-tooltip__label"><?php echo esc_html__( 'Created', 'jamp' ); ?></span>
							<span class="jamp-note-info-tooltip__field"><?php echo esc_html( $note_created_date ); ?></span>
						</span>
						</a> | 
						<a href="<?php echo esc_url( get_edit_post_link( $note->ID ) ); ?>"><?php echo esc_html__( 'Edit', 'jamp' ); ?></a> | 
						<a href="#" class="jamp-column-note__note-trash-action" data-note="<?php echo esc_attr( $note->ID ); ?>"><?php echo esc_html__( 'Trash', 'jamp' ); ?></a>
					</div>
				</div>
			</div>
			<?php

		}
	}

	// Add the placeholder to be shown when no notes are available.
	$css_class = ( ! empty( $notes ) ) ? 'jamp-column-note__no-notes-notice--hidden' : '';

	?>
	<span class="jamp-column-note__no-notes-notice <?php echo esc_attr( $css_class ); ?>">—</span>
	<?php

	// New note link.
	$create_url = add_query_arg(
		array(
			'post_type'        => 'jamp_note',
			'jamp_scope'       => 'entity',
			'jamp_target_type' => $target_type,
			'jamp_target'      => $post_id,
		),
		admin_url( 'post-new.php' )
	);

	?>
	<div class="jamp-column-note__generic-actions">
		<a href="<?php echo esc_url( $create_url ); ?>"><?php echo esc_html__( 'Add a note', 'jamp' ); ?></a>
	</div>
	<?php

}

// Columns on the notes post type page.
if ( 'jamp_color' === $column_name ) {

	if ( isset( $jamp_meta['jamp_color'] ) ) {
		echo '<span class="jamp-color-preview jamp-color-preview--big jamp-note--color-' . esc_attr( $jamp_meta['jamp_color'][0] ) . '" title="' . esc_attr__( 'Color: ', 'jamp' ) . ' ' . esc_attr__( 'blue', 'jamp' ) . '"></span>';
	} else {
		echo '<span class="jamp-color-preview jamp-color-preview--big" title="' . esc_attr__( 'Color: ', 'jamp' ) . ' ' . esc_attr__( 'none', 'jamp' ) . '"></span>';
	}
}

if ( 'jamp_author' === $column_name ) {

	$note        = get_post( $post_id );
	$note_author = get_userdata( $note->post_author );

	echo esc_html( $note_author->display_name );

}

if ( 'jamp_location' === $column_name ) {

	if ( ! empty( $jamp_meta['jamp_scope'] ) ) {

		switch ( $jamp_meta['jamp_scope'][0] ) {

			case 'global':
				echo '<strong>' . esc_html__( 'Global', 'jamp' ) . '</strong>';
				break;

			case 'section':
				// Look for the section url inside the sections list and print the corresponding name.
				$section_name        = '';
				$section_parent_name = '';

				foreach ( $this->sections_list as $section ) {

					if ( $section['url'] === $jamp_meta['jamp_target'][0] && $section['is_enabled'] ) {

						$section_name        = $section['name'];
						$section_parent_name = ( isset( $section['parent_name'] ) ) ? $section['parent_name'] : '';

					}
				}

				if ( ! empty( $section_name ) ) {

					echo '<strong>' . esc_html__( 'Section', 'jamp' ) . '</strong><br>| ' . esc_html( $section_parent_name ) . ' ' . esc_html( $section_name );

				} else {

					?>
					<span class="jamp-column-note__orphan-note-notice">
						<?php echo esc_html__( 'Note attached to a no longer existing Section.', 'jamp' ); ?>
					</span>
					<?php

				}

				break;

			case 'entity':
				// Look for the target type name inside the target types list and print the corresponding label.
				$target_type_name = esc_html__( 'Item', 'jamp' );

				if ( ! empty( $jamp_meta['jamp_target_type'][0] ) ) {
					foreach ( $this->target_types_list as $target_type ) {

						if ( $target_type['name'] === $jamp_meta['jamp_target_type'][0] ) {

							$target_type_name = $target_type['singular_name'];

						}
					}
				}

				// The current item exists.
				$current_item_exists = false;
				// The name or title of the current item.
				$current_item_name = '';

				// Check if the target type is a post type or a screen.
				if ( post_type_exists( $jamp_meta['jamp_target_type'][0] ) ) { // It's a post type.

					if ( ! empty( $jamp_meta['jamp_target'][0] ) ) {
						$current_item = get_post( $jamp_meta['jamp_target'][0] );

						if ( ! empty( $current_item ) ) {
							$current_item_exists = true;
							$current_item_name   = $current_item->post_title;
						}
					}
				} else { // It's a screen.

					// Plugins page.
					if ( 'plugins' === $jamp_meta['jamp_target_type'][0] ) {

						if ( ! function_exists( 'get_plugin_data' ) ) {
							require_once ABSPATH . 'wp-admin/includes/plugin.php';
						}

						// Get plugin data.
						$plugin_path = WP_PLUGIN_DIR . '/' . $jamp_meta['jamp_target'][0];

						if ( file_exists( $plugin_path ) ) {
							$plugin_data = get_plugin_data( $plugin_path, false, true );

							if ( ! empty( $plugin_data ) ) {
								$current_item_exists = true;
								$current_item_name   = $plugin_data['Name'];
							}
						}
					}

					// Users page.
					if ( 'users' === $jamp_meta['jamp_target_type'][0] ) {

						// Get user data.
						$note_user = get_userdata( $jamp_meta['jamp_target'][0] );

						if ( ! empty( $note_user ) ) {
							$current_item_exists = true;
							$current_item_name   = $note_user->data->display_name;
						}
					}
				}

				if ( $current_item_exists ) {

					if ( empty( $current_item_name ) ) {
						$current_item_name = __( '(no title)' );
					}
					echo '<strong>' . esc_html__( 'Item', 'jamp' ) . '</strong><br>| ' . esc_html( $target_type_name ) . ' "' . esc_html( $current_item_name ) . '"';

				} else {

					?>

					<span class="jamp-column-note__orphan-note-notice">
					<?php
						// translators: %s is the item type the note is attached to (eg. post, page...).
						printf( esc_html__( 'Note attached to a no longer existing %s.', 'jamp' ), esc_html( $target_type_name ) );
					?>
					</span>

					<?php
					// Show deleted item name, if any.
					if ( isset( $jamp_meta['jamp_deleted_target_name'][0] ) ) {
						?>
						<span>
							<br>
							<?php
								$deleted_target_name = $jamp_meta['jamp_deleted_target_name'][0];

								if ( empty( $deleted_target_name ) ) {
									$deleted_target_name = __( '(no title)' );
								}

								// translators: %s is the deleted item name.
								printf( esc_html__( 'Deleted item: %s.', 'jamp' ), esc_html( $deleted_target_name ) );
							?>
						</span>
						<?php
					}
					?>

					<?php

				}

				break;

			default:
				break;

		}
	}
}

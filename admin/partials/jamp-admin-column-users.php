<?php
/**
 * Provides HTML code for the custom column added to the Users page.
 *
 * @since      1.3.0
 * @package    Jamp
 * @subpackage Jamp/admin/partials
 */

?>

<?php
if ( 'jamp_note' === $column_name ) {

	// Get the current target type.
	$screen      = get_current_screen();
	$target_type = $screen->id;

	// Get notes.
	$notes_args = array(
		'post_type'      => 'jamp_note',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'   => 'jamp_target',
				'value' => $user_id,
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

			$column_content .= '<div class="jamp-column-note ' . esc_attr( $note_color_class ) . ' ' . esc_attr( $column_notes_closed_class ) . '" data-note="' . esc_attr( $note->ID ) . '">'
								. '<button class="jamp-column-note__title">'
									. '<span>' . esc_html( $note_title ) . '</span>'
									. '<span class="jamp-column-note__arrow ' . esc_attr( $column_notes_closed_arrow_class ) . '" aria-hidden="true">&#9650;</span>'
									. '<span class="screen-reader-text">' . esc_html__( 'Click to open or close the note', 'jamp' ) . '</span>'
								. '</button>'
								. '<div class="jamp-column-note__container">'
									. '<div class="jamp-column-note__content">' . wp_kses_post( $note->post_content ) . '</div>'
									. '<div class="jamp-column-note__note-actions">'
										. '<a href="#" class="jamp-column-note__note-info-action jamp-note-info-tooltip">' . esc_html__( 'Info', 'jamp' )
											. '<span class="jamp-note-info-tooltip__content jamp-note-info-tooltip__content--top">'
												. '<span class="jamp-note-info-tooltip__label">' . esc_html__( 'Author', 'jamp' ) . '</span>'
												. '<span class="jamp-note-info-tooltip__field">' . esc_html( $note_author->display_name ) . '</span>'
												. '<span class="jamp-note-info-tooltip__label">' . esc_html__( 'Last edit', 'jamp' ) . '</span>'
												. '<span class="jamp-note-info-tooltip__field">' . esc_html( $note_modified_date ) . '</span>'
												. '<span class="jamp-note-info-tooltip__label">' . esc_html__( 'Created', 'jamp' ) . '</span>'
												. '<span class="jamp-note-info-tooltip__field">' . esc_html( $note_created_date ) . '</span>'
											. '</span>'
										. '</a> | '
										. '<a href="' . esc_url( get_edit_post_link( $note->ID ) ) . '">' . esc_html__( 'Edit', 'jamp' ) . '</a> | '
										. '<a href="#" class="jamp-column-note__note-trash-action" data-note="' . esc_attr( $note->ID ) . '">' . esc_html__( 'Trash', 'jamp' ) . '</a>'
									. '</div>'
								. '</div>'
							. '</div>';

		}
	}

	// Add the placeholder to be shown when no notes are available.
	$css_class = ( ! empty( $notes ) ) ? 'jamp-column-note__no-notes-notice--hidden' : '';

	$column_content .= '<span class="jamp-column-note__no-notes-notice ' . esc_attr( $css_class ) . '">—</span>';

	// New note link.
	$create_url = add_query_arg(
		array(
			'post_type'        => 'jamp_note',
			'jamp_scope'       => 'entity',
			'jamp_target_type' => $target_type,
			'jamp_target'      => $user_id,
		),
		admin_url( 'post-new.php' )
	);

	$column_content .= '<div class="jamp-column-note__generic-actions">'
						. '<a href="' . esc_url( $create_url ) . '">' . esc_html__( 'Add a note', 'jamp' ) . '</a>'
					. '</div>';

}

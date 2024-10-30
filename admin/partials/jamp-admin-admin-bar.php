<?php
/**
 * Provides HTML code for the admin bar item.
 *
 * @since      1.0.0
 * @package    Jamp
 * @subpackage Jamp/admin/partials
 */

?>

<?php
// Define date/time format.
$date_time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

$html = '';

// Global notes.
$html .= '<div class="jamp-admin-bar-section">';

$create_url = add_query_arg(
	array(
		'post_type'  => 'jamp_note',
		'jamp_scope' => 'global',
	),
	admin_url( 'post-new.php' )
);

$html .= '<span class="jamp-admin-bar-section-title">' . esc_html__( 'Global Notes', 'jamp' ) . '</span> '
		. '(<a class="jamp-admin-bar-action jamp-admin-bar-action--create" href="' . esc_url( $create_url ) . '">' . esc_html__( 'New', 'jamp' ) . '</a>)';

if ( ! empty( $global_notes ) ) {

	foreach ( $global_notes as $note ) {

		$note_author        = get_userdata( $note->post_author );
		$note_modified_date = date_i18n( $date_time_format, strtotime( $note->post_modified ) );
		$note_created_date  = date_i18n( $date_time_format, strtotime( $note->post_date ) );
		$note_title         = ( ! empty( $note->post_title ) ? $note->post_title : __( '(no title)' ) );

		$jamp_meta        = get_post_meta( $note->ID );
		$note_color_class = 'jamp-note--color-yellow';
		if ( isset( $jamp_meta['jamp_color'][0] ) && ! empty( $jamp_meta['jamp_color'][0] ) ) {
			$note_color_class = 'jamp-note--color-' . $jamp_meta['jamp_color'][0];
		}

		$html .= '<div class="jamp-admin-bar-note ' . esc_attr( $note_color_class ) . '" data-note="' . esc_attr( $note->ID ) . '" data-scope="global">'
				. '<span class="jamp-admin-bar-note__title">' . esc_html( $note_title ) . '</span>'
				. '<span class="jamp-admin-bar-note__actions">'
				. '<a class="jamp-admin-bar-action jamp-admin-bar-action--info" href="#" title="' . esc_html__( 'Details', 'jamp' ) . '"></a>'
				. '<a class="jamp-admin-bar-action jamp-admin-bar-action--edit" href="' . esc_url( get_edit_post_link( $note->ID ) ) . '" title="' . esc_html__( 'Edit', 'jamp' ) . '"></a>'
				. '<a class="jamp-admin-bar-action jamp-admin-bar-action--trash" href="#" data-note="' . esc_attr( $note->ID ) . '" title="' . esc_html__( 'Move to Trash', 'jamp' ) . '"></a>'
				. '</span>'
				. '<div class="jamp-admin-bar-note__content">' . wp_kses_post( $note->post_content ) . '</div>'
				. '<div class="jamp-admin-bar-note__details">'
				. '<span class="jamp-admin-bar-note__detail"><span class="jamp-admin-bar-note__detail-label">' . esc_html__( 'Author', 'jamp' ) . ':</span> ' . esc_html( $note_author->display_name ) . '</span>'
				. '<span class="jamp-admin-bar-note__detail"><span class="jamp-admin-bar-note__detail-label">' . esc_html__( 'Last edit', 'jamp' ) . ':</span> ' . esc_html( $note_modified_date ) . '</span>'
				. '<span class="jamp-admin-bar-note__detail"><span class="jamp-admin-bar-note__detail-label">' . esc_html__( 'Created', 'jamp' ) . ':</span> ' . esc_html( $note_created_date ) . '</span>'
				. '</div>'
				. '</div>';

	}
}

// Add the placeholder to be shown when no notes are available.
$css_class = ( ! empty( $global_notes ) ) ? 'jamp-admin-bar-note__no-notes-notice--hidden' : '';
$html     .= '<span class="jamp-admin-bar-note__no-notes-notice ' . esc_attr( $css_class ) . '">' . esc_html__( 'No global notes.', 'jamp' ) . '</span>';

$html .= '</div>';

// Section notes.
if ( $this->is_section_supported() ) {

	$html .= '<div class="jamp-admin-bar-section">';

	$create_url = add_query_arg(
		array(
			'post_type'   => 'jamp_note',
			'jamp_scope'  => 'section',
			'jamp_target' => $this->get_current_page_url( true ),
		),
		admin_url( 'post-new.php' )
	);

	$html .= '<span class="jamp-admin-bar-section-title">' . esc_html__( 'Notes for this section', 'jamp' ) . '</span> '
			. '(<a class="jamp-admin-bar-action jamp-admin-bar-action--create" href="' . esc_url( $create_url ) . '">' . esc_html__( 'New', 'jamp' ) . '</a>)';

	if ( ! empty( $section_notes ) ) {

		foreach ( $section_notes as $note ) {

			$note_author        = get_userdata( $note->post_author );
			$note_modified_date = date_i18n( $date_time_format, strtotime( $note->post_modified ) );
			$note_created_date  = date_i18n( $date_time_format, strtotime( $note->post_date ) );

			$jamp_meta        = get_post_meta( $note->ID );
			$note_color_class = 'jamp-note--color-yellow';
			if ( isset( $jamp_meta['jamp_color'][0] ) && ! empty( $jamp_meta['jamp_color'][0] ) ) {
				$note_color_class = 'jamp-note--color-' . $jamp_meta['jamp_color'][0];
			}

			$html .= '<div class="jamp-admin-bar-note ' . esc_attr( $note_color_class ) . '" data-note="' . esc_attr( $note->ID ) . '" data-scope="section">'
					. '<span class="jamp-admin-bar-note__title">' . esc_html( $note->post_title ) . '</span>'
					. '<span class="jamp-admin-bar-note__actions">'
					. '<a class="jamp-admin-bar-action jamp-admin-bar-action--info" href="#" title="' . esc_html__( 'Details', 'jamp' ) . '"></a>'
					. '<a class="jamp-admin-bar-action jamp-admin-bar-action--edit" href="' . esc_url( get_edit_post_link( $note->ID ) ) . '" title="' . esc_html__( 'Edit', 'jamp' ) . '"></a>'
					. '<a class="jamp-admin-bar-action jamp-admin-bar-action--trash" href="#" data-note="' . esc_attr( $note->ID ) . '" title="' . esc_html__( 'Move to Trash', 'jamp' ) . '"></a>'
					. '</span>'
					. '<div class="jamp-admin-bar-note__content">' . wp_kses_post( $note->post_content ) . '</div>'
					. '<div class="jamp-admin-bar-note__details">'
					. '<span class="jamp-admin-bar-note__detail"><span class="jamp-admin-bar-note__detail-label">' . esc_html__( 'Author', 'jamp' ) . ':</span> ' . esc_html( $note_author->display_name ) . '</span>'
					. '<span class="jamp-admin-bar-note__detail"><span class="jamp-admin-bar-note__detail-label">' . esc_html__( 'Last edit', 'jamp' ) . ':</span> ' . esc_html( $note_modified_date ) . '</span>'
					. '<span class="jamp-admin-bar-note__detail"><span class="jamp-admin-bar-note__detail-label">' . esc_html__( 'Created', 'jamp' ) . ':</span> ' . esc_html( $note_created_date ) . '</span>'
					. '</div>'
					. '</div>';

		}
	}

	// Add the placeholder to be shown when no notes are available.
	$css_class = ( ! empty( $section_notes ) ) ? 'jamp-admin-bar-note__no-notes-notice--hidden' : '';
	$html     .= '<span class="jamp-admin-bar-note__no-notes-notice ' . esc_attr( $css_class ) . '">' . esc_html__( 'No notes for this section.', 'jamp' ) . '</span>';

	$html .= '</div>';

}

// Dialog to confirm all trash actions.
$html .= '<div class="jamp-trash-dialog jamp-trash-dialog--hidden" title="' . esc_html__( 'Move to Trash', 'jamp' ) . '">'
		. '<p>' . esc_html__( 'Do you want to move this note to Trash?', 'jamp' ) . '</p>'
		. '</div>';

return $html;

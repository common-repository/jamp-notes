<?php
/**
 * Provides HTML code for the view notes meta box.
 *
 * @since      1.1.0
 * @package    Jamp
 * @subpackage Jamp/admin/partials
 */

?>

<?php
// Get the current target type.
$target_type       = '';
$current_post_type = $args['args']['admin']->get_current_post_type();

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
			'value' => $post->ID,
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

	foreach ( $notes as $note ) {

		$note_author        = get_userdata( $note->post_author );
		$note_modified_date = date_i18n( $date_time_format, strtotime( $note->post_modified ) );
		$note_created_date  = date_i18n( $date_time_format, strtotime( $note->post_date ) );

		?>
		<div class="jamp-meta-box-note">
			<span class="jamp-meta-box-note__title"><?php echo esc_html( $note->post_title ); ?></span>
			<div class="jamp-meta-box-note__container">
				<div class="jamp-meta-box-note__content"><?php echo wp_kses_post( $note->post_content ); ?></div>
				<div class="jamp-meta-box-note__details">
					<span class="jamp-meta-box-note__detail-label"><?php echo esc_html__( 'Author', 'jamp' ); ?>:</span> <?php echo esc_html( $note_author->display_name ); ?>
					<span class="jamp-meta-box-note__detail-separator"></span>
					<span class="jamp-meta-box-note__detail-label"><?php echo esc_html__( 'Last edit', 'jamp' ); ?>:</span> <?php echo esc_html( $note_modified_date ); ?>
					<span class="jamp-meta-box-note__detail-separator"></span>
					<span class="jamp-meta-box-note__detail-label"><?php echo esc_html__( 'Created', 'jamp' ); ?>:</span> <?php echo esc_html( $note_created_date ); ?>
				</div>
			</div>
		</div>
		<?php

	}
} else {
	?>

	<span class="jamp-meta-box-note__no-notes-notice"><?php echo esc_html__( 'No notes for this item.', 'jamp' ); ?></span>	

	<?php
}

(function( $ ) {
	'use strict';

	$(function() {
		const jampMetaBox = $( '#jamp_meta_box' );
		const jampColumn  = $( 'th#jamp_note.column-jamp_note' );
		
		// Moves a note to trash.
		function moveToTrash( note, location ) {
			if ( note !== '' && note !== null ) {
				$.post( jamp_ajax.ajax_url, {
					_ajax_nonce: jamp_ajax.nonce,
					action: 'move_to_trash',
					note: note
				}, function( response ) {
					if ( response.success ) {
						if ( location === 'column' ) {
							removeTrashedNoteFromColumn( note );
						} else if ( location === 'adminbar' ) {
							removeTrashedNoteFromAdminBar( note );
						}
					} else {
						alert( jamp_strings.move_to_trash_error );
					}
				} );
			}
		}
		
		// Removes trashed note from a custom column.
		function removeTrashedNoteFromColumn( note ) {
			let selectedNote = $( '.jamp-column-note[data-note=' + note + ']' );

			// Gets number of notes in the current table cell.
			let cell = selectedNote.parent( 'td' );
			let existingNotes = cell.find( '.jamp-column-note' ).length;

			// Hides current note.
			selectedNote.addClass( 'jamp-note--deleting' ).fadeOut( 600, function() {
				// Removes hidden note.
				$( this ).remove();

				// Shows the placeholder if the row contains no more notes.
				if ( ( existingNotes - 1 ) === 0 ) {
					cell.find( '.jamp-column-note__no-notes-notice' ).removeClass( 'jamp-column-note__no-notes-notice--hidden' );
				}
			} );
		}
		
		// Removes trashed note from admin bar.
		function removeTrashedNoteFromAdminBar( note ) {
			let selectedNote = $( '.jamp-admin-bar-note[data-note=' + note + ']' );
			let selectedNoteScope = selectedNote.data( 'scope' );

			// Gets number of notes in the current admin bar section.
			let section = selectedNote.parent( '.jamp-admin-bar-section' );
			let existingNotes = section.find( '.jamp-admin-bar-note' ).length;

			// Hides current note.
			selectedNote.addClass( 'jamp-note--deleting' ).fadeOut( 600, function() {
				// Removes hidden note.
				$( this ).remove();

				// Shows the placeholder if the section contains no more notes.
				if ( ( existingNotes - 1 ) === 0 ) {
					section.find( '.jamp-admin-bar-note__no-notes-notice' ).removeClass( 'jamp-admin-bar-note__no-notes-notice--hidden' );
				}

				// Updates or removes notes count.
				let notesCount;
				
				if ( selectedNoteScope === 'global' ) {
					notesCount = $( '#wp-admin-bar-jamp .global-notes-count' );
				} else if ( selectedNoteScope === 'section' ) {
					notesCount = $( '#wp-admin-bar-jamp .section-notes-count' );
				}
				
				if ( ( existingNotes - 1 ) === 0 ) {
					notesCount.remove();
				} else {
					notesCount.text( existingNotes - 1 );
				}
			} );
		}

		// Meta box and edit screen features.
		if ( jampMetaBox.length > 0 ) {
			// Gets entities of the specified target type and insert them in the meta box items select.
			function getEntitiesList( targetType ) {
				if ( targetType !== '' && targetType !== null ) {
					$.post( jamp_ajax.ajax_url, {
						_ajax_nonce: jamp_ajax.nonce,
						action: 'build_targets_list',
						target_type: targetType
					}, function( response ) {
						if ( response.success ) {
							let options = '';

							$.each( response.data, function( key, entity ) {
								options += `<option class="target-entity" value="${entity.id}">${entity.title} (${entity.status})</option>`;
							} );

							$( '#jamp_meta_box #target option.target-entity' ).remove();
							$( '#jamp_meta_box #target' ).append( options );

							// Selects the option having the value from the database if it's present in the select.
							let savedTarget = $( '#jamp_meta_box #saved-target' ).val();

							if ( $( `#jamp_meta_box #target option[value="${savedTarget}"]` ).length > 0 ) {
								$( '#jamp_meta_box #target' ).val( savedTarget );
							} else {
								$( '#jamp_meta_box #target' ).val( '' );
							}
						} else {
							alert( jamp_strings.get_entities_list_error );
						}
					} );
				} else {
					$( '#jamp_meta_box #target option.target-entity' ).remove();
				}
			}

			// Hides and resets meta box fields skipping ignored elements.
			function hideFields( ignoredElements ) {
				if ( $.inArray('.meta-section', ignoredElements) < 0 ) {
					$( '#jamp_meta_box .meta-section' ).hide().find( 'option:first' ).attr( 'selected', 'selected' );
					$( '#jamp_meta_box #section' ).removeClass( 'form-field-invalid' );
				}

				if ( $.inArray('.meta-target-type', ignoredElements) < 0 ) {
					$( '#jamp_meta_box .meta-target-type' ).hide().find( 'option:first' ).attr( 'selected', 'selected' );
					$( '#jamp_meta_box #target-type' ).removeClass( 'form-field-invalid' );
				}

				if ( $.inArray('.meta-target', ignoredElements) < 0 ) {
					$( '#jamp_meta_box .meta-target' ).hide().find( 'option:first' ).attr( 'selected', 'selected' );
					$( '#jamp_meta_box #target' ).removeClass( 'form-field-invalid' );
				}
			}

			// Shows or hides meta box fields based on selected Scope.
			function setFieldsVisibility( scope ) {
				switch ( scope ) {
					case 'global':
						hideFields();
						break;
					case 'section':
						hideFields( [ '.meta-section' ] );
						$( '#jamp_meta_box .meta-section' ).show();
						break;
					case 'entity':
						hideFields( [ '.meta-target-type', '.meta-target' ] );
						$( '#jamp_meta_box .meta-target-type' ).show();
						$( '#jamp_meta_box #target-type' ).trigger( 'change' );
						$( '#jamp_meta_box .meta-target' ).show();
						break;
					default:
				}
			}

			// Fills meta box entities list on target type change.
			$( '#jamp_meta_box #target-type' ).on( 'change', function() {
				getEntitiesList( this.value );
			} );
	
			// Sets meta box fields visibility on Scope change.
			$( '#jamp_meta_box input[type=radio][name=scope]' ).on( 'change', function() {
				setFieldsVisibility( this.value );
			} );
	
			// Sets meta box fields visibility on page ready.
			setFieldsVisibility( $( '#jamp_meta_box input[type=radio][name=scope]:checked' ).val() );

			// Publish button on Note edit screen: validate settings.
			$( '.post-type-jamp_note #publish' ).on( 'click', function( e ) {
				let validate = false;
				let scope = $( '#jamp_meta_box input[type=radio][name=scope]:checked' );

				switch ( scope.val() ) {
					case 'global':
						validate = true;
						break;
					case 'section':
						let section = $( '#jamp_meta_box #section' );

						if ( section.val() !== '' && section.val() !== null ) {
							section.removeClass( 'form-field-invalid' );
							validate = true;
						} else {
							section.addClass( 'form-field-invalid' );
							validate = false;
						}

						break;
					case 'entity':
						let targetType = $( '#jamp_meta_box #target-type' );
						let target = $( '#jamp_meta_box #target' );

						if ( targetType.val() !== '' && targetType.val() !== null ) {
							targetType.removeClass( 'form-field-invalid' );
							validate = true;
						} else {
							targetType.addClass( 'form-field-invalid' );
							validate = false;
						}

						if ( target.val() !== '' && target.val() !== null ) {
							target.removeClass( 'form-field-invalid' );
							validate = true;
						} else {
							target.addClass( 'form-field-invalid' );
							validate = false;
						}

						break;
					default:
				}

				if ( validate === false ) {
					e.preventDefault();
				}
			} );
		}

		// Custom column features.
		if ( jampColumn.length > 0 ) {
			// Trash links on custom column.
			$( '.jamp-column-note__note-trash-action' ).on( 'click', function( e ) {
				e.preventDefault();
	
				let trashLink = $( this );
	
				$( '.jamp-trash-dialog' ).dialog( {
					modal: true,
					closeText: '',
					buttons: {
						'OK': function() {
							$( this ).dialog( 'close' );
							moveToTrash( trashLink.data( 'note' ), 'column' );
						}
					}
				} );
			} );
	
			// Info links on custom column.
			$( '.jamp-column-note__note-info-action' ).on( 'click', function( e ) {
				e.preventDefault();
			} );
	
			// Open or close a note on custom column.
			$( '.jamp-column-note__title' ).on( 'click', function( e ) {
				e.preventDefault();
	
				$(this).children( '.jamp-column-note__arrow' ).toggleClass( 'rotate-180' );
				$(this).next( '.jamp-column-note__container' ).slideToggle( 300 );
			} );
		}

		// Trash links on admin bar.
		$( '.jamp-admin-bar-action--trash' ).on( 'click', function( e ) {	
			e.preventDefault();

			let trashLink = $( this );

			$( '.jamp-trash-dialog' ).dialog( {
				modal: true,
				closeText: '',
				buttons: {
					'OK': function() {
						$( this ).dialog( 'close' );
						moveToTrash( trashLink.data( 'note' ), 'adminbar' );
					}
				}	
			} );	
		} );
		
		// Info links on admin bar.
		$( '.jamp-admin-bar-action--info' ).on( 'click', function( e ) {
			e.preventDefault();
			
			$( this ).parent().nextAll( '.jamp-admin-bar-note__details' ).slideToggle( 100 );
		} );
	});

})( jQuery );

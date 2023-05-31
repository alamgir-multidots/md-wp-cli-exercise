( function ( $ ) {
	const MdWpCliExerciseFrontendScripts = {
		init() {
			MdWpCliExerciseFrontendScripts.autoRefreshLocationsList();
			$( 'body' ).on(
				'change',
				'.multidots_locations_listing_refresh_action',
				function ( e ) {
					e.preventDefault();
					const form = $( this ).data( 'form-key' );
					MdWpCliExerciseFrontendScripts.refreshLocationsList( e, form );
				}
			);

			$( 'body' ).on(
				'click',
				'.multidots_add_feedback_action',
				function ( e ) {
					e.preventDefault();

					MdWpCliExerciseFrontendScripts.addUpdateFeeback( e );
				}
			);

			$( 'body' ).on(
				'click',
				'.md-location-list-paginaion',
				function ( e ) {
					e.preventDefault();
					const page = $( this ).data( 'page' );
					const form = $( this ).data( 'form-key' );
					MdWpCliExerciseFrontendScripts.refreshLocationsList(
						e,
						form,
						page
					);
				}
			);
		},

		/**
		 * Show general error
		 *
		 * @since x.x.x
		 */
		showGeneralError: () => {
			$( '#multidots-locations-render-search-result' ).html(
				md_wp_cli_exercise_ajax_object.general_error
			);
			MdWpCliExerciseFrontendScripts.afterAjaxAction();
		},

		/**
		 * Before ajax actions
		 *
		 * @since x.x.x
		 */
		beforeAjaxAction: ( form = '' ) => {
			$( 'body' ).css( 'cursor', 'progress' );
			$( '.md-locations-render-result-' . form ).html( '' );
			$( '.multidots-locations-render-search-location' ).append(
				'<div class="multidots-search-location-form-loading"></div>'
			);

			$( '.multidots-submitted-error-mgs' ).html('');
			$( '.multidots-submitted-data-mgs' ).html('');
			$( '.md-wp-cli-exercise-field' ).removeClass('md-required-field');

			$( '.md-locations-render-result-' . form ).append(
				'<div class="multidots-search-location-listing-loading"><div class="multidots-search-location-form-spinner"></div></div>'
			);
		},

		/**
		 * After ajax actions
		 *
		 * @since x.x.x
		 */
		afterAjaxAction: () => {
			$( 'body' ).css( 'cursor', '' );
			$( '.multidots-search-location-form-loading' ).remove();
			$( '.multidots-search-location-form-spinner' ).remove();
		},

		/**
		 * Add / Update feedback data
		 *
		 * @since x.x.x
		 *
		 * @param {Object} e Object current prevent default event.
		 */
		addUpdateFeeback: ( e ) => {
			e.preventDefault();
			let error_found = 0;
			$('.md-wp-cli-exercise-field').removeClass('md-required-field');

			if ( $( '.md-feedback-msg' ).val() == '' ) {
				$( '.multidots-submitted-error-mgs' ).html(md_wp_cli_exercise_ajax_object.required_error);
				$('.md-feedback-msg').addClass('md-required-field');
				error_found = 1;
			}

			if ( $( '.md-feedback-email' ).val() == '' ) {
				$( '.multidots-submitted-error-mgs' ).html(md_wp_cli_exercise_ajax_object.required_error);
				$('.md-feedback-email').addClass('md-required-field');
				error_found = 1;
			}

			if ( error_found > 0 ) {
				return;
			}

			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_wp_cli_exercise_ajax_object.ajax_url,
				data: {
					action: 'multidots_add_feedback_data',
					first_name: $( '.md-feedback-first-name' ).val(),
					last_name: $( '.md-feedback-last-name' ).val(),
					phone: $( '.md-feedback-phone' ).val(),
					email: $( '.md-feedback-email' ).val(),
					feedback: $( '.md-feedback-msg' ).val(),
					id: $( '.md-feedback-id' ).val(),
					_nonce: md_wp_cli_exercise_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					MdWpCliExerciseFrontendScripts.beforeAjaxAction();
				},
				success( response ) {
					$( '.multidots-submitted-data-mgs' ).html(
						response.content
					);

					MdWpCliExerciseFrontendScripts.afterAjaxAction();
					
					if ( $( '.md-feedback-id' ).val() == '' ) {
						$( '.md-feedback-first-name' ).val('');
						$( '.md-feedback-last-name' ).val('');
						$( '.md-feedback-phone' ).val('');
						$( '.md-feedback-email' ).val('');
						$( '.md-feedback-msg' ).val('');
					}
				},
				error() {
					MdWpCliExerciseFrontendScripts.showGeneralError();
				},
			} );
		},

		/**
		 * Refresh locations data
		 *
		 * @since x.x.x
		 *
		 * @param {Object}  e    Object current prevent default event.
		 * @param {Integer} page Integer current prevent default event.
		 */
		refreshLocationsList: ( e, form, page = 1 ) => {
			e.preventDefault();
			
			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_wp_cli_exercise_ajax_object.ajax_url,
				data: {
					action: 'md_wp_cli_exercise_refresh_listing',
					regions: $( '.multidots_location_search_region-' + form ).val(),
					types: $( '.multidots_location_search_type-' + form ).val(),
					form,
					page,
					_nonce: md_wp_cli_exercise_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					MdWpCliExerciseFrontendScripts.beforeAjaxAction( form );
				},
				success( response ) {
					$( '.md-locations-render-result-' + form ).html(
						response.content
					);

					MdWpCliExerciseFrontendScripts.afterAjaxAction();
				},
				error() {
					MdWpCliExerciseFrontendScripts.showGeneralError();
				},
			} );
		},

		/**
		 * Refresh locations data
		 *
		 * @since x.x.x
		 */
		autoRefreshLocationsList: () => {
			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_wp_cli_exercise_ajax_object.ajax_url,
				data: {
					action: 'md_wp_cli_exercise_refresh_listing',
					_nonce: md_wp_cli_exercise_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					MdWpCliExerciseFrontendScripts.beforeAjaxAction();
				},
				success( response ) {
					$( '.md-locations-render-onload-result' ).html(
						response.content
					);
					MdWpCliExerciseFrontendScripts.afterAjaxAction();
				},
				error() {
					MdWpCliExerciseFrontendScripts.showGeneralError();
				},
			} );
		},
	};

	$( function () {
		MdWpCliExerciseFrontendScripts.init();
		if ( $('.md-wp-cli-exercise-select2-field').length ) {
			$('.md-wp-cli-exercise-select2-field').select2({
				placeholder: $(this).data('placeholder'),
				allowClear: true
			});
		}
	} );
} )( jQuery );

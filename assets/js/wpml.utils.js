
$ = jQuery;

wpml = wpml || {};

	/**
	 * WPML filter for AJAX Request
	 * 
	 * @since    1.0.0
	 * 
	 * @param    string      Request type: GET, POST
	 * @param    object      Data object to pass
	 * @param    function    Function to run on success
	 * @param    function    Function to run on complete
	 */
	wpml.__ajax = function( data ) {
		var data = $.extend({
				url: ajaxurl
			},
			data
		);
		$.ajax( data );
	};

	/**
	 * WPML filter for AJAX GET Request
	 * 
	 * @since    1.0.0
	 * 
	 * @param    object      Data object to pass
	 */
	wpml._get = function( data ) {
		wpml.__ajax({
			type: 'GET',
			data: data.data || {},
			beforeSend: data.beforeSend || function() {},
			success: data.success || function() {},
			complete: data.complete || function() {},
			error: data.error || function() {}
		});
	};

	/**
	 * WPML filter for AJAX POST Request
	 * 
	 * @since    1.0.0
	 * 
	 * @param    object      Data object to pass
	 */
	wpml._post = function( data ) {
		wpml.__ajax({
			type: 'POST',
			data: data.data || {},
			beforeSend: data.beforeSend || function() {},
			success: data.success || function() {},
			complete: data.complete || function() {},
			error: data.error || function() {}
		});
	};

	/**
	 * Determine which data package the submitted field name belongs to.
	 * 
	 * @since    1.0.0
	 * 
	 * @param    string    Field name
	 * 
	 * @return   string    Data package name
	 */
	wpml.switch_data = function( f_name ) {

		switch ( f_name ) {
			case "poster":
			case "title":
			case "original_title":
			case "overview":
			case "production_companies":
			case "production_countries":
			case "spoken_languages":
			case "runtime":
			case "genres":
			case "release_date":
				var _data = 'meta';
				break;
			case "director":
			case "producer":
			case "photography":
			case "composer":
			case "author":
			case "writer":
			case "cast":
				var _data = 'crew';
				break;
			default:
				var _data = 'data';
				break;
		}

		return _data;
	};

	/**
	 * Status indicator
	 */
	wpml.state = wpml_state = {

		container: '#wpml_status',

		set: function() {},
		clear: function() {}
	};

		/**
		 * Update status
		 * 
		 * @since    1.0.0
		 * 
		 * @param    string    Status Message
		 * @param    string    Status type: error, update
		 */
		wpml.state.set = function( message, style ) {
			$(wpml_state.container).append( '<p>' + message + '</p>' ).removeClass().addClass( style ).show().focus();
			if ( 'error' == style )
				$('.spinner, .loading').removeClass('spinner loading');
		};

		/**
		 * Clear status
		 */
		wpml.state.clear = function() {
			$(wpml_state.container).empty().removeClass().hide();
		};

	/**
	 * Parse URL Query part to extract specific variables
	 * 
	 * @since    1.0.0
	 * 
	 * @param    string    URL Query part to parse
	 * @param    string    Wanted variable name
	 * 
	 * @return   string|boolean    Variable value if available, false else
	 */
	wpml.http_query_var = function( query, variable ) {

		var vars = query.split("&");
		for ( var i = 0; i <vars.length; i++ ) {
			var pair = vars[ i ].split("=");
			if ( pair[0] == variable )
				return pair[1];
		}
		return false;
	};

	/**
	 * Reinit WP_List_Table Checkboxes events. Events are messed up when
	 * using AJAX to reload tables' contents, so we need to override WordPress
	 * default jQuery handlers for Checkboxes click events.
	 * 
	 * @since    1.0.0
	 * 
	 * @param    object    Click Event Object
	 * 
	 * @return   boolean
	 */
	wpml.reinit_checkboxes_all = function( e, $input ) {

		var c = $input.prop('checked'),
			kbtoggle = 'undefined' == typeof toggleWithKeyboard ? false : toggleWithKeyboard,
			toggle = e.shiftKey || kbtoggle;

		$input.closest( 'table' ).children( 'tbody' ).filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.prop('checked', function() {
			if ( $input.is(':hidden') )
				return false;
			if ( toggle )
				return $input.prop( 'checked' );
			else if (c)
				return true;
			return false;
		});

		$input.closest('table').children('thead,  tfoot').filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.prop('checked', function() {
			if ( toggle )
				return false;
			else if (c)
				return true;
			return false;
		});

	};

	/**
	 * Reinit multiple checkboxes selection usin Shift+click.
	 * 
	 * Events are messed up when * using AJAX to reload tables' contents, so
	 * we need to override WordPress default jQuery handlers for Checkboxes
	 * click events.
	 * 
	 * @since    1.0.0
	 * 
	 * @param    object    Click Event Object
	 * 
	 * @return   boolean
	 */
	wpml.reinit_checkboxes = function( e, $input ) {

		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' );
			first = checks.index( lastClicked );
			last = checks.index( $input );
			checked = $input.prop('checked');
			if ( 0 < first && 0 < last && first != last ) {
				sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
				sliced.prop( 'checked', function() {
					if ( $input.closest('tr').is(':visible') )
						return checked;

					return false;
				});
			}
		}
		lastClicked = $input;

		// toggle "check all" checkboxes
		var unchecked = $input.closest('tbody').find(':checkbox').filter(':visible').not(':checked');
		$input.closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
			return ( 0 === unchecked.length );
		});

		return true;
	};

	/**
	 * Find current action's nonce value.
	 * 
	 * @since    1.0.0
	 * 
	 * @param    string    Action name
	 * 
	 * @return   boolean|string    Nonce value if available, false else.
	 */
	wpml.get_nonce = function( action ) {

		var nonce_name = '#_wpmlnonce_' + action.replace( /\-/g, '_' ),
		         nonce = null;

		if ( undefined != $( nonce_name ) )
			nonce = $( nonce_name ).val();

		return nonce;
	};

	/**
	 * Update current action's nonce value.
	 * 
	 * @since    1.0.0
	 * 
	 * @param    string    Action name
	 */
	wpml.update_nonce = function( action, nonce ) {

		var nonce_name = '#_wpmlnonce_' + action.replace( /\-/g, '_' );

		if ( undefined != $( nonce_name ) && undefined != nonce )
			$( nonce_name ).val( nonce );
	};

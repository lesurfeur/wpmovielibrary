<?php
/**
 * WPML TMDb API class
 * 
 * Heavily modified class based on Jonas De Smet "Glamorous" TMDb PHP API class.
 * The original class support almost all of the API functions, whereas this one
 * is stripped to the maximum and handles only what WPML needs, meaning the
 * connection to TheMovieDB and movie images, casts and meta.
 * 
 * @see API Documentation: http://docs.themoviedb.apiary.io/
 * 
 * @uses WordPress WP_Http Class instead of CURL like the original class.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'TMDb' ) ) :

	class TMDb
	{
		const API_VERSION    = '3';
		const API_URL        = '://api.themoviedb.org';
		const API_RELAY_URL  = '://tmdb.caercam.org/api';

		/**
		 * The TMDb-config
		 *
		 * @var object
		 */
		protected $_config;

		/**
		 * TMDb API Key
		 *
		 * @var string
		 */
		protected $_api_key = '';

		/**
		 * Dummy API?
		 *
		 * @var boolean
		 */
		protected $internal = false;

		/**
		 * Default constructor
		 *
		 * @param    string    $apikey API-key recieved from TMDb
		 * @param    string    $default Lang Default language (ISO 3166-1)
		 * @param    boolean   $config Load the TMDb-config
		 */
		public function __construct( $config = false, $dummy = false ) {

			if ( ! is_admin() )
				return false;

			if ( true === $config )
				self::getConfiguration();

			$this->_api_key = WPML_Settings::tmdb__apikey();
			$this->internal = WPML_Settings::tmdb__internal_api();

			if ( '' == $this->_api_key )
				$this->internal = true;
		}

		/**
		 * Check the submitted API Key is valid.
		 * 
		 * @param    string    $key API Key
		 * 
		 * @return   array|string     API Config if the key is valid, error message else
		 */
		public function checkApiKey( $key ) {

			$this->_api_key = esc_attr( $key );
			$this->internal = false;

			return self::_makeCall( 'configuration' );
		}

		/**
		 * Getter for the TMDB-config
		 *
		 * @return   array    TMDb result
		 */
		public function getConfig() {
			return ( ! is_null( $this->_config ) ? $this->_config : self::getConfiguration() );
		}

		/**
		 * Search a movie by querystring
		 *
		 * @param    string    $query Query to search after in the TMDb database
		 * @param    int       $page Number of the page with results (default first page)
		 * @param    bool      $adult Whether of not to include adult movies in the results (default false)
		 * @param    mixed     $year Filter the result with a year
		 * @param    mixed     $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result 
		 */
		public function searchMovie( $query, $page = 1, $adult = false, $year = null, $lang = null ) {

			$params = array(
				'query'         => $query,
				'page'          => (int) $page,
				'language'      => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang,
				'include_adult' => (bool) $adult,
				'year'          => $year,
			);

			return self::_makeCall( 'search/movie', $params );
		}

		/**
		 * Retrieve all basic information for a particular movie
		 *
		 * @param    mixed    $idTMDb-id or IMDB-id
		 * @param    mixed    $lang Filter the result with a language
		 * 
		 * @return   array    TMDb result 
		 */
		public function getMovie( $id, $lang = null ) {

			$params = array( 'language' => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang );
			return self::_makeCall( 'movie/' . $id, $params );
		}

		/**
		 * Retrieve all of the movie cast information for a particular movie
		 *
		 * @param    mixed    $id TMDb-id or IMDB-id
		 * 
		 * @return   array    TMDb result
		 */
		public function getMovieCast( $id ) {

			return self::_makeCall( 'movie/' . $id . '/casts' );
		}

		/**
		 * Retrieve all images for a particular movie
		 *
		 * @param    mixed    $id TMDb-id or IMDB-id
		 * @param    mixed    $lang Filter the result with a language
		 * 
		 * @return   array    TMDb result
		 */
		public function getMovieImages( $id, $lang = null ) {

			$params = array( 'language' => is_null( $lang ) ? WPML_Settings::tmdb__lang() : $lang );
			return self::_makeCall( 'movie/' . $id . '/images', $params );
		}

		/**
		 * Get configuration from TMDb
		 *
		 * @return   array    TMDb result
		 */
		public function getConfiguration() {

			$config = WPML_Cache::get( 'tmdb_api_config' );
			if ( ! $config ) {

				$config = $this->_makeCall( 'configuration' );

				if ( is_wp_error( $config ) ) {
					if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
						return $config;

					WPML_Utils::admin_notice( $config->get_error_message(), 'error' );
					return array();
				}

				if ( ! empty( $config ) && null == $this->_config )
					$this->_config = $config;

				WPML_Cache::set( 'tmdb_api_config', $config );
			}

			return $config;
		}

		/**
		 * Get Image URL
		 *
		 * @param    string    $filepath Filepath to image
		 * @param    const     $imagetype Image type
		 * @param    string    $size Valid size for the image
		 * 
		 * @return   string
		 */
		public function getImageUrl( $filepath, $imagetype, $size ) {

			$config = self::getConfig();

			$size_alias = array(
				'poster' => array( 'xx-small', 'x-small', 'small', 'medium', 'large', 'full', 'original' ),
				'backdrop' => array( 'small', 'medium', 'full', 'original' )
			);

			if ( isset( $config['images'] ) ) {

				$base_url = $config['images']['base_url'];
				$defaults = array(
						'poster' => array(
							'xx-small'  => $base_url . $config['images']['poster_sizes'][0],
							'x-small'   => $base_url . $config['images']['poster_sizes'][1],
							'small'     => $base_url . $config['images']['poster_sizes'][2],
							'medium'    => $base_url . $config['images']['poster_sizes'][3],
							'large'     => $base_url . $config['images']['poster_sizes'][4],
							'full'      => $base_url . $config['images']['poster_sizes'][5],
							'original'  => $base_url . $config['images']['poster_sizes'][6]
						),
						'backdrop' => array(
							'small'     => $base_url . $config['images']['backdrop_sizes'][0],
							'medium'    => $base_url . $config['images']['backdrop_sizes'][1],
							'full'      => $base_url . $config['images']['backdrop_sizes'][2],
							'original'  => $base_url . $config['images']['backdrop_sizes'][3]
						)
					);

				if ( is_null( $filepath ) && is_null( $imagetype ) && is_null( $size ) )
					return $defaults;
				else if ( is_null( $filepath ) && ! is_null( $imagetype ) && in_array( $imagetype, array( 'poster', 'backdrop' ) ) )
					return $defaults[ $imagetype ];

				$available_sizes = self::getAvailableImageSizes( $imagetype );
				$_size = (int) array_search( $size, $size_alias[ $imagetype ] );

				if ( isset( $config['images'][ $imagetype . '_sizes' ][ $_size ] ) )
					return $base_url . $config['images'][ $imagetype . '_sizes' ][ $_size ] . $filepath;
				else
					return new WP_Error( 'wrong_size', sprintf( __( 'The size "%s" is not supported by TMDb', 'wpmovielibrary' ), $size ) );
			}
			else
				return new WP_Error( 'no_config', __( 'No configuration available for image URL generation', 'wpmovielibrary' ) );
		}

		/**
		 * Get available image sizes for a particular image type
		 *
		 * @param    string    $imagetype Image type
		 * 
		 * @return   array|string     Available image sizes or error message
		 */
		public function getAvailableImageSizes( $imagetype ) {

			$config = self::getConfig();

			if ( isset( $config['images'][$imagetype.'_sizes'] ) )
				return $config['images'][$imagetype.'_sizes'];
			else
				return new WP_Error( 'no_config', __( 'API Error: no configuration available to retrieve available image sizes', 'wpmovielibrary' ) );
		}

		/**
		 * Makes the call to the API
		 *
		 * @param    string    $function API specific function name for in the URL
		 * @param    array     $params Unencoded parameters for in the URL
		 * @param    string    $session_id Session_id for authentication to the API for specific API methods
		 * 
		 * @return   array|string     TMDb result or error message
		 */
		private function _makeCall( $function, $params = null, $session_id = null, $method = 'get' ) {

			$params = ( ! is_array( $params ) ) ? array() : $params;
			$url = WPML_Settings::tmdb__scheme() . TMDb::API_URL . '/' . TMDb::API_VERSION . '/' . $function . '?' . http_build_query( array( 'api_key' => $this->_api_key ), '', '&' );
			$url .= ( ! is_null( $params ) && ! empty( $params ) ) ? '&' . http_build_query( $params, '', '&' ) : '';

			if ( true === $this->internal ) {
				$url = 'http' . TMDb::API_RELAY_URL . '/' . $function;
				if ( isset( $params['query'] ) && '' != $params['query'] )
					$url .= '/' . rawurlencode( $params['query'] );
				if ( isset( $params['language'] ) && false !== $params['language'] )
					$url .= '/' . $params['language'];
			}

			$results = array();
			$request  = new WP_Http;
			$headers  = array( 'Accept' => 'application/json' );
			$response = $request->request( $url, array( 'headers' => $headers ) );

			if ( is_wp_error( $response ) )
				return $response;

			if ( isset( $response['response']['code'] ) && 200 != $response['response']['code'] )
				return new WP_Error( 'connect_failed', sprintf( __( 'API Error: server connection to "%s" returned error %s: %s', 'wpmovielibrary' ), $url, $response['response']['code'], $response['response']['message'] ) );

			$header = $response['headers'];
			$body   = $response['body'];

			$results = json_decode( $body, true );

			// Using array_key_exists() instead of isset() to prevent weird bug in PHP 5.3
			if ( is_array( $body ) && array_key_exists( 'status_code', $body ) && array_key_exists( 'status_message', $body ) )
				return new WP_Error( 'connect_failed', sprintf( __( 'API Error: connection to TheMovieDB API failed with message "%s" (code %s)', 'wpmovielibrary' ), $body['status_code'], $body['status_message'] ) );

			if ( is_null( $results ) )
				return new WP_Error( 'unknown_error', __( 'API Error: unknown server error, unable to perform request.', 'wpmovielibrary' ) );

			return $results;
		}
	}

endif;
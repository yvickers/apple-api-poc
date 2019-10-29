<?php

class AppleSearchAPI{

	protected static $_instance = null;

	public $endpoint = 'http://itunes.apple.com/search';

	protected $limit = 25;

	function __construct(){

	}

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * load results from apple api
	 * @param  string $search  search term
	 * @param  array  $options optional key=>value pairs to pass to search api
	 * @return array          results
	 */
	function get_results( $search, $options = array() ){
		$ch = curl_init();

		$url = $this->endpoint;
		$options += array( 'term' => $search, 'limit' => $this->limit );
		$url .= '?'.http_build_query( $options );

		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$results = curl_exec($ch);
		curl_close($ch);

		$results = json_decode( $results, true );
		$results = $results['results'];
		usort( $results, array( $this, 'sort_alpha' ) );

		return $results;
	}

	/**
	 * sort based on trackname alphabetically
	 * @param  array $a first result
	 * @param  array $b second result
	 * @return int    direction to move in sort
	 */
	function sort_alpha( $a, $b ){
		return strcmp( $a["trackName"], $b["trackName"] );
	}

}

/**
 * search api singleton function
 */
function AppleSearchAPI(){
	return AppleSearchAPI::instance();
}
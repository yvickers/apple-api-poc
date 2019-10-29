<?php

/**
 * https://affiliate.itunes.apple.com/resources/documentation/itunes-store-web-service-search-api/
 */

class AppleSearchAPI{

	protected static $_instance = null;

	public $endpoint = 'http://itunes.apple.com/search';

	protected $limit = 25;

	/**
	 * caching recommended by api instructions
	 */
	protected $using_cache = true;
	protected $cache = '/cache/';
	protected $cache_time = 86400; //60 * 60 * 24 (1 day of caching)

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

		if( $this->using_cache() && ( $results = $this->get_cache_file( $url ) ) ){
			//using cached information
		}else{
			//pulling from live api
			curl_setopt($ch, CURLOPT_URL, $url );
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$results = curl_exec($ch);
			curl_close($ch);

			if( $this->using_cache() ){
				$this->store_cache_file( $url, $results );
			}
		}

		$results = json_decode( $results, true );
		$results = $results['results'];
		usort( $results, array( $this, 'sort_alpha' ) );

		return $results;
	}

	/**
	 * determine if we should save and use cached results
	 * @return bool true if using cache
	 */
	function using_cache(){
		return $this->using_cache;
	}

	/**
	 * get cache file corresponding to passed url
	 * @param  string $url api url used to get live results
	 * @return mixed      results or false
	 */
	function get_cache_file( $url ){
		$cache_file = $this->cache_filename( $url );

		//if file exists and information not too old, send cached information
		if( file_exists( $cache_file ) && ( $this->cache_time > ( strtotime('now') - filemtime( $cache_file ) ) ) ){
			return file_get_contents( $cache_file );
		}

		return false;
	}

	/**
	 * store information to cache
	 * @param  string $url     api url used to get live results
	 * @param  string $results json results from api
	 */
	function store_cache_file( $url, $results ){
		$handle = fopen( $this->cache_filename( $url ),  "w");
		if( false !== $handle ){
			fwrite( $handle, $results );
			fclose( $handle );
		}
	}

	/**
	 * single function to keep filenames consistent
	 * @param  string $url api url for live results
	 * @return string      directory + filename for cache file
	 */
	function cache_filename( $url ){
		return dirname(__FILE__) . $this->cache . md5( $url ) . '.txt';
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
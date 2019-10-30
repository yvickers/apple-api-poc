<?php

/**
 * https://affiliate.itunes.apple.com/resources/documentation/itunes-store-web-service-search-api/
 */

class AppleSearchAPI{

	protected static $_instance = null;

	public $endpoint = 'https://itunes.apple.com/search';

	protected $sort_method = 'sort_alpha';

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
		$options += array( 'term' => $search, 'media' => 'music', 'entity' => 'song' );
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
		usort( $results, array( $this, $this->sort_method) );

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
	 * change sorting method, default to alphabetic
	 */
	function set_sort( $sort ){
		switch( $sort ){
			case 'alphabetically':
			default:
				$this->sort_method = 'sort_alpha';
			break;

			case 'collection_price':
				$this->sort_method = 'sort_collection_price';
			break;

			case 'release_date':
				$this->sort_method = 'sort_release_date';
			break;
		}
	}

	/**
	 * sort based on trackname alphabetically
	 * in the case of missing trackname, sort toward the bottom
	 * @param  array $a first result
	 * @param  array $b second result
	 * @return int    direction to move in sort
	 */
	function sort_alpha( $a, $b ){

		if( !isset( $a["trackName"] ) ){
			return 1;
		}

		if( !isset( $b["trackName"] ) ){
			return -1;
		}

		$ret = strcmp( $a["trackName"], $b["trackName"] );

		return $ret;
	}

	/**
	 * sort items by their collection price - track price is more pertinent, but all seem to be the same
	 * note that some items do not have a collection price, these will sort to the bottom.
	 * @param  array $a first result
	 * @param  array $b second result
	 * @return int    direction to sort
	 */
	function sort_collection_price( $a, $b ){

		if( !isset( $a["collectionPrice"] ) ){
			return 1;
		}

		if( !isset( $b["collectionPrice"] ) ){
			return -1;
		}

		if ( $a["collectionPrice"] == $b["collectionPrice"] ) {
			return 0;
		}

		return ( $a["collectionPrice"] < $b["collectionPrice"] ) ? -1 : 1;
	}

	/**
	 * sort items by their release date
	 * items without a release date get sorted toward bottom
	 * @param  array $a first result
	 * @param  array $b second result
	 * @return int    direction to sort
	 */
	function sort_release_date( $a, $b ){

		if( !isset( $a["releaseDate"] ) ){
			return 1;
		}

		if( !isset( $b["releaseDate"] ) ){
			return -1;
		}

		$a_str_to_time = strtotime( $a["releaseDate"] );
		$b_str_to_time = strtotime( $b["releaseDate"] );

		if ( $a_str_to_time == $b_str_to_time ) {
			return 0;
		}

		return ( $a_str_to_time < $b_str_to_time ) ? -1 : 1;
	}

}

/**
 * search api singleton function
 */
function AppleSearchAPI(){
	return AppleSearchAPI::instance();
}
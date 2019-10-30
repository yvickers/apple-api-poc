<?php
	include( 'inc/api.php' );

	$results = array();
	$search_display = '';
	$sort = 'alphabetically';
	if( isset($_GET['sort']) && '' != $_GET['sort'] ){
		$sort = $_GET['sort'];
		AppleSearchAPI()->set_sort( $sort );
	}

	if( isset( $_GET['search'] ) && $_GET['search'] != '' ){
		$search_display = htmlspecialchars( $_GET['search'] );
		$results = AppleSearchAPI()->get_results( $_GET['search'], array( 'limit' => 25 )  );
	}

	$sort_options = array(
		 'alphabetically' => 'A to Z',
		 'collection_price' => 'Collection Price',
		 'release_date' => 'Release Date',
	);

?>
<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Apple Search API</title>
	<meta name="description" content="Demonstration of Apple search api">

	<link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">

</head>

<body>

	<div role="main">

		<div class="wrapper">
			<form>
				<div class="search-wrap">
					<label for="SearchTerms" class="sr-only">Search Terms</label>
					<input type="text" name="search" id="SearchTerms" value="<?php echo $search_display; ?>" value="<?php echo $search_display; ?>" placeholder="Search for some music..." aria-label="Search Terms">
					<button type="submit" title="Search"><span class="sr-only">Search</span></button>
				</div>
				<fieldset>
					<legend>Sort Order</legend>
					<div>
						<?php foreach( $sort_options as $value => $label ): ($sel = ( $value == $sort )? ' CHECKED':''); ?>
							<label><input type="radio" name="sort" value="<?php echo $value; ?>"<?php echo $sel;?>><?php echo $label; ?></label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</form>

			<?php if( $search_display != '' ): ?>
				<h1>Search Results for "<?php echo $search_display; ?>"</h1>
			<?php endif; ?>
		</div>

		<div class="wrapper grid">

			<?php foreach( $results as $result ): $result += array( 'trackName' => '', 'releaseDate' => '', 'collectionPrice' => '' ); ?>

				<a class="grid__item" href="<?php echo $result['previewUrl'] ?>" target="_blank" title="Play sample from <?php echo htmlspecialchars( $result['trackName'] ); ?>">
					<img src="<?php echo $result['artworkUrl100'] ?>" alt="" aria-hidden="true">
					<span class="grid__item__title"><?php echo $result['trackName'] ?></span>
					<span class="grid__item__artist"><?php echo $result['artistName'] ?></span>
					<!-- Release Date: <?php echo $result['releaseDate']; ?> -->
					<!-- Collection Price: <?php echo $result['collectionPrice']; ?> -->
				</a>

			<?php endforeach;?>

		</div>

	</div>

</body>
</html>
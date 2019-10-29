<?php
	include( 'inc/api.php' );

	$results = array();
	$search_display = '';

	if( isset( $_GET['search'] ) && $_GET['search'] != '' ){
		$search_display = htmlspecialchars( $_GET['search'] );
		$results = AppleSearchAPI()->get_results( $_GET['search']  );
	}

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
				<label for="SearchTerms" class="sr-only">Search Terms</label>
				<input type="text" name="search" id="SearchTerms" value="<?php echo $search_display; ?>" value="<?php echo $search_display; ?>" placeholder="Search for some music..." aria-label="Search Terms">
				<button type="submit" title="Search"><span class="sr-only">Search</span></button>
			</form>

			<?php if( $search_display != '' ): ?>
				<h1>Search Results for "<?php echo $search_display; ?>"</h1>
			<?php endif; ?>
		</div>

		<div class="wrapper grid">

			<?php foreach( $results as $result ): ?>

				<a class="grid__item" href="<?php echo $result['previewUrl'] ?>" target="_blank" title="Play sample from <?php echo htmlspecialchars( $result['trackName'] ); ?>">
					<img src="<?php echo $result['artworkUrl100'] ?>" alt="" aria-hidden="true">
					<span class="grid__item__title"><?php echo $result['trackName'] ?></span>
					<span class="grid__item__artist"><?php echo $result['artistName'] ?></span>
				</a>

			<?php endforeach;?>

		</div>

	</div>

</body>
</html>
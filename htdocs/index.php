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

	<link rel="stylesheet" href="/assets/css/style.css">

</head>

<body>

	<div class="wrapper">
		<form>
			<input type="text" name="search" value="<?php echo $search_display; ?>" value="<?php echo $search_display; ?>">
			<input type="submit" value="Search">
		</form>

		<?php if( $search_display != '' ): ?>
			Search Results for "<?php echo $search_display; ?>"
		<?php endif; ?>
	</div>

	<div class="wrapper grid">

		<?php foreach( $results as $result ): ?>

			<a class="grid__item" href="<?php echo $result['previewUrl'] ?>" target="_blank">
				<img src="<?php echo $result['artworkUrl100'] ?>" alt="">
				<span class="grid__item__title"><?php echo $result['trackName'] ?></span>
				<span class="grid__item__artist"><?php echo $result['artistName'] ?></span>
			</a>

		<?php endforeach;?>

	</div>

</body>
</html>
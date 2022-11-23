<?php require_once('top.php'); ?>
		<!-- START Main -->
		<main id="main">
			<div class="">
				<?php echo do_shortcode('[Waymark content="submission"]'); ?>
			</div>

			<h1><?php the_title(); ?></h1>
			
			<p><?php the_content(); ?></p>
		</main>
		<!-- END Main -->
<?php require_once('bottom.php'); ?>		
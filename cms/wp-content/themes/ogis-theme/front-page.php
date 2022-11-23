<?php require_once('top.php'); ?>
		<!-- START Main -->
		<main id="main">
			<div id="waymark">
				<?php echo do_shortcode('[Waymark content="submission"]'); ?>
			</div>
			<div id="words">
				<h1><?php the_title(); ?></h1>
			
				<?php the_content(); ?>
			</div>
		</main>
		<!-- END Main -->
<?php require_once('bottom.php'); ?>		
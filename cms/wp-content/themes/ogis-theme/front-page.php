<?php require_once('inc/top.php'); ?>
			<div id="waymark">
				<?php echo do_shortcode('[Waymark content="submission" map_hash="ogis_demo_add"]'); ?>
			</div>
			<article id="words">
				<h1><?php the_title(); ?></h1>
			
				<?php the_content(); ?>


				<?php require_once('inc/footer.php'); ?>
			</article>
<?php require_once('inc/bottom.php'); ?>		
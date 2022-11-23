<?php require_once('top.php'); ?>
			<div id="waymark">
				<?php echo do_shortcode('[Waymark content="submission" map_hash="ogis_demo_add"]'); ?>
			</div>
			<article id="words">
				<h1><?php the_title(); ?></h1>
			
				<?php the_content(); ?>


				<footer id="footer">v1.0</footer>
			</article>
<?php require_once('bottom.php'); ?>		
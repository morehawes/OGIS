<?php require_once('top.php'); ?>
			<div id="waymark">
				<?php echo do_shortcode('[Waymark map_id="' . get_the_ID() . '" map_hash="ogis_demo_view"]'); ?>
			</div>
			<article id="words">
				<h1><?php the_title(); ?></h1>
			
				<?php the_content(); ?>
				
				<hr />
			
				<a href="<?php echo get_home_url(); ?>">&lt; Home</a>

				<footer id="footer">v1.0</footer>				
			</article>
<?php require_once('bottom.php'); ?>		
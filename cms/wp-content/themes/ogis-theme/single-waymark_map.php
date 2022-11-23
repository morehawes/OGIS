<?php require_once('top.php'); ?>
		<!-- START Main -->
		<main id="main">
			<div id="waymark">
				<?php echo do_shortcode('[Waymark map_id="' . get_the_ID() . '" map_hash="ogis_demo_view"]'); ?>
			</div>
			<div id="words">
				<h1><?php the_title(); ?></h1>
			
				<?php the_content(); ?>
				
				<hr />
				
				<a href="<?php echo get_home_url(); ?>">&lt; Home</a>
			</div>
		</main>
		<!-- END Main -->
<?php require_once('bottom.php'); ?>		
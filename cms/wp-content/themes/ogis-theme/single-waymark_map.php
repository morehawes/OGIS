<?php require_once('inc/top.php'); ?>
			<div id="waymark">
				<?php echo do_shortcode('[Waymark map_id="' . get_the_ID() . '" map_hash="ogis_demo_view"]'); ?>
			</div>
			
			<article id="words">
				<header id="header">
					<div class="waymark-message waymark-success">Your submission has been published <a href="<?php echo get_permalink(get_the_ID()); ?>">here</a>: <input type="text" value="<?php echo get_permalink(get_the_ID()); ?>" /></div>
				</header>
				
				<h1><?php the_title(); ?></h1>
			
				<?php the_content(); ?>
				
				<?php require_once('inc/footer.php'); ?>				
			</article>
<?php require_once('inc/bottom.php'); ?>		
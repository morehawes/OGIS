<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width" />
		
		<title><?php wp_title('|', true, 'right'); ?><?php echo get_bloginfo( 'name', 'display' ); ?></title>
		
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		
		<!-- START Header -->
		<header id="header">

		</header>
		<!-- END Header -->		
		
		<!-- START Main -->
		<main id="main">
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
			<?php endwhile; else : ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
			<?php endif; ?>
		</main>
		<!-- END Main -->
	
		<!-- START Footer -->
		<footer id="footer">
		
		</footer>
		<!-- END Footer -->
		
		<?php wp_footer(); ?>
	</body>
</html>

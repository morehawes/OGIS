<?php 
$collection = get_queried_object();

get_header(); ?>

<article id="collection-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php echo do_shortcode('[' . Waymark_Config::get_item('shortcode') . ' map_hash="collection" shortcode_header="0" collection_id="' . $collection->term_id . '"]'); ?>
		
	<p class="lead text-center mb-5"><?php echo $collection->description; ?></p>
</article>

<?php map_first_archive_pagination(); ?>

<div class="row mb-4">
	<?php

/*

	//Content
	if(have_posts()) {
		//The Loop
		while(have_posts()) {
			the_post();
			get_template_part('template-parts/content/content', 'excerpt');
		}
	//No Content
	} else {
		get_template_part('template-parts/content/content', 'none');
	}

*/	
	
	?>
</div>

<?php map_first_archive_pagination(); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>

<div class="container page-base">
	<div class="row page-content single-update" id="<?php echo $post->post_name; ?>">
		<div class="span12">
			<article>
				<h1><?php the_title();?></h1>
				<div class="date"><?php echo get_post_meta($post->ID, 'update_date', True); ?></div>
				<?php echo display_social( get_permalink( $post->ID ), $post->post_title ); ?>
				<?php the_content();?>
			</article>
		</div>
	</div>
</div>

<?php get_template_part('includes/below-the-fold'); ?>

<?php get_footer();?>

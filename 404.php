<?php @header("HTTP/1.1 404 Not found", true, 404);?>
<?php disallow_direct_load('404.php');?>

<?php get_header(); the_post();?>
<article role="main" id="page-not-found">
	<main class="container page-content">
		<?php
		$page = get_page_by_title( '404' );
		$content = '';
		if ( $page && $page->post_status === 'publish' ) {
			$content = trim( apply_filters( 'the_content', $page->post_content ) );
		}
		?>
		<?php if ( $content ) : ?>
			<?php echo $content; ?>
		<?php else: ?>
			<div class="knightro-bg">
				<div class="row">
					<div class="span8">
						<h1 class="not-found-header">Page Not Found</h1>
						<p class="lead">Don't give in to despair, your quest continues here...</p>
						<p>Try double-checking the spelling of the address you requested, or search using the field below:</p>
						<form class="search-form mb-3" action="https://search.ucf.edu">
							<div class="input-group">
								<label class="sr-only" for="q-404">Search UCF</label>
								<input name="client" type="hidden" value="UCF_Main">
								<input name="proxystylesheet" type="hidden" value="UCF_Main">
								<span class="form-inline">
								<input id="q-404" class="search-field" name="q" type="text" placeholder="Tell us more about what you're looking for...">
									<button class="search-submit btn">Search</button>
								</span>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</main>
	<?php get_template_part('includes/below-the-fold'); ?>
</article>
<?php get_footer();?>

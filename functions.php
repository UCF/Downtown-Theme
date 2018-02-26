<?php
require_once('functions/base.php');   			# Base theme functions
require_once('functions/feeds.php');			# Where per theme settings are registered
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

//Add theme-specific functions here.

/**
 * Returns a theme option value or NULL if it doesn't exist
 **/
function get_theme_option($key) {
	global $theme_options;
	return isset($theme_options[$key]) ? $theme_options[$key] : NULL;
}


/**
 * Disable the standard wysiwyg editor for this theme to prevent markup from being blown away by
 * WYSIWYG users.
 **/
function disable_wysiwyg($c) {
    global $post_type;

    if ('page' == $post_type && get_theme_option('enable_page_wysiwyg') == 1) {
        return false;
    }
    return $c;
}
add_filter('user_can_richedit', 'disable_wysiwyg');


/**
 * Returns the url of the parallax feature's/page's featured image by the
 * size specified.
 *
 * @param int $feature_id    - post ID of the parallax feature or page with featured image
 * @param string $size       - image size registered with Wordpress to fetch the image by
 * @param string $cpt_field  - name (including prefix) of the meta field for the potential overridden image
 * @return string
 **/
function get_parallax_feature_img($post_id, $size, $cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);
	$thumb = null;
	$generated_thumb = wp_get_attachment_image_src($featured_img_id, $size);
	$custom_thumb = wp_get_attachment_url(get_post_meta($post_id, $cpt_field, true));

	$thumb = $custom_thumb ? $custom_thumb : $generated_thumb[0];
	return preg_replace('/^http(s)?\:/', '', $thumb);
}


/**
 * Output CSS necessary for responsive parallax features.
 *
 * @param int $post_id        - post ID of the parallax feature or page
 * @param string $d_cpt_field - name (including prefix) of the meta field for the potential overridden image for desktop browsers
 * @param string $t_cpt_field - name (including prefix) of the meta field for the potential overridden image for tablet browsers
 * @param string $m_cpt_field - name (including prefix) of the meta field for the potential overridden image for mobile browsers
 **/
function get_parallax_feature_css($post_id, $d_cpt_field, $t_cpt_field, $m_cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);

	$featured_img_f = wp_get_attachment_image_src($featured_img_id, 'parallax_feature-full');
	$featured_img_d = get_parallax_feature_img($post_id, 'parallax_feature-desktop', $d_cpt_field);
	$featured_img_t = get_parallax_feature_img($post_id, 'parallax_feature-tablet', $t_cpt_field);
	$featured_img_m = get_parallax_feature_img($post_id, 'parallax_feature-mobile', $m_cpt_field);
	if ($featured_img_f) { $featured_img_f = preg_replace('/^http(s)?\:/', '', $featured_img_f[0]); }

	ob_start();
?>
	<style type="text/css">
		<?php if ($featured_img_f) { ?>
		@media all and (min-width: 1200px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_f?>'); } }
		<?php } ?>
		<?php if ($featured_img_d) { ?>
		@media all and (max-width: 1199px) and (min-width: 768px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_d?>'); } }
		<?php } ?>
		<?php if ($featured_img_t) { ?>
		@media all and (max-width: 767px) and (min-width: 481px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_t?>'); } }
		<?php } ?>
		<?php if ($featured_img_m) { ?>
		@media all and (max-width: 480px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_m?>'); } }
		<?php } ?>
	</style>
	<!--[if lt IE 9]>
	<style type="text/css">
		#photo_<?=$post_id?> { background-image: url('<?=$featured_img_d?>'); }
	</style>
	<![endif]-->
<?php
	return ob_get_clean();
}


/**
 * Display a subpage parallax header image.
 **/
function get_parallax_page_header($page_id) {
	$page = get_post($page_id);
	ob_start();
	echo get_parallax_feature_css($page_id, 'page_image_d', 'page_image_t', 'page_image_m');
	?>
	<section class="parallax-content parallax-header">
		<div class="parallax-photo" id="photo_<?php echo $page_id; ?>" data-stellar-background-ratio="0.5">
			<?php
				if (get_theme_option('enable_skyline')) :
			?>
			<div class="skyline"></div>
			<?php
				else:
			?>
			<div class="no-skyline"></div>
			<?php
				endif;
			?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}


/**
 * Displays a call to action link, using the page link provided in Theme Options.
 **/
function get_cta_link() {
	$link = get_permalink(get_post(get_theme_option('cta'))->ID);
	ob_start();
?>
	<a href="<?php echo $link; ?>"> <?php echo get_theme_option('cta_link_text'); ?></a>
<?php
	return ob_get_clean();
}

/**
 * Displays a call to action link, using the page link provided in Theme Options.
 **/
function get_cta_prefix() {
	$text = get_theme_option('cta_prefix');
	ob_start();
	echo $text;
	return ob_get_clean();
}


/**
 * Hide unused admin tools (Links, Comments, etc)
 **/
function hide_admin_links() {
	remove_menu_page('link-manager.php');
}
add_action('admin_menu', 'hide_admin_links');


/**
* Displays social buttons (Facebook, Twitter, G+) for a post.
* Accepts a post URL and title as arguments.
*
* @return string
* @author Jo Dickson
**/
function display_social($url, $title) {
    $tweet_title = urlencode($title);
    ob_start(); ?>
    <aside class="social">
        <a class="share-facebook" target="_blank" data-button-target="<?php echo $url; ?>" href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>" title="Like this story on Facebook">
            Like "<?php echo $title; ?>" on Facebook
        </a>
        <a class="share-twitter" target="_blank" data-button-target="<?php echo $url; ?>" href="https://twitter.com/intent/tweet?text=<?php echo $tweet_title; ?>&url=<?php echo $url; ?>" title="Tweet this story">
            Tweet "<?php echo $title; ?>" on Twitter
        </a>
        <a class="share-googleplus" target="_blank" data-button-target="<?php echo $url; ?>" href="https://plus.google.com/share?url=<?php echo $url; ?>" title="Share this story on Google+">
            Share "<?php echo $title; ?>" on Google+
        </a>
        <a class="share-linkedin" target="_blank" data-button-target="<?php echo $url; ?>" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $tweet_title; ?>" title="Share this story on Linkedin">
        	Share "<?php echo $title; ?>" on Linkedin
        </a>
        <a class="share-email" target="_blank" data-button-target="<?php echo $url; ?>" href="mailto:?subject=<?php echo $title; ?>&amp;body=Check out this article on www.ucf.edu.%0A%0A<?php echo $url; ?>" title="Share this article in an email">
        	Share "<?php echo $title; ?>" in an email
        </a>
    </aside>
    <?php
    return ob_get_clean();
}


/**
 * Allow json files to be uploaded to the media library.
 **/
function uploads_allow_json( $mimes ) {
	$mimes['json'] = 'application/json';
	return $mimes;
}
add_filter( 'upload_mimes', 'uploads_allow_json' );


/**
 * Modifies default fields in comments_form() output.
 **/
function modified_comment_form_fields( $fields ) {
	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}
	return $fields;
}
add_filter( 'comment_form_default_fields', 'modified_comment_form_fields' );


/**
 * Bare-bones logic for appending 'comment-success' GET param to the
 * comment form's redirect url, instead of linking to the
 * individual comment's anchor (which won't exist on the page if comment
 * moderation is enforced.)
 **/
function comment_confirmation_message( $location, $comment ) {
	$location_parts = explode( '#', $location );
	$location_noanchor = $location_parts[0];
	$location = strpos( $location_noanchor, '?' ) !== false ? $location_noanchor . '&comment-success' : $location_noanchor . '?comment-success';

	return $location;
}

add_filter( 'comment_post_redirect', 'comment_confirmation_message' );


/**
 * Displays a list of attachments as a Bootstrap slideshow.
 *
 * @since v1.1.0
 * @author Jo Dickson
 * @param string $gallery_id A unique identifier for the gallery (to use as the id attribute on the gallery's parent element)
 * @param array $attachments Array of attachment post objects
 * @param array $attr Array of [gallery] shortcode attributes
 * @return string
 */
function display_gallery_slideshow( $gallery_id, $attachments, $attr ) {
	ob_start();
?>
	<div class="carousel slide" id="<?php echo $gallery_id; ?>">
		<ol class="carousel-indicators">
			<?php
			$indicatorcount = 0;

			foreach ( $attachments as $attachment ):
				$css_class = '';
				if ( $indicatorcount == 1 ) {
					$css_class = 'active';
				}
			?>
				<li data-target="#<?php echo $gallery_id; ?>" data-slide-to="<?php echo $indicatorcount; ?>" class="<?php echo $css_class; ?>"></li>
			<?php
				$indicatorcount++;
			endforeach;
			?>
		</ol>
		<div class="carousel-inner" role="listbox">
			<?php
			$i = 0;

			// Begin counting slides to set the first one as the active class
			$slidecount = 1;
			foreach ( $attachments as $id => $attachment ):
				$link_url = trim( get_post_meta( $attachment->ID, '_media_link', true ) );
				$image    = wp_get_attachment_image( $attachment->ID, $attr['size'] );
				$excerpt  = wptexturize( trim( $attachment->post_excerpt ) );

				$css_class = 'item';
				if ( $slidecount == 1 ) {
					$css_class .= ' active';
				}

				// Add a link to the image if a link exists.
			?>
				<div class="<?php echo $css_class; ?>">
					<?php echo ( !empty( $link_url ) ? '<a href="' . $link_url . '">' : '' ); ?>
					<?php echo $image; ?>
					<?php echo ( !empty( $link_url ) ? '</a>' : '' ); ?>

					<?php if ( $excerpt ): ?>
					<div class="carousel-caption">
						<?php echo $excerpt; ?>
					</div>
					<?php endif; ?>
				</div>
			<?php
				$slidecount++;
			endforeach;
			?>
		</div>
		<a class="left carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="prev">
			<span class="icon-left fa fa-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="next">
		    <span class="icon-right fa fa-chevron-right" aria-hidden="true"></span>
		    <span class="sr-only">Next</span>
		</a>
	</div>

<?php
	return ob_get_clean();
}


/**
 * Displays a list of attachments as a list of clickable thumbnails.
 *
 * @since v1.1.0
 * @author Jo Dickson
 * @param string $gallery_id A unique identifier for the gallery (to use as the id attribute on the gallery's parent element)
 * @param array $attachments Array of attachment post objects
 * @param array $attr Array of [gallery] shortcode attributes
 * @return string
 */
function display_gallery_thumbnails( $gallery_id, $attachments, $attr ) {
	ob_start();
?>
TODO
<?php
	return ob_get_clean();
}

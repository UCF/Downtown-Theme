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
			<?php echo get_page_video_markup( $page_id ); ?>
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

function get_page_video_markup( $page_id ) {
	$video = null;

	$video_id = get_post_meta( $page_id, 'page_video', TRUE );

	$video = wp_get_attachment_url( $video_id ) ?: null;

	ob_start();
	if ( $video ) :
?>
	<video class="page-header-video" muted loop autoplay>
		<source src="<?php echo $video; ?>" type="video/mp4">
	</video>
	<button class="header-video-toggle btn play-enabled" type="button" data-toggle="button" aria-pressed="false" aria-label="Play or pause background videos">
		<span class="fa fa-pause header-video-pause" aria-hidden="true"></span>
		<span class="fa fa-play header-video-play" aria-hidden="true"></span>
	</button>
<?php
	endif;

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

if ( ! function_exists( 'format_caption_shortcode' ) ) {
	/**
	 * Overrides the caption shortcode
	 * @param string $output The passed in output
	 * @param array $attr The attribute array
	 * @param string $content The content string passed in the shortcode
	 * @return string The html output
	 */
	function format_caption_shortcode( $output, $attr, $content ) {
		$atts = shortcode_atts( array(
			'id'	  => '',
			'align'	  => '',
			'caption' => '',
			'class'   => '',
			'width'   => '',
		), $attr, 'caption' );

		if ( ! empty( $atts['id'] ) ) {
			$atts['id'] = 'id="' . esc_attr( sanitize_html_class( $atts['id'] ) ) . '" ';
		}

		$align = $atts['align'] ?: '';
		$class = trim( 'figure ' . $align . ' ' . $atts['class'] );
		$html5 = current_theme_supports( 'html5' );

		// Add 'figure-img' class to inner <img>
		if ( preg_match( '/<img [^>]+>/', $content, $matches ) !== false ) {
			if ( strpos( $matches[0], 'figure-img' ) === false ) {
				$image_filtered = str_replace( "class='", "class='figure-img ", str_replace( 'class="', 'class="figure-img ', $matches[0] ) );
				$content = str_replace( $matches[0], $image_filtered, $content );
			}
		}
		if ( $html5 ) {
			$html = '<figure ' . $atts['id'] . 'class="' . esc_attr( $class ) . '">'
			. do_shortcode( $content ) . '<figcaption class="figure-caption">' . $atts['caption'] . '</figcaption></figure>';
		} else {
			$html = '<div ' . $atts['id'] . 'class="' . esc_attr( $class ) . '">'
			. do_shortcode( $content ) . '<p class="figure-caption">' . $atts['caption'] . '</p></div>';
		}

		return $html;
	}

	add_filter( 'img_caption_shortcode', 'format_caption_shortcode', 10, 3 );
}

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
	if ( empty( $attachments ) || empty( $attr ) ) { return; }

	ob_start();
?>
	<div class="gallery gallery-slideshow carousel slide" id="<?php echo $gallery_id; ?>" data-interval="false">
		<ol class="carousel-indicators">
			<?php
			$indicatorcount = 0;

			foreach ( $attachments as $attachment ):
				$css_class = '';
				if ( $indicatorcount === 0 ) {
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
			// Begin counting slides to set the first one as the active class
			$slidecount = 1;
			foreach ( $attachments as $attachment ):
				$link_url = trim( get_post_meta( $attachment->ID, '_media_link', true ) );
				$image    = wp_get_attachment_image( $attachment->ID, $attr['size'] );
				$excerpt  = wptexturize( trim( $attachment->post_excerpt ) );

				$css_class = 'item';
				if ( $slidecount === 1 ) {
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
			<span class="fa fa-arrow-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="next">
		    <span class="fa fa-arrow-right" aria-hidden="true"></span>
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
	if ( empty( $attachments ) || empty( $attr ) ) { return; }

	ob_start();
?>
	<div id="<?php echo $gallery_id; ?>" class="gallery gallery-thumbnails">
		<div class="row">

		<?php
		$grid_total = 12;
		$cols = intval( $attr['columns'] );
		if ( !in_array( $cols, get_gallery_grid_options() ) ) {
			$cols = 4;  // force a sane default if one isn't provided
		}

		$span = 'span' . $grid_total / $cols;
		$count = 0;

		foreach ( $attachments as $attachment ):
			$excerpt = esc_attr( wptexturize( trim( $attachment->post_excerpt ) ) );
			$img_url_full = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$img_url_full = $img_url_full ? $img_url_full[0] : '';
		?>
			<?php if ( $count % $cols === 0 && $count > 0 ): ?>
			</div><div class="row">
			<?php endif; ?>

			<div class="<?php echo $span; ?>">
				<a href="<?php echo $img_url_full; ?>" class="thumbnail" data-fancybox="<?php echo $gallery_id; ?>" data-caption="<?php echo $excerpt; ?>">
					<?php echo wp_get_attachment_image( $attachment->ID, $attr['size'] ); ?>
				</a>
			</div>
		<?php
		$count++;
		endforeach;
		?>

		</div>
	</div>
<?php
	return ob_get_clean();
}


/**
 * Returns an array of valid Bootstrap grid .span class values for use in
 * gallery layouts.
 *
 * @since v1.1.0
 * @author Jo Dickson
 * @return array
 */
function get_gallery_grid_options() {
	return array( 1, 2, 3, 4, 6 );
}


/**
 * Replaces gallery settings in the media library modal with our own.
 * Based on https://wordpress.stackexchange.com/a/209923
 *
 * @since v1.1.0
 * @author Jo Dickson
 * @return void
 */
function custom_gallery_settings() {
?>
<script type="text/html" id="tmpl-custom-gallery-settings">
	<h2><?php _e( 'Gallery Settings' ); ?></h2>

	<label class="setting">
		<span><?php _e('Layout'); ?></span>
		<select data-setting="layout">
			<option value="thumbnail">Thumbnails (default)</option>
			<option value="slideshow">Slideshow</option>
		</select>
	</label>

	<label class="setting">
		<span><?php _e('Columns'); ?></span>
		<select class="columns" name="columns" data-setting="columns">
			<?php
			$col_options = get_gallery_grid_options();
			foreach ( $col_options as $i ) :
			?>
				<option value="<?php echo esc_attr( $i ); ?>" <#
					if ( <?php echo $i; ?> == wp.media.galleryDefaults.columns ) { #>selected="selected"<# }
				#>>
					<?php echo esc_html( $i ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</label>

	<label class="setting size">
		<span><?php _e( 'Size' ); ?></span>
		<select class="size" name="size"
			data-setting="size"
			<# if ( data.userSettings ) { #>
				data-user-setting="imgsize"
			<# } #>
			>
			<?php
			/** This filter is documented in wp-admin/includes/media.php */
			$size_names = apply_filters( 'image_size_names_choose', array(
				'full'      => __( 'Full Size' ),
				'large'     => __( 'Large' ),
				'medium'    => __( 'Medium' ),
				'thumbnail' => __( 'Thumbnail' ),
			) );

			foreach ( $size_names as $size => $label ) : ?>
				<option value="<?php echo esc_attr( $size ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</label>
</script>

<script type="text/javascript">
	jQuery( document ).ready( function() {
		_.extend( wp.media.galleryDefaults, {
			layout: 'thumbnail',
			columns: 4,
			size: 'full'
		} );

		wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend( {
			template: function( view ) {
				return wp.media.template( 'custom-gallery-settings' )( view );
			}
		} );
	} );
</script>
<?php
}

add_action( 'print_media_templates', 'custom_gallery_settings' );


/**
 * Returns a brand link for use in primary site navigation.
 *
 * @since v1.1.0
 * @author Jo Dickson
 * @return string
 */
function get_home_link() {
	ob_start();
?>
<a class="home-link" href="<?php echo home_url(); ?>"><span class="gold-text">UCF</span> Downtown</a>
<?php
	return ob_get_clean();
}

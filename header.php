<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?="\n".header_()."\n"?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!--[if IE]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<?php if(GA_ACCOUNT or CB_UID):?>

		<script type="text/javascript">
			var _sf_startpt = (new Date()).getTime();
			<?php if(GA_ACCOUNT):?>

			var GA_ACCOUNT  = '<?=GA_ACCOUNT?>';
			var _gaq        = _gaq || [];
			_gaq.push(['_setAccount', GA_ACCOUNT]);
			_gaq.push(['_setDomainName', 'none']);
			_gaq.push(['_setAllowLinker', true]);
			_gaq.push(['_trackPageview']);
			<?php endif;?>
			<?php if(CB_UID):?>

			var CB_UID      = '<?=CB_UID?>';
			var CB_DOMAIN   = '<?=CB_DOMAIN?>';
			<?php endif?>

		</script>
		<?php endif;?>

		<?php $post_type = get_post_type($post->ID);
			if(($stylesheet_id = get_post_meta($post->ID, $post_type.'_stylesheet', True)) !== False
				&& ($stylesheet_url = wp_get_attachment_url($stylesheet_id)) !== False) : ?>
				<link rel='stylesheet' href="<?=$stylesheet_url?>" type='text/css' media='all' />
		<?php endif; ?>

		<script type="text/javascript">
			var PostTypeSearchDataManager = {
				'searches' : [],
				'register' : function(search) {
					this.searches.push(search);
				}
			}
			var PostTypeSearchData = function(column_count, column_width, data) {
				this.column_count = column_count;
				this.column_width = column_width;
				this.data         = data;
			}
		</script>

	</head>
	<body class="<?php echo body_classes(); ?>">
		<nav class="header-nav">
			<div class="container">
				<?php echo get_home_link(); ?>
				<a class="mobile-nav-toggle" href="#"><div class="hamburger"></div>Menu</a>
				<?php wp_nav_menu( array(
					'theme_location' => 'nav-menu',
					'container' => false,
					'menu_class' => 'menu '.get_header_styles(),
					'menu_id' => 'header-menu',
					'depth' => 1
					) );
				?>
			</div>
		</nav>

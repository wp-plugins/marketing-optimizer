<!doctype html>
<html class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php bloginfo('name'); ?> <?php wp_title(' - ', true, 'left'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel='stylesheet' id='wpbs-style-css' href='<?php echo plugins_url(); ?>/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_ct_advertisement3/css/style.css?ver=1.0' type='text/css' media='all' />
<!-- media-queries.js (fallback) -->
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>			
<![endif]-->
<!-- html5.js -->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!-- wordpress head functions -->
<?php wp_head(); ?>
<!-- end of wordpress head -->
</head>
<body style="margin:0;">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php the_content(); ?>
<?php endwhile; ?>	
<?php endif; ?>
<?php wp_footer();?>
</body>
</html>
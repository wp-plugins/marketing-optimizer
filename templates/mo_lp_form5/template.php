<!doctype html>
<html class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php bloginfo('name'); ?> <?php wp_title(' - ', true, 'left'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- media-queries.js (fallback) -->
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>			
<![endif]-->
<!-- html5.js -->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel='stylesheet' id='wpbs-style-css' href='<?php echo plugins_url(); ?>/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_form5/css/style.css?ver=1.0' type='text/css' media='all' />
<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<!-- wordpress head functions -->
	<?php wp_head(); ?>
<!-- end of wordpress head -->
<script type='text/javascript' src='<?php echo plugins_url(); ?>/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_form4/js/scripts.js?ver=1.2'></script>
</head>
<body>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php the_content(); ?>
	<?php endwhile; ?>	
	<?php endif; ?>
	<?php wp_footer();?>
</body>
</html>
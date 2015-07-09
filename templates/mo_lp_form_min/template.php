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
<link rel='stylesheet' id='bootstrap-css' href='/wp-content/plugins/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_is/css/bootstrap.css?ver=1.0' type='text/css' media='all' />
<link rel='stylesheet' id='wpbs-style-css' href='/wp-content/plugins/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_is/css/style.css?ver=1.0' type='text/css' media='all' />
<!-- wordpress head functions -->
    <?php wp_head(); ?>
<!-- end of wordpress head -->
<script type='text/javascript' src='/wp-content/plugins/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_is/js/bootstrap.min.js?ver=1.2'></script>
<script type='text/javascript' src='/wp-content/plugins/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_is/js/scripts.js?ver=1.2'></script>
<script type='text/javascript' src='/wp-content/plugins/<?php echo mo_plugin::MO_DIRECTORY?>/templates/mo_lp_is/js/modernizr.full.min.js?ver=1.2'></script>
</head>
<body>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php the_content(); ?>
<?php endwhile; ?>	
<?php endif; ?>
<?php wp_footer();?>
<!-- ================================ End Template Editing ================================ -->
</body>
</html>
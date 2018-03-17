<?php

	/*
	*
	*	Header 4
	*	------------------------------------------------
	* 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
	*
	*	Output for header-4
	*
	*/
	
	global $sf_options;
	$fullwidth_header    = $sf_options['fullwidth_header'];
?>

<?php if ( $fullwidth_header ) { ?>
<header id="header" class="sticky-header fw-header clearfix">
<?php } else { ?>
<header id="header" class="sticky-header clearfix">
<?php } ?>
	<div class="container"> 
		<div class="row"> 
			
			<?php echo sf_logo( 'col-sm-4 logo-left' ); ?>
			
			<?php echo sf_main_menu( 'main-navigation', 'float-2' ); ?>
			
		</div> <!-- CLOSE .row --> 
	</div> <!-- CLOSE .container --> 
</header> 
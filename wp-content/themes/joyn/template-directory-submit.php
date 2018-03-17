<?php
	/*
		Template Name: Directory Submission
	*/
?>

<?php get_header(); ?>
	
<div class="container">

	<?php sf_base_layout('page'); ?>
	
	<?php do_action( 'sf_insert_directory'); ?>
	
</div>

<?php get_footer(); ?>
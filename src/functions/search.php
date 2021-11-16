<?php


if(!function_exists('alter_sfquery')){

	function alter_sfquery( $query_args, $sfid ) {
		
		if($sfid== 'id')
		{
			//modify $query_args here before returning it
			// $query_args['posts_per_page'] = 9;
		}
		
		return $query_args;
	}
	
	add_filter( 'sf_edit_query_args', 'alter_sfquery', 20, 2 );
}


add_filter('wpcf7_autop_or_not', '__return_false');



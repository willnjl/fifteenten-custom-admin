<?php


// Standard wk_kses arguments

if(!function_exists('textArgs')){
	function textArgs()
	{
		return [
			'h1' => [
				'class' => true
			],
			'h2' => [
				'class' => true
			],
			'h3' => [
				'class' => true
			],
			'h4' => [
				'class' => true
			],
			'p' => [
				'class' => true
			],
			'a' => [
				'href' => true,
				'class' => true
			],
			'br' => true,
			'i' => [
				'class' => true
			],

			'strong' => true,
			'b' => true,
		];
	}
}


if(!function_exists('postArgs')){
		
	function postArgs()
	{
		return [
			'h1' => [
				'class' => true
			],
			'h2' => [
				'class' => true
			],
			'h3' => [
				'class' => true
			],
			'h4' => [
				'class' => true
			],
			'p' => [
				'class' => true
			],
			'a' => [
				'href' => true,
				'class' => true
			],
			'br' => true,
			'i' => [
				'class' => true
			],

			'strong' => true,
			'b' => true,
		];
	}
}

if(!function_exists('acf_responsive_img')){

	function acf_responsive_img($image_id,$image_size,$max_width){

		// check the image ID is not blank
		if($image_id != '') {

			// set the default src image size
			$image_src = wp_get_attachment_image_url( $image_id, $image_size );

			// set the srcset with various image sizes
			$image_srcset = wp_get_attachment_image_srcset( $image_id, $image_size );

			// generate the markup for the responsive image
			echo 'src="'.$image_src.'" srcset="'.$image_srcset.'" sizes="(max-width: '.$max_width.') 100vw, '.$max_width.'" loading="lazy"';

		}
	}
}

if(!function_exists('outputMedia')){

	function outputMedia($media, $size = null, $width = null)
	{
		if($media['type'] == 'image') : ?>
		<img <?= acf_responsive_img($media['id'], $size, $width); ?> alt="<?= esc_attr(($media['alt']));?>"/>
		<? else : ?>
		<video src="<?= esc_url($media['url']); ?>" controls></video>
		<? endif; 
	}	
}

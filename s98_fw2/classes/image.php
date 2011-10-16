<?php
/**
 * Image class, has random functions for images
 *
 * Functions:
 * array( $width, $height ) proportions ( $width, $height, $width_constraint, $height_constraint ) - get's proper portions of an image
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class image extends Base_Class {
	/**
	 * Figures out the right proportion for an image
	 * 
	 * @since 1.0
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $width_contraint
	 * @param int $height_contraint
	 * @return array( $width, $height )
	 */
	public function proportions( $width, $height, $width_constraint, $height_constraint ) {
		if( $width <= $width_constraint && $height <= $height_constraint )
			return array( $width, $height );
		
		$width_factor =  $width_constraint / $width;
		$height_factor = $height_constraint / $height;
		
		// Find out which way we have to resize it
		if( $width_factor < $height_factor ) {
			$new_height = $width_factor * $height;
			$new_width = ( $width_constraint < $width ) ? $width_constraint : $width;
		} else {
			$new_width = $height_factor * $width;
			$new_height = ( $height_constraint < $height ) ? $height_constraint : $height;
		}
		
		
		return array( round( $new_width ), round( $new_height ) );
	}
	
	/**
	 * Resizes and image and moves to a certain location
	 * 
	 * Based on the work below:
	 * @author Bit Repository
	 * @url http://www.bitrepository.com/resize-an-image-keeping-its-aspect-ratio-using-php-and-gd.html
	 * 
	 * @uses format::slug
	 * 
	 * @param string $image_to_resize
	 * @param string $save_folder
	 * @param string $name (optional|) The name of the new image
	 * @param int $width_constraint (optional|100)
	 * @param int $height_constraint (optional|100)
	 * @param int $quality (optional|90) the quality to maintain in % (if jpeg)
	 * @param bool $keep_proportions (optional|true) whether to keep the proportions of the image
	 * @param bool $fill_constraints (optional|true) whether to fill the constraints
	 */
	public function resize( $image_to_resize, $save_folder, $name = '', $width_constraint = 100, $height_constraint = 100, $quality = 90, $keep_proportions = true, $fill_constraints = true ) {
		if( !file_exists( $image_to_resize ) )
			return false;
		
		$info = getimagesize( $image_to_resize );
		
		if( empty( $info ) )
			return false;
					
		$width = $info[0];
		$height = $info[1];
		$mime = $info['mime'];
		
		// Keep proportions
		// if( $keep_proportions ) {
		if( $fill_constraints ) {
			list( $new_width, $new_height ) = self::proportions( $width, $height, $width_constraint, $height_constraint );
		} else {
			$new_width = $width;
			$new_height = $height;
		}
		
		// What sort of image?
		$type = substr( strrchr( $mime, '/' ), 1 );
		
		switch ( $type ) {
			case 'jpeg':
				$image_create_func = 'ImageCreateFromJPEG';
				$image_save_func = 'ImageJPEG';
				$new_image_ext = 'jpg';
			break;
			
			case 'png':
				$image_create_func = 'ImageCreateFromPNG';
				$image_save_func = 'ImagePNG';
				$new_image_ext = 'png';
			break;
			
			case 'bmp':
				$image_create_func = 'ImageCreateFromBMP';
				$image_save_func = 'ImageBMP';
				$new_image_ext = 'bmp';
			break;
			
			case 'gif':
				$image_create_func = 'ImageCreateFromGIF';
				$image_save_func = 'ImageGIF';
				$new_image_ext = 'gif';
			break;
			
			case 'vnd.wap.wbmp':
				$image_create_func = 'ImageCreateFromWBMP';
				$image_save_func = 'ImageWBMP';
				$new_image_ext = 'bmp';
			break;
			
			case 'xbm':
				$image_create_func = 'ImageCreateFromXBM';
				$image_save_func = 'ImageXBM';
				$new_image_ext = 'xbm';
			break;
			
			default:
				$image_create_func = 'ImageCreateFromJPEG';
				$image_save_func = 'ImageJPEG';
				$new_image_ext = 'jpg';
			break;
		}

		// New Image
        if( $keep_proportions ) { 
			$image_c = imagecreatetruecolor( $new_width, $new_height );
		} else {
			$image_c = imagecreatetruecolor( $width_constraint, $height_constraint );
		}
		$new_image = $image_create_func( $image_to_resize );
		
		// Keep transparency
		if( 'gif' == $type || 'png' == $type ) {
			//$transparent_color = imagecolortransparent( $new_image );
			imagepalettecopy( $new_image, $image_c );
			imagefill( $image_c, 0, 0, imagecolorallocate( $image_c, 255, 255, 255 ) ); // Make the background white
			//imagecolortransparent( $image_c, $transparent_color );
		}
		
		if( !$keep_proportions) {
			$destination_x = ceil( ( $width_constraint - $new_width ) / 2 );
			$destination_y = ceil( ( $height_constraint - $new_height ) / 2 );

			imagefilledrectangle( $image_c, 0, 0, $width_constraint, $height_constraint, imagecolorallocate( $image_c, 255, 255, 255 ) );
		} else {
			$destination_x = $destination_y = 0;
		}
		
		// Copy image to new image with new proportions
		imagecopyresampled( $image_c, $new_image, $destination_x, $destination_y, 0, 0, $new_width, $new_height, $width, $height );
		
		// mail( 'tom@studio98.com', 'Image Upload Data', ( "destination_x:" . $destination_x . ", destination_y:" . $destination_y . ", width:" . $width . ", height:" . $height . ", new_width:" . $new_width . ", new_height:" . $new_height.", width_constraint:" . $width_constraint . ", height_constraint:" . $height_constraint ) );
		
		// Make the directory if it doesn't exist
		if( !file_exists( $save_folder ) )
			mkdir( $save_folder, 0777, true );
		
		$new_name = ( $name ) ? $name . '.' . $new_image_ext : format::slug( basename( $image_to_resize ) ) . '_resized.' . $new_image_ext;

		$save_path = $save_folder . $new_name;
		
		// Remove any existing image
		if( is_file( $save_path ) )
			unlink( $save_path );
		
		$process = ( 'jpeg' == $type ) ? $image_save_func( $image_c, $save_path, $quality ) : $image_save_func( $image_c, $save_path );
		
		return array( $process, $save_path );
	}
}
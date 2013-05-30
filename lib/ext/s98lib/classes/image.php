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
    public static $extensions = array( 'jpeg', 'jpg', 'png', 'gif' );

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
	public static function proportions( $width, $height, $width_constraint, $height_constraint ) {
		if ( $width <= $width_constraint && $height <= $height_constraint )
			return array( $width, $height );
		
		$width_factor =  $width_constraint / $width;
		$height_factor = $height_constraint / $height;
		
		// Find out which way we have to resize it
		if ( $width_factor < $height_factor ) {
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
	public static function resize( $image_to_resize, $save_folder, $name = '', $width_constraint = 100, $height_constraint = 100, $quality = 90, $keep_proportions = true, $fill_constraints = true ) {
		if ( !file_exists( $image_to_resize ) )
			return false;

		$info = getimagesize( $image_to_resize );
		
		if ( empty( $info ) )
			return false;
					
		$width = $info[0];
		$height = $info[1];
		$mime = $info['mime'];
		
		// Keep proportions
		if ( $fill_constraints ) {
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
		
		// Create New Image
		$new_image = $image_create_func( $image_to_resize );
		
		// New Image
        if ( $keep_proportions ) { 
			$resized_image = imagecreatetruecolor( $new_width, $new_height );
		} else {
			$resized_image = imagecreatetruecolor( $width_constraint, $height_constraint );
		}

		// Keep transparency
		if ( $info[2] == IMAGETYPE_GIF || $info[2] == IMAGETYPE_PNG ) {
			$transparent_index = imagecolortransparent( $new_image );
			
			if ( $transparent_index >= 0 ) {
				$transparent_color = imagecolorsforindex( $new_image, $transparent_index );
				$transparent_allocated_color = imagecolorallocate( $resized_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue'] );
				
				imagefill( $resized_image, 0, 0, $transparent_allocated_color ); // Make the background white
				
				imagecolortransparent( $resized_image, $transparent_allocated_color );
			} elseif ( $info[2] == IMAGETYPE_PNG ) {
				// Turn off transparency blending (temporarily)
				imagealphablending( $resized_image, false );
		   
				// Create a new transparent color for image
				$transparent_allocated_color = imagecolorallocatealpha( $resized_image, 255, 255, 255, 127 );
		   
				// Completely fill the background of the new image with allocated color.
				imagefill( $resized_image, 0, 0, $transparent_allocated_color );
		   
				// Restore transparency blending
				imagesavealpha( $resized_image, true );
			}
		}
		
		if ( !$keep_proportions ) {
			$destination_x = ceil( ( $width_constraint - $new_width ) / 2 );
			$destination_y = ceil( ( $height_constraint - $new_height ) / 2 );

            // Determine allocated color -- special ternary operator
            $allocated_color = isset( $transparent_allocated_color ) ? $transparent_allocated_color : imagecolorallocate( $resized_image, 255, 255, 255 );

			imagefilledrectangle( $resized_image, 0, 0, $width_constraint, $height_constraint, $allocated_color );
		} else {
			$destination_x = $destination_y = 0;
		}
		
		// Copy image to new image with new proportions
		imagecopyresampled( $resized_image, $new_image, $destination_x, $destination_y, 0, 0, $new_width, $new_height, $width, $height );
		
		// Make the directory if it doesn't exist
		if ( !file_exists( $save_folder ) ) {
            // @fix MkDir isnt' changing the permissions, so we have to do the second call too.
			mkdir( $save_folder, 0777, true );
            chmod( $save_folder, 0777 );
        }

		$new_name = ( $name ) ? $name . '.' . $new_image_ext : format::slug( basename( $image_to_resize ) ) . '_resized.' . $new_image_ext;

		$save_path = $save_folder . $new_name;
		
		// Remove any existing image
		if ( is_file( $save_path ) )
			unlink( $save_path );
		
		$process = ( 'jpeg' == $type ) ? $image_save_func( $resized_image, $save_path, $quality ) : $image_save_func( $resized_image, $save_path );
		
		return array( $process, $save_path );
	}
}
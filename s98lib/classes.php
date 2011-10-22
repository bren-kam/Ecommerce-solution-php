<?php
/**
 * This will autoload the classes
 *
 * This calls another class so that this framework can 
 * be called within another system. If the other system 
 * has it's own autoload function, just call the 
 * function below.
 *
 * @param string $class_name the class name is automatically sent here
 */
if ( !function_exists( __autoload ) && AUTOLOAD ) {
	function __autoload( $class_name ) {
		s98lib_classes( $class_name );
	}
}

/**
 * Loads each class as it is called
 *
 * Tries to load from class directory first, if it doesn't exist
 * it checks if it's a module and then tries to include it there.
 *
 * @package Studio98 Framework
 * @since 1.0
 * 
 * @param string $class_name the class name is automatically sent here
 */
function s98lib_classes( $class_name ) {
	if ( '_' == $class_name[0] ) {
		$prefix = '_';
		$class_name = substr( $class_name, 1 );
	} else {
		$prefix = '';
	}

	$class_name = $prefix . strtolower( str_replace( '_', '-', $class_name ) );
	$class_path = FWPATH . "classes/$class_name.php";
	
	$libraries_path = FWPATH . "libraries/$class_name.php";
	
	if ( file_exists( $class_path ) ) {
		require_once $class_path;
	} elseif ( file_exists( $libraries_path ) ) {
		require_once $libraries_path;
	} else {
		global $modules;
		
		if ( in_array( $class_name, $modules ) ) {
			$mod_path = MODPATH . $class_name . '/class.' . $class_name . '.php';
			
			if ( file_exists( $mod_path ) )
				require_once $mod_path;
		}
	} 
}
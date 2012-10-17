<?php
require_once '../config.php';
require_once FWPATH . 'libraries/jsmin.php';

// Output a minified version of the Javascript file.
echo JSMin::minify( file_get_contents( $_SERVER['DOCUMENT_ROOT'] . "/" . $_GET['js_page'] . '.js' ) );
?>
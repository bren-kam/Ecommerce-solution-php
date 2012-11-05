<?php

/**
 * Load an exception
 *
 * @var string $exception
 */
function load_exception( $exception ) {
    if ( !stristr( $exception, 'Exception' ) )
        return;

    // Form the model name, i.e., AccountListing to account-listing.php
    $exception_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $exception ) ) . '.php', 1 );

    $full_path = LIB_PATH . 'exceptions/' . $exception_file;

    if ( is_file( $full_path ) )
        require_once $full_path;
}

/**
 * Load a response
 *
 * @var string $response

function load_response( $response ) {
    if ( !stristr( $response, 'Response' ) )
        return;

    // Form the model name, i.e., AccountListing to account-listing.php
    $response_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $response ) ) . '.php', 1 );

    $full_path = LIB_PATH . 'responses/' . $response_file;

    if ( is_file( $full_path ) )
        require_once $full_path;
}

/**
 * Load helpers
 *
 * @var string $helper

function load_helper( $helper ) {
    // Form the model name, i.e., AccountListing to account-listing.php
    $helper_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $helper ) ) . '.php', 1 );

    $full_path = LIB_PATH . 'helpers/' . $helper_file;

    if ( is_file( $full_path ) )
        require_once $full_path;
}

/**
 * Load a model
 *
 * @var string $model

function load_model( $model ) {
    // Form the model name, i.e., AccountListing to account-listing.php
    $model_file = substr( strtolower( preg_replace( '/(?<!-)[A-Z]/', '-$0', $model ) ) . '.php', 1 );

    // Define the paths to search
    $paths = array( MODEL_PATH, LIB_PATH . 'models/' );

    // Loop through each path and see if it exists
    foreach ( $paths as $path ) {
        $full_path = $path . $this->model_path . $model_file;

        if ( is_file( $full_path ) ) {
            require_once $full_path;
            break;
        }
    }
}

// Must always be included
lib('responses/response');
*/

spl_autoload_register( 'load_exception' );

//spl_autoload_register( 'load_response' );
//spl_autoload_register( 'load_helper' );
//spl_autoload_register( 'load_model' );

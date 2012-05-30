<?php
/**
 * @page Install a Package
 * @package Grey Suit Retail
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'install-package' ) ) {
	if ( !$user ) {
		echo json_encode( array( 'success' => false, 'error' => _('You must be signed in to install a package.') ) );
		exit;
	}

    // Make sure they have permission to remove it
    if ( $user['role'] < 7 ) {
		echo json_encode( array( 'success' => false, 'error' => _('You must be signed in to install a package.') ) );
		exit;
	}

    $w = new Websites;
    $response = $w->install_package( $_POST['wid'], $_POST['cpid'] );

	// If there was an error, let them know
	echo json_encode( array( 'success' => $response->success(), 'theme' => $response->get('theme'), 'message' => _('You have successfully installed the package!'), 'error' => _('An error occurred while trying to install a package. Please contact a system administrator.') ) );
} else {
	echo json_encode( array( 'success' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}
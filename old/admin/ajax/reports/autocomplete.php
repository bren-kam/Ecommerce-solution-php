<?php
/**
 * @page Autocomplete Reports
 * @package Grey Suit Retail
 * @subpackage Admin
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'autocomplete' ) ) {
	// Get the right suggestions for the right type
	switch ( $_POST['type'] ) {
		case 'brand':
			$b = new Brands;

			$results = $b->autocomplete( $_POST['term'] );
		break;
		
		case 'online_specialist':
			$w = new Websites;

			$results = $w->autocomplete_online_specialists( $_POST['term'] );
		break;

        case 'marketing_specialist':
            $w = new Websites;

            $results = $w->autocomplete_marketing_specialists( $_POST['term'] );
        break;

		case 'company':
            if ( $user['role'] > 7 ) {
                $c = new Companies;

                $results = $c->autocomplete( $_POST['term'] );
            } else {
                $results = array();
            }
		break;

        case 'billing_state':
            $results = $u->autocomplete( $_POST['term'], 'billing_state' );

            if ( is_array( $results ) )
            foreach ( $results as &$r ) {
                // Adjust for autocomplete
                $r['object_id'] = $r['billing_state'];
            }
        break;

        case 'package':
            $c = new Companies;

            $results = $c->autocomplete_packages( $_POST['term'] );
        break;
	}
	
	// Needs to return an array, even if nothing was gotten
	if ( !$results )
		$results = array();
	
	// Needs to be in JSON
	echo json_encode( array( 'objects' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}
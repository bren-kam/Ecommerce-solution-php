<?php
/**
 * @page Mobile Marketing
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

// Instantiate Classes
$m = new Mobile_Marketing;

$error = "";

// Check for submission
if ( isset( $_POST['_nonce'] ) ) {
	
	if ( nonce::verify( $_POST['_nonce'], 'update-mobile-pages' ) ) {
		
		// Save page data 
		$data = array( 
			'homepage' => array( 'content' => $_POST['taHomepageContent'], 'title' => 'Home' )
			, 'contact-us' => array( 'content' => $_POST['taContactUsContent'], 'title' => 'Contact Us' )
			, 'current-ad' => array( 'content' => $_POST['taCurrentAdContent'], 'title' => 'Current Ad' )
		);
		
		$result = $m->update_mobile_pages( $data );
		
	} else {
		$error = _("A verification error occurred when saving.  Please refresh the page and try again.");
	}
	
}

// Get current pages
$pages = $m->get_mobile_pages();

// Organize by slug
if ( $pages )
	$pages = ar::assign_key( $pages, 'slug' );

// Get data
$mobile_pages = 1;
$selected = "mobile_marketing";
$title = _('Mobile Pages') . ' | ' . TITLE;
javascript( 'mammoth', '' );
get_header();
?>

<div id="content">
	<h1><?php echo _('Mobile Pages'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/mobile-marketing/list-pages/" perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="65%" sort="1"><?php echo _('Title'); ?></th>
					<th width="15%"><?php echo _('Status'); ?></th>
					<th width="20%"><?php echo _('Last Updated'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>


<?php get_footer(); ?>
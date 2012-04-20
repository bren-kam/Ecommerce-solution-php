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
	<?php get_sidebar( 'mobile-marketing/', 'dashboard' ); ?>
	<div id="subcontent">
		<?php if ( !empty( $error ) ): ?>
			<p class="error"><?php echo $error; ?></p>
		<?php endif; ?>
		<form action="/mobile-marketing/pages/" method="POST">
			<h2>Homepage</h2>
			<textarea id="taHomepageContent" name="taHomepageContent" rte="1"><?php echo ( $pages['homepage'] ) ? $pages['homepage']['content'] : NULL; ?></textarea>
			<h2>Contact Us</h2>
			<textarea id="taContactUsContent" name="taContactUsContent" rte="1"><?php echo ( $pages['contact-us'] ) ? $pages['contact-us']['content'] : NULL; ?></textarea>
			<h2>Current Ad</h2>
			<textarea id="taCurrentAdContent" name="taCurrentAdContent" rte="1"><?php echo ( $pages['current-ad'] ) ? $pages['current-ad']['content'] : NULL; ?></textarea>
			<input type="submit" class="button" value="<?php echo _('Save Pages'); ?>" />
			<?php nonce::field( 'update-mobile-pages' ); ?>
		</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
<?php
/**
 * @page Edit Account
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;
$v = new Validator;

library('r53');

$r53 = new Route53( config::key('aws_iam-access-key'), config::key('aws_iam-secret-key') );

// Get the website id if there is one
$website_id = ( isset( $_GET['wid'] ) ) ? $_GET['wid'] : false;

$v->form_name = 'fEditDNS';

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-dns' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
        //$w->update_dns_zone();
	}
}

//$dns_zone = $w->get_dns_zone( $website_id );
$website = $w->get_website( $website_id );

css( 'form', 'accounts/edit' );
javascript( 'validator', 'jquery', 'accounts/edit' );

$selected = 'accounts';
$title = _('Edit Account') . ' | ' . TITLE;
get_header();
?>

<div id="content">
    <h1><?php echo _('DNS Zone File'), ': ', $website['title']; ?></h1>
	<br clear="all" /><br />
	<?php $sidebar_emails = true; get_sidebar( 'accounts/', 'dns' ); ?>
	<div id="subcontent">
        <?php fn::info( $r53->createHostedZone( 'test.com', md5( microtime() ) ) ); ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>

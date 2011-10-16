<?php
/**
 * @page Analytics
 * @package Imagine Retailer
 */

global $user;

// Instantiate Classes
$fb = new FB( '179756052091285', '8a76794c39b8992c21f706c9258c8bbb', false, array( 'scope' => 'read_insights,offline_access' ) );
$a = new Analytics;
$v = new Validator;

// Set Validation
$v->add_validation( 'tFBConnectionKey', 'req', _('The "Facebook Connection Key" field is required') );

// Make sure it's a valid request
if( nonce::verify( $_POST['_nonce'], 'connect-to-field' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if( empty( $errs ) )
		$success = $a->connect( $_POST['sFBPageID'], $fb->getAccessToken(), $_POST['tFBConnectionKey'] );
}

// Make sure it's a valid request
if( nonce::verify( $_GET['_nonce'], 'remove-connection' ) )
	$remove_success = $a->remove_connection( $_GET['rc'], $fb->getAccessToken() );

// Get the conneted pages
$connected_pages = ar::assign_key( $a->get_connected_pages( $fb->getAccessToken() ), 'fb_page_id' );

// Get the pages via FQL
$params['access_token'] = $fb->getAccessToken();
$params['method'] = 'fql.query';
$params['query'] = "SELECT page_id, name FROM page WHERE page_id IN ( SELECT page_id FROM page_admin WHERE uid = me() AND type <> 'APPLICATION' )";

$pages_xml = simplexml_load_string( $fb->makeRequest( 'https://api.facebook.com/restserver.php', $params ) );

// Turn them into an associateive array
$pages = array();
foreach ( $pages_xml->page as $page ) {
	$pages[(string) $page->page_id] = (string) $page->name;
}

add_footer('<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "179756052091285", status: true, cookie: true,
             xfbml: true});
	FB.setSize({ width: 720, height: 500 });
  };
  (function() {
    var e = document.createElement("script"); e.async = true;
    e.src = document.location.protocol +
      "//connect.facebook.net/en_US/all.js";
    document.getElementById("fb-root").appendChild(e);
  }());
</script>');
$title = _('Analytics') . ' | ' . _('Online Platform');
get_header('facebook/');
?>

<div id="content">
	<h1><?php echo _('Online Platform - Analytics'); ?></h1>
	<?php 
	if( $success ) 
		echo '<p class="success">', _('Your information has been successfully updated!'), '</p>';

	if( $remove_success ) 
		echo '<p class="success">', _('Your connection has been successfully removed!'), '</p>';

	if( isset( $errs ) )
			echo "<p class='error'>$errs</p>";
	
	?>
	<form name="fConnect" method="post" action="/facebook/analytics/">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th width="200"><label for="sFBPageID"><strong>Page</strong></label></td>
			<th><label for="tFBConnectionKey"><strong>Connection Key</strong></label></td>
		</tr>
			<tr>
				<td>
					<select name="sFBPageID" id="sFBPageID">
					<?php 
					foreach( $pages as $fb_page_id => $name ) {
						if ( array_key_exists( $fb_page_id, $connected_pages ) )
							continue;
						?>
						<option value="<?php echo $fb_page_id; ?>"><?php echo $name; ?></option>
					<?php } ?>
					</select>
				</td>
				<td><input type="text" class="tb" name="tFBConnectionKey" id="tFBConnectionKey" value="" /></td>
			</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" class="button" value="<?php echo _('Connect'); ?>" /></td>
		</tr>
	</table>
	<?php nonce::field('connect-to-field'); ?>
	</form>
	<br /><br />
	
	<?php if ( is_array( $connected_pages ) ) { ?>
	<table cellpadding="0" cellspacing="0" class="form">
		<tr>
			<th width="150"><strong>Page</strong></th>
			<th width="150"><strong>Website</strong></th>
			<th><strong>Remove</strong></th>
		</tr>
		<?php foreach ( $connected_pages as $fb_page_id => $cp ) { ?>
		<tr>
			<td><?php echo $pages[$fb_page_id]; ?></td>
			<td><?php echo $cp['title']; ?></td>
			<td><a href="/facebook/analytics/?rc=<?php echo $fb_page_id; ?>&_nonce=<?php echo nonce::create('remove-connection'); ?>">Remove Connection</a></td>
		</tr>
		<?php } ?>
	</table>
	<?php } ?>
</div>

<?php get_footer('facebook/'); ?>
<?php
/**
 * @page Mobile Marketing
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

$w = new Websites();

$settings = $w->get_settings( 'trumpia-username', 'trumpia-password' );
$url = 'http://greysuitmobile.com/action/index.act?mode=signin';
$post = array(
	'id' => $settings['trumpia-username']
	, 'password' => $settings['trumpia-password']
	, 'version' => '2'
);

$response = json_decode( curl::post( $url, $post ) );

$selected = "mobile_marketing";
$title = _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Mobile Marketing'); ?> - <a href="/mobile-marketing/website/" title="<?php echo _('Mobile Marketing - Website'); ?>" class="small">(<?php echo _('go to website'); ?>)</a> </h1>
	<br clear="all" /><br />
        <?php if ( '1' == $response->result ) { ?>
			<p align="center" id="loading"><?php echo _('Loading...'); ?></p>
			<iframe src="/mobile-marketing/trumpia-form/" width="100%" height="600" id="iframe" class="hidden"></iframe>
			<script type="text/javascript">
				setTimeout( function() {
					$('#iframe').attr( 'src', 'http://greysuitmobile.com/main.php' );
					
					setTimeout( function() {
						$('#loading').remove();
						$('#iframe').show();
					 }, 2000 );
				}, 2000 );

                // Keep us logged in
                setInterval( function() {
                    $("#refresh").remove();
                    $('body').append('<iframe src="/mobile-marketing/trumpia-form/" width="100%" height="600" id="refresh" class="hidden"></iframe>');
                }, 180000 );
			</script>
		<?php } else { ?>
			<p><?php echo _('Mobile Marketing setup has not been completed. Please contact your online specialist for assistance'); ?></p>
		<?php } ?>
	<br /><br />
</div>

<?php get_footer(); ?>
<?php
/**
 * @page Social Media - Facebook - Email Sign Up
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['social_media'] )
    url::redirect('/');

// Make Sure they can only get here when they select a page
if ( !isset( $_GET['smfbpid'] ) )
    url::redirect('/social-media/facebook/');

$s = new Social_Media();
$facebook_page = $s->get_facebook_page( $_GET['smfbpid'] );

// They chose an invalid page
if ( !$facebook_page )
    url::redirect('/social-media/facebook/');

// Set the session
$_SESSION['sm_facebook_page_id'] = $facebook_page['id'];

$w = new Websites;
$settings = $w->get_settings( 'facebook-url', 'social-media-add-ons' );
	
css('social-media/facebook/main');
$selected = "social_media";
$title = _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Facebook'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
        <?php
        $social_media_add_ons = array(
            'email-sign-up' => _('Email Sign Up')
            , 'fan-offer' => _('Fan Offer')
            , 'sweepstakes' => _('Sweepstakes')
            , 'share-and-save' => _('Share and Save')
            , 'facebook-site' => _('Facebook Site')
            , 'contact-us' => _('Contact Us')
            , 'about-us' => _('About Us')
            , 'products' => _('Products')
            , 'current-ad' => _('Current Ad')
            , 'posting' => _('Posting')
        );

        $website_social_media_add_ons = @unserialize( $settings['social-media-add-ons'] );

        foreach ( $social_media_add_ons as $url => $name ) {
            if ( !is_array( $website_social_media_add_ons ) || !in_array( $url, $website_social_media_add_ons ) )
                continue;

            $image = $url;

            switch ( $url ) {
                case 'posting':
                    $url .= '/post';
                break;

                default:break;
            }
            ?>
            <p class="sm">
                <a href="/social-media/facebook/<?php echo $url; ?>/" title="<?php echo $name; ?>"><img src="/images/social-media/facebook/<?php echo $image; ?>.jpg" width="75" height="75" alt="<?php echo $name; ?>" /></a>
                <br />
                <a href="/social-media/facebook/<?php echo $url; ?>/" title="<?php echo $name; ?>"><?php echo $name; ?></a>
            </p>
            <?php
        }

        if ( !empty( $settings['facebook_url'] ) ) {
        ?>
		<p class="sm">
			<a href="<?php echo $settings['facebook_url']; ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><img src="/images/social-media/facebook/analytics.jpg" width="75" height="75" alt="<?php echo _('Analytics'); ?>" /></a>
			<br />
			<a href="<?php echo $settings['facebook_url']; ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><?php echo _('Analytics'); ?></a>
		</p>
		<?php } ?>
		<br clear="all" /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
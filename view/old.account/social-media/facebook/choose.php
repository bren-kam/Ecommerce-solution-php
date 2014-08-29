<?php
/**
 * @package Grey Suit Retail
 * @page Choose | Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $settings
 */

echo $template->start( _('Facebook'), 'sidebar' );
?>

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

    $link = url::add_query_arg( 'smfbpid', $_GET['smfbpid'], "/social-media/facebook/$url/" );
    ?>
    <p class="sm">
        <a href="<?php echo $link; ?>" title="<?php echo $name; ?>"><img src="/images/social-media/facebook/<?php echo $image; ?>.jpg" width="75" height="75" alt="<?php echo $name; ?>" /></a>
        <br />
        <a href="<?php echo $link; ?>" title="<?php echo $name; ?>"><?php echo $name; ?></a>
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
<br clear="left" /><br />
<br /><br />
<br /><br />
<br /><br />
<?php echo $template->end(); ?>
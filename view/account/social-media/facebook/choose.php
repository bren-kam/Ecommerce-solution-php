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
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Choose Facebook Application
            </header>

            <div class="panel-body">

                <div class="row">
                    <?php
                    $social_media_add_ons = array(
                        'email-sign-up' => _('Email Sign Up')
                        , 'fan-offer' => _('Fan Offer')
                        , 'sweepstakes' => _('Sweepstakes')
                        , 'share-and-save' => _('Share and Save')
                        //, 'facebook-site' => _('Facebook Site')
                        , 'contact-us' => _('Contact Us')
                        , 'about-us' => _('About Us')
                        , 'products' => _('Products')
                        , 'current-ad' => _('Current Ad')
                        , 'posting' => _('Posting')
                    );

                    $website_social_media_add_ons = @unserialize( $settings['social-media-add-ons'] );

                    foreach ( $social_media_add_ons as $url => $name ):
                        if ( !is_array( $website_social_media_add_ons ) || !in_array( $url, $website_social_media_add_ons ) )
                            continue;

                        $image = $url;

                        switch ( $url ):
                            case 'posting':
                                $url .= '/post';
                                break;

                            default:break;
                        endswitch;

                        $link = url::add_query_arg( 'smfbpid', $_GET['smfbpid'], "/social-media/facebook/$url/" );
                    ?>

                        <div class="col-lg-2 text-center">
                            <a href="<?php echo $link; ?>" title="<?php echo $name; ?>"><img src="/images/social-media/facebook/<?php echo $image; ?>.jpg" /></a>
                            <br />
                            <a href="<?php echo $link; ?>" title="<?php echo $name; ?>"><?php echo $name; ?></a>
                        </div>
                    <?php endforeach; ?>

                    <?php if ( !empty( $settings['facebook_url'] ) ):
                        ?>
                        <div class="col-lg-2 text-center">
                            <a href="<?php echo $settings['facebook_url']; ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><img src="/images/social-media/facebook/analytics.jpg" /></a>
                            <br />
                            <a href="<?php echo $settings['facebook_url']; ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><?php echo _('Analytics'); ?></a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </div>
</div>
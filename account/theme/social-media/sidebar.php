<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
    <a href="/social-media/facebook/" class="top" title="<?php echo _('Pages'); ?>"><?php echo _('Pages'); ?></a>
    <?php if ( isset( $facebook_pages ) ) { ?>
        <a href="/social-media/facebook/add-edit/" title="<?php echo _('Add a Page'); ?>" class="sub"><?php echo _('Add'); ?></a>
    <?php
    }
    if ( isset( $_SESSION['sm_facebook_page_id'] ) ) {
        $w = new Websites;
        $settings = $w->get_settings( 'facebook-url', 'social-media-add-ons' );

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

                switch ( $url ) {
                    case 'posting':
                        $url .= '/post';
                    break;

                    default:break;
                }
            ?>
            <a href="/social-media/facebook/<?php echo $url; ?>/" class="top" title="<?php echo $name; ?>"><?php echo $name; ?></a>
            <?php if ( 'posting/post' == $url && isset( $posting ) ) { ?>
                <a href="/social-media/facebook/posting/" title="<?php echo _('View Posts'); ?>" class="sub"><?php echo _('View'); ?></a>
            <?php
            }
        }
        ?>

        <?php if ( !empty( $settings['facebook-url'] ) ) { ?>
        <a href="<?php echo $settings['facebook-url']; ?>" class="top" title="<?php echo _('Analytics'); ?>" target="_blank"><?php echo _('Analytics'); ?></a>
        <?php
        }
    }
    ?>
    <a href="/social-media/settings/" class="top" title="<?php echo _('Settings'); ?>"><?php echo _('Settings'); ?></a>
</div>
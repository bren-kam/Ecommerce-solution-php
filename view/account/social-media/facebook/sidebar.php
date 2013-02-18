<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/social-media/facebook/" title="<?php echo _('Pages'); ?>" class="top first<?php $template->select('facebook-pages'); ?>"><?php echo _('Pages'); ?></a>
    <?php if ( $template->v('facebook-pages') ) { ?>
        <a href="/social-media/facebook/" title="<?php echo _('View'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
        <a href="/social-media/facebook/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
    <?php
    }

    if ( isset( $_SESSION['sm_facebook_page_id'] ) ) {
        $settings = $user->account->get_settings( 'facebook-url', 'social-media-add-ons' );

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
            <a href="/social-media/facebook/<?php echo $url; ?>/" class="top<?php $template->select( format::slug( $name ) ); ?>" title="<?php echo $name; ?>"><?php echo $name; ?></a>
            <?php if ( 'posting/post' == $url && isset( $posting ) ) { ?>
                <a href="/social-media/facebook/posting/" title="<?php echo _('View Posts'); ?>" class="sub view<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <?php
            }
        }
        ?>

        <?php if ( !empty( $settings['facebook-url'] ) ) { ?>
        <a href="<?php echo $settings['facebook-url']; ?>" class="top<?php $template->select( 'analytics' ); ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><?php echo _('Analytics'); ?></a>
        <?php
        }
    }
    ?>

	<a href="/social-media/facebook/settings/" title="<?php echo _('Settings'); ?>" class="top last<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
</div>
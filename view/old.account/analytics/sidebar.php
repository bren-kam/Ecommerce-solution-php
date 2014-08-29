<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/analytics/" title="<?php echo _('Dashboard'); ?>" class="top first<?php $template->select('dashboard'); ?>"><?php echo _('Dashboard'); ?></a>
    <a href="/analytics/content-overview/" title="<?php echo _('Content Overview'); ?>" class="top<?php $template->select('content-overview'); ?>"><?php echo _('Content Overview'); ?></a>

    <a href="/analytics/traffic-sources-overview/" title="<?php echo _('Traffic Sources'); ?>" class="top<?php $template->select('traffic-sources'); ?>"><?php echo _('Traffic Sources'); ?></a>
    <?php if ( $template->v('traffic-sources') ) { ?>
		<a href="/analytics/traffic-sources/" title="<?php echo _('Sources'); ?>" class="sub<?php $template->select('sources'); ?>"><?php echo _('Sources'); ?></a>
		<a href="/analytics/keywords/" title="<?php echo _('Keywords'); ?>" class="sub<?php $template->select('keywords'); ?>"><?php echo _('Keywords'); ?></a>
	<?php
    }

    if ( $user->account->email_marketing ) {
    ?>
    <a href="/analytics/email-marketing/" title="<?php echo _('Email Marketing'); ?>" class="top<?php $template->select('email-marketing'); ?>"><?php echo _('Email Marketing'); ?></a>
    <?php
    }

    $facebook_url = $user->account->get_settings( 'facebook-url' );

    if ( !empty( $facebook_url ) ) {
    ?>
    <a href="<?php echo $facebook_url; ?>" title="<?php echo _('Facebook'); ?>" class="top" target="_blank"><?php echo _('Facebook'); ?></a>
    <?php } ?>
</div>
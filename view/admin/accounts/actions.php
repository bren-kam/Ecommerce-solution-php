<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account > Actions
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var array $settings
 */

?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/edit/?aid=<?php echo $account->id; ?>" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/actions/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $account->craigslist ) { ?>
        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
    <?php
    }

    if ( $user->has_permission(10) ) {
        ?>
        <div class="tab-link"><a href="/accounts/dns/?aid=<?php echo $account->id; ?>" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php echo $template->start( _('Actions') ); ?>

<?php if ( 0 == $account->version ) { ?>
    <p><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_website' ), '/accounts/install-website/' ) ); ?>" title="<?php echo _('Install Website'); ?>"><?php echo _('Install Website'); ?></a></p>
<?php
}

if ( 0 != $account->version ) {
?>
    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/install-package/' ); ?>" title="<?php echo _('Install Package'); ?>"><?php echo _('Install Package'); ?></a></p>
<?php } ?>

<p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/delete-categories-and-products/' ); ?>" title="<?php echo _('Delete Categories and Products'); ?>" confirm="<?php echo _('Are you sure you want to delete all categories and products? This cannot be undone.'); ?>"><?php echo _('Delete Categories and Products'); ?></a></p>

<?php if ( empty( $settings['trumpia-api-key'] ) ) { ?>
    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/create-trumpia-account/' ); ?>" title="<?php echo _('Create Trumpia Account'); ?>" rel="dialog" cache="0"><?php echo _('Create Trumpia Account'); ?></a></p>
<?php } ?>

<?php if ( $account->craigslist && empty( $settings['craigslist-customer-id'] ) ) { ?>
    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/create-craigslist-account/' ); ?>" title="<?php echo _('Create Craigslist Account'); ?>"><?php echo _('Create Craigslist Account'); ?></a></p>
<?php } ?>

<p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/cancel/' ); ?>" title="<?php echo _('Cancel Account'); ?>" confirm="<?php echo _('Are you sure you want to deactivate this account?'); ?>"><?php echo _('Cancel Account'); ?></a></p>

<p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/run-ashley-feed/' ); ?>" title="<?php echo _('Run Ashley FTP'); ?>"><?php echo _('Run Ashley FTP'); ?></a></p>


<?php echo $template->end(); ?>
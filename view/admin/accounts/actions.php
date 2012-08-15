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
 */

?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/edit/?aid=<?php echo $account->id; ?>" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/actions/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $user->has_permission(10) ) { ?>
        <div class="tab-link"><a href="/accounts/dns/?aid=<?php echo $account->id; ?>" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php echo $template->start( _('Actions') ); ?>

<table class="col-2">
    <?php if ( 0 == $account->version ) { ?>
        <tr>
            <td><?php echo _('Install Website'); ?>:</td>
            <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_website' ), '/accounts/install-website/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Install Website'); ?>"><?php echo _('Install Website'); ?></a></td>
        </tr>
    <?php
    }

    if ( 0 != $account->version ) {
    ?>
        <tr>
            <td><?php echo _('Install Package'); ?>:</td>
            <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/install-package/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Install Package'); ?>"><?php echo _('Install Package'); ?></a></td>
        </tr>
    <?php } ?>
    <tr>
        <td><?php echo _('Delete Categories and Products'); ?>:</td>
        <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/delete-categories-and-products/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Delete'); ?>"><?php echo _('Delete'); ?></a></td>
    </tr>
    <tr>
        <td><?php echo _('Reset Social Media'); ?>:</td>
        <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/delete-categories-and-products/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Delete'); ?>"><?php echo _('Delete'); ?></a></td>
    </tr>
    <tr>
        <td><?php echo _('Create Trumpia Account'); ?>:</td>
        <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/delete-categories-and-products/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Delete'); ?>"><?php echo _('Delete'); ?></a></td>
    </tr>
    <tr>
        <td><?php echo _('Reset Social Media'); ?>:</td>
        <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/delete-categories-and-products/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Delete'); ?>"><?php echo _('Delete'); ?></a></td>
    </tr>
    <tr>
        <td><?php echo _('Cancel Account'); ?>:</td>
        <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/delete-categories-and-products/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Delete'); ?>"><?php echo _('Delete'); ?></a></td>
    </tr>
    <tr>
        <td><?php echo _('Run Ashley FTP'); ?>:</td>
        <td><a href="<?php echo url::add_query_arg( array( 'aid' => $account->id, '_nonce' => nonce::create('install_package' ), '/accounts/delete-categories-and-products/' ) ); ?>" class="button" ajax="1" title="<?php echo _('Delete'); ?>"><?php echo _('Delete'); ?></a></td>
    </tr>
</table>


<?php echo $template->end(); ?>
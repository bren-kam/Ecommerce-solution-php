<?php
/**
 * @package Grey Suit Retail
 * @page Mobile Marketing - Add/Edit
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var MobilePage $page
 * @var string $errs
 * @var string $js_validation
 */

$title = ( $page->id ) ? _('Edit Page') : _('Add Page');
echo $template->start( $title );

if ( isset( $errs ) )
    echo "<p class='red'>$errs</p>";
?>
<form name="fAddEditPage" action="<?php echo url::add_query_arg( 'mpid', $page->id, '/mobile-marketing/website/add-edit/' ); ?>" method="post">
    <div id="title-container">
        <input name="tTitle" id="tTitle" class="tb" value="<?php echo $page->title; ?>" tmpval="<?php echo _('Page Title...'); ?>" />
    </div>
    <?php if ( 'home' != $page->slug ) { ?>
    <div id="dSlug">
        <span><strong><?php echo _('Link:'); ?></strong> http://<?php echo $user->account->domain; ?>/<input type="text" name="tSlug" id="tSlug" maxlength="50" class="tb" value="<?php echo $page->slug; ?>" />/</span>
    </div>
    <?php } ?>
    <br />
    <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $page->content; ?></textarea>

    <br /><br />
    <br /><br />
    <p><input type="submit" value="<?php echo _('Save'); ?>" class="button" /></p>
    <?php nonce::field( 'add_edit' ); ?>
</form>
<?php
echo $js_validation;
echo $template->end();
?>
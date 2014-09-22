<?php
/**
 * @package Grey Suit Retail
 * @page Import | Subscribers | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailList[] $email_lists
 */

echo $template->start( _('Import'), '../sidebar' );
?>

<div id="dUploadedSubscribers" class="hidden">
    <p><?php echo _('Please verify the first email addresses below are correct:'); ?></p>
    <table cellpadding="0" cellspacing="1" id="tUploadedSubcribers" class="generic">
        <tr>
            <th width="50%"><?php echo _('Email'); ?></th>
            <th><?php echo _('Name'); ?></th>
        </tr>
    </table>
    <br /><br />
    <form action="/email-marketing/subscribers/import/" method="post">
        <?php nonce::field( 'import' ); ?>
        <input type="hidden" name="hEmailLists" id="hEmailLists" />
        <input type="submit" class="button" value="<?php echo _('Continue'); ?>" />
    </form>
</div>
<div id="dDefault">
    <p><?php echo _('On this page you can import a list of subscribers who have requested you email them information.'); ?></p>
    <p><?php echo _('Please make your spreadsheet layout match the example below.'); ?></p>
    <p><?php echo _('Example:'); ?></p>
    <table cellpadding="0" cellspacing="1" class="generic">
        <tr>
            <th width="50%"><?php echo _('Email'); ?></th>
            <th><?php echo _('Name'); ?></th>
        </tr>
        <tr>
            <td><?php echo _('email@example.com'); ?></td>
            <td><?php echo _('John Doe'); ?></td>
        </tr>
        <tr>
            <td><?php echo _('jane@doe.com'); ?></td>
            <td><?php echo _('Jane'); ?></td>
        </tr>
        <tr class="last">
            <td>...</td>
            <td>...</td>
        </tr>
    </table>
    <br /><br />
    <p>
    <?php
        foreach ( $email_lists as $el ) {
            $checked = ( 0 == $el->category_id ) ? ' checked="checked"' : '';
    ?>
        <input type="checkbox" class="cb" value="<?php echo $el->id; ?>" id="el<?php echo $el->id; ?>"<?php echo $checked; ?> /> <label for="el<?php echo $el->id; ?>"><?php echo $el->name; ?></label><br />
    <?php } ?>
    </p>

    <br />
    <a href="#" id="aImportSubscribers" class="button" title="<?php echo _('Import'); ?>"><?php echo _('Import'); ?></a>
    <div class="hidden" id="import-subscribers"></div>
    <?php nonce::field( 'import_subscribers', '_import_subscribers' ); ?>
    <br /><br />
    <br /><br />
</div>

<?php echo $template->end(); ?>
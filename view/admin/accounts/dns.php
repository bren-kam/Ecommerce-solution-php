<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account > DNS
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var int $zone_id
 * @var string $errs
 * @var string $domain_name,
 * @var string $full_domain_name,
 * @var array $records
 */

?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/edit/?aid=<?php echo $account->id; ?>" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/actions/?aid=<?php echo $account->id; ?>" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $account->craigslist ) { ?>
        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
    <?php
    }

    if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ) {
        ?>
        <div class="tab-link"><a href="/accounts/dns/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php
echo $template->start( _('DNS Zone File:') . ' ' . $account->title );

if ( $errs )
    echo '<p class="red">' . $errs . '</p><br />';

if ( !empty( $zone_id ) ) {
?>
    <a href="<?php echo url::add_query_arg( 'a', 'delete' ); ?>" class="button float-right" title="<?php echo _('Delete Zone File'); ?>"><?php echo _('Delete Zone File'); ?></a>
    <br class="clr" /><br />
    <form name="fEditDNS" id="fEditDNS" action="" method="post" err="<?php echo 'The records you have entered do not match the type you have selected.'; ?>">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td><label><?php echo _('Domain'); ?>: </label></td>
            <td><?php echo $domain_name; ?></td>
        </tr>
        <tr>
            <td><label><?php echo _('Records'); ?>: </label></td>
            <td>
                <table cellpadding="0" cellspacing="0" class="format">
                    <thead>
                        <tr>
                            <th><?php echo _('Name'); ?></th>
                            <th><?php echo _('Type'); ?></th>
                            <th><?php echo _('TTL'); ?></th>
                            <th><?php echo _('Records'); ?></th>
                            <th width="35">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ( is_array( $records['ResourceRecordSets'] ) )
                        foreach ( $records['ResourceRecordSets'] as $r ) {
                            $no_delete =  $full_domain_name == $r['Name'] && ( 'NS' == $r['Type'] || 'SOA' == $r['Type'] );
                            ?>
                            <tr>
                                <td class="top"><?php if ( $no_delete ) { echo $r['Name']; } else { ?><input type="text" name="changes[name][]" class="tb disabled" disabled="disabled" tmpval="<?php echo _('Domain'); ?>" value="<?php echo $r['Name']; ?>" /><input class="action disabled" type="hidden" disabled="disabled" name="changes[action][]" value="" /><?php } ?></td>
                                <td class="top">
                                    <?php if ( $no_delete ) { echo $r['Type']; } else { ?>
                                    <select name="changes[type][]" class="disabled" disabled="disabled">
                                        <?php
                                        $types = array( 'A', 'CNAME', 'MX', 'NS' );
                                        foreach ( $types as $type ) {
                                            $selected = ( $type == $r['Type'] ) ? ' selected="Selected"' : '';
                                            ?>
                                            <option value="<?php echo $type; ?>"<?php echo $selected; ?>><?php echo $type; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php } ?>
                                </td>
                                <td class="top"><?php if ( $no_delete ) { echo $r['TTL']; } else { ?><input type="text" name="changes[ttl][]" class="tb disabled" tmpval="TTL" value="<?php echo $r['TTL']; ?>" disabled="disabled" /><?php } ?></td>
                                <td class="top"><?php if ( $no_delete ) { echo implode( "<br />\n", preg_replace( '/\.$/', '', $r['ResourceRecords'] ) ); } else { ?><textarea name="changes[records][]" class="tmpval disabled" cols="50" rows="3" tmpval="<?php echo _('Records - 1 per line'); ?>" disabled="disabled"><?php echo implode( "\n", $r['ResourceRecords'] ); ?></textarea><?php } ?></td>
                                <td class="top">
                                    <?php if ( !$no_delete ) { ?>
                                        <a href="#" class="edit-record" title="<?php echo _('Edit Record'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit Record'); ?>" /></a>
                                        <a href="#" class="delete-record" title="<?php echo _('Delete Record'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete Record'); ?>" /></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="last"><td colspan="4"><a href="#" id="aAddRecord" title="<?php echo _('Add Record'); ?>"><?php echo _('Add Record'); ?></a></td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" class="button" value="<?php echo _('Save DNS'); ?>" /></td>
        </tr>
    </table>
    <?php nonce::field('dns'); ?>
    </form>
    <table class="hidden" id="original">
        <tr>
            <td class="top"><input type="text" name="changes[name][]" class="tb changes-name" tmpval="<?php echo _('Domain'); ?>" /><input type="hidden" class="action" name="changes[action][]" value="1" /></td>
            <td class="top">
                <select name="changes[type][]" class="changes-type">
                <?php
                    $types = array( 'A', 'CNAME', 'MX', 'NS' );
                    foreach ( $types as $type ) {
                        ?>
                        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td class="top"><input type="text" name="changes[ttl][]" class="tb changes-ttl" tmpval="TTL" value="14400" /></td>
            <td class="top"><textarea name="changes[records][]" class="tmpval changes-records" cols="50" rows="3" tmpval="<?php echo _('Records - 1 per line'); ?>"><?php echo _('Records - 1 per line'); ?></textarea></td>
            <td>
                <a href="#" class="edit-record" title="<?php echo _('Edit Record'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit Record'); ?>" /></a>
                <a href="#" class="delete-record" title="<?php echo _('Delete Record'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete Record'); ?>" /></a>
            </td>
        </tr>
    </table>
<?php } else { ?>
    <p><a href="<?php echo url::add_query_arg('a', 'create'); ?>" class="button" title="<?php echo _('Create Zone File'); ?>"><?php echo _('Create Zone File'); ?></a></p>
    <br /><br />
    <br /><br />
    <br /><br />
<?php 
} 

echo $template->end(); 
?>
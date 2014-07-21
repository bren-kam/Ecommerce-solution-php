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

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
                    <li><a href="/accounts/website-settings/?aid=<?php echo $account->id ?>">Website</a></li>
                    <li><a href="/accounts/other-settings/?aid=<?php echo $account->id ?>">Other</a></li>
                    <li><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $account->craigslist ): ?>
                        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
                    <?php endif; ?>

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li class="active"><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>

                    <li><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
                    <li class="dropdown">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Customize <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/accounts/customize/settings/?aid=<?php echo $account->id ?>">Settings</a></li>
                            <li><a href="/accounts/customize/stylesheet/?aid=<?php echo $account->id ?>">LESS/CSS</a></li>
                            <li><a href="/accounts/customize/favicon/?aid=<?php echo $account->id ?>">Favicon</a></li>
                        </ul>
                    </li>
                </ul>
                <h3>DNS: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">


<?php if ( $errs ): ?>
    <div class="alert alert-danger"><?php echo $errs ?></div>
<?php endif; ?>

<?php if ( !empty( $zone_id ) ): ?>

    <h4>Records</h4>

    <form name="fEditDNS" id="fEditDNS" action="" method="post" err="<?php echo 'The records you have entered do not match the type you have selected.'; ?>" role="form">

        <table class="table">
            <thead>
                <tr>
                    <th><?php echo _('Name'); ?></th>
                    <th><?php echo _('Type'); ?></th>
                    <th><?php echo _('TTL'); ?></th>
                    <th><?php echo _('Records'); ?></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ( is_array( $records['ResourceRecordSets'] ) )
                foreach ( $records['ResourceRecordSets'] as $r ):
                    $no_delete =  $full_domain_name == $r['Name'] && ( 'NS' == $r['Type'] || 'SOA' == $r['Type'] );
                    ?>
                    <tr>
                        <td>
                            <?php if ( $no_delete ) { echo $r['Name']; } else { ?>
                                <input type="text" name="changes[name][]" class="form-control disabled" disabled="disabled" placeholder="<?php echo _('Domain'); ?>" value="<?php echo $r['Name']; ?>" />
                                <input class="action disabled" type="hidden" disabled="disabled" name="changes[action][]" value="" />
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ( $no_delete ) { echo $r['Type']; } else { ?>
                                <select name="changes[type][]" class="form-control disabled changes-type" disabled="disabled">
                                    <?php
                                    $types = array( 'A', 'CNAME', 'MX', 'NS', 'TXT' );
                                    foreach ( $types as $type ) {
                                        $selected = ( $type == $r['Type'] ) ? ' selected="Selected"' : '';
                                        ?>
                                        <option value="<?php echo $type; ?>"<?php echo $selected; ?>><?php echo $type; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ( $no_delete ) { echo $r['TTL']; } else { ?>
                                <input type="text" name="changes[ttl][]" class="form-control disabled" placeholder="TTL" value="<?php echo $r['TTL']; ?>" disabled="disabled" />
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ( $no_delete ) { echo implode( "<br />\n", preg_replace( '/\.$/', '', $r['ResourceRecords'] ) ); } else { ?>
                                <textarea name="changes[records][]" class="form-control disabled changes-records" cols="40" rows="3" placeholder="<?php echo _('Records - 1 per line'); ?>" disabled="disabled"><?php echo implode( "\n", $r['ResourceRecords'] ); ?></textarea>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ( !$no_delete ) { ?>
                                <a href="#" class="edit-record" title="<?php echo _('Edit Record'); ?>"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="delete-record" title="<?php echo _('Delete Record'); ?>"><i class="fa fa-trash-o"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><a href="#" id="aAddRecord" class="btn btn-default" title="<?php echo _('Add Record'); ?>"><?php echo _('Add Record'); ?></a></p>

        <p>
            <a href="<?php echo url::add_query_arg( 'a', 'delete' ); ?>" class="btn btn-warning" title="<?php echo _('Delete Zone File'); ?>" confirm="Delete zone file?"><?php echo _('Delete Zone File'); ?></a>
            <button type="submit" class="btn btn-primary pull-right">Save DNS</button>
        </p>

        <?php nonce::field('dns'); ?>
    </form>

<?php else: ?>

    <p>No zone file found.</p>
    <a href="<?php echo url::add_query_arg('a', 'create'); ?>" class="btn btn-primary btn-lg" title="<?php echo _('Create Zone File'); ?>"><?php echo _('Create Zone File'); ?></a>

<?php endif; ?>




</div>
        </section>
    </div>
</div>



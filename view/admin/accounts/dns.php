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
 * @var string $cloudflare_zone_id
 * @var string $errs
 * @var string $domain_name,
 * @var string $full_domain_name,
 * @var array $records
 * @var array $zone_details
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

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li class="active"><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>

                    <li><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
                    <li><a href="/accounts/passwords/?aid=<?php echo $account->id ?>">Passwords</a></li>
                    <li class="dropdown">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Customize <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/accounts/customize/settings/?aid=<?php echo $account->id ?>">Settings</a></li>
                            <li><a href="/accounts/customize/stylesheet/?aid=<?php echo $account->id ?>">LESS/CSS</a></li>
                            <li><a href="/accounts/customize/favicon/?aid=<?php echo $account->id ?>">Favicon</a></li>
<!--                            <li><a href="/accounts/customize/ashley-express-shipping-prices/?aid=--><?php //echo $account->id ?><!--">Ashley Express - Shipping Prices</a></li>-->
                        </ul>
                    </li>
                </ul>
                <h3>DNS: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">


<?php if ( $errs ): ?>
    <div class="alert alert-danger"><?php echo $errs ?></div>
<?php endif; ?>

<?php if ( !empty( $cloudflare_zone_id ) ): ?>
    <h4>Records</h4>

    <?php if ( is_array( $records ) ) : ?>
        <p>Your name servers:</p>
        <ul>
            <?php foreach( $zone_details->name_servers as $ns ): ?>
            <li><?php echo $ns; ?></li>
            <?php endforeach; ?>
        </ul>
        <form name="fEditDNS" id="fEditDNS" action="" method="post" err="The records you have entered do not match the type you have selected." role="form">
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
                <tr class="hidden cloudflare">
                   <td>
                       <input type="text" name="changes[][name]" class="form-control disabled" disabled="disabled" placeholder="Domain" />
                       <input class="action disabled" type="hidden" disabled="disabled" name="changes[][action]" value="" />
                   </td>
                   <td>
                       <select name="changes[][type]" class="form-control disabled changes-type" disabled="disabled">
                           <?php
                           $types = array( 'A', 'CNAME', 'MX', 'NS', 'TXT' );

                           foreach ( $types as $type ) {
                               ?>
                               <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                           <?php } ?>
                       </select>
                   </td>
                   <td>
                       <input type="text" name="changes[][ttl]" class="form-control disabled" placeholder="TTL" disabled="disabled" />
                   </td>
                   <td>
                       <textarea name="changes[][content]" class="form-control disabled changes-records" cols="40" rows="3" disabled="disabled"></textarea>
                   </td>
                   <td>
                       <a href="#" class="edit-record cloudflare" title="Edit Record"><i class="fa fa-pencil"></i></a>
                       <a href="#" class="delete-record" title="Delete Record"><i class="fa fa-trash-o"></i></a>
                   </td>
               </tr>
                <?php foreach ( $records as $record ): ?>

                    <tr id="record-<?php echo $record->id; ?>" data-original-type="<?php echo $record->type; ?>">
                        <td>
                            <input type="text" name="changes[<?php echo $record->id; ?>][name]" class="form-control disabled" disabled="disabled" placeholder="Domain" value="<?php echo $record->name; ?>" />
                            <input class="action disabled" type="hidden" disabled="disabled" name="changes[<?php echo $record->id; ?>][action]" value="" />
                        </td>
                        <td>
                            <select name="changes[<?php echo $record->id; ?>][type]" class="form-control disabled changes-type" disabled="disabled">
                                <?php
                                $types = array( 'A', 'CNAME', 'MX', 'NS', 'TXT' );

                                foreach ( $types as $type ) {
                                    $selected = ( $type == $record->type ) ? ' selected="Selected"' : '';
                                    ?>
                                    <option value="<?php echo $type; ?>"<?php echo $selected; ?>><?php echo $type; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="changes[<?php echo $record->id; ?>][ttl]" class="form-control disabled" placeholder="TTL" value="<?php echo $record->ttl; ?>" disabled="disabled" />
                        </td>
                        <td>
                            <textarea name="changes[<?php echo $record->id; ?>][content]" class="form-control disabled changes-records" cols="40" rows="3" disabled="disabled"><?php echo $record->content; ?></textarea>
                        </td>
                        <td>
                            <a href="#" class="edit-record cloudflare" title="Edit Record"><i class="fa fa-pencil"></i></a>
                            <a href="#" class="delete-record" title="Delete Record"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <p><a href="#" id="aAddRecord" class="btn btn-default" title="Add Record">Add Record</a></p>

            <p>
                <button type="submit" class="btn btn-primary pull-right">Save DNS</button>
            </p>

            <?php nonce::field('dns'); ?>
        <?php else: ?>
        <p>Please adjust your name servers to the following:</p>
        <ul>
            <?php foreach( $zone_details->name_servers as $ns ): ?>
            <li><?php echo $ns; ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </form>
<?php elseif ( !empty( $zone_id ) ): ?>

    <a href="<?php echo url::add_query_arg('a', 'transfer'); ?>" class="btn btn-primary btn-lg" title="Transfer to Cloudflare">Transfer to Cloudflare</a>

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
                foreach ( $records['ResourceRecordSets'] as $id => $r ):
                    $no_delete =  $full_domain_name == $r['Name'] && ( 'NS' == $r['Type'] || 'SOA' == $r['Type'] );
                    ?>
                    <tr id="record-<?php echo $id;?>" data-original-type="<?php echo $r['Type']; ?>">
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
                            <!--<input type="text" name="changes[ttl][]" class="form-control disabled" placeholder="TTL" value="<?php echo $r['TTL']; ?>" disabled="disabled" /> -->
                            <select name="changes[ttl][]" class="form-control disabled" placeholder="TTL" disabled="disabled">
                                <option value="">TTL </option>
                                <option value="120">2 minutes </option>
                                <option value="300">5 minutes </option>
                                <option value="600">10 minutes </option>
                                <option value="900">15 minutes </option>
                                <option value="1200">20 minutes </option>
                                <option value="1800">30 minutes </option>
                                <option value="3600"> 1 hour </option>                                
                                <option value="7200"> 2 hours </option>
                                <option value="18000"> 5 hours </option>
                                <option value="43200"> 12 hours </option>
                                <option value="86400"> 1 day </option>                                                                                                                                
                            </select>
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



<div class="modal fade" id="modal-TXT" tabindex="-1" role="dialog" aria-hidden="true" >
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Record: TXT Content</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <input type="hidden" value="" name="record-id" class="record-id">
                        <label>Current Value:</label>                        
                        <pre class="previous-value">

                        </pre>
                        <label>New Value</label>
                        <textarea class="form-control txt-value" value=""  name="txt-value" /></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="modal-MX" tabindex="-1" role="dialog" aria-hidden="true" >
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit Record: MX Content</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <input type="hidden" value="" name="record-id" class="record-id">
                        <label>Server:</label>                        
                        <input type="text" class="form-control mx-server" value=""  name="mx-server" />
                        <label>Priority:</label>
                        <input type="number" class="form-control mx-priority" value=""  name="mx-priority" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
</div>

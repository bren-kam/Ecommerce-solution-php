<?php
/**
 * @package Grey Suit Retail
 * @page Settings > DNS
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
                DNS Zone File
            </header>

            <div class="panel-body">

                <div class="alert alert-warning">
                    <strong>WARNING!</strong><br>
                    Modifying this section can cause various parts or your entire website to go offline. Change at your own risk.
                </div>

                <?php if ( $errs ): ?>
                    <div class="alert alert-danger"><?php echo $errs ?></div>
                <?php endif; ?>

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
                                <tr>
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
                </form>

            <?php endif; ?>
            </div>
        </section>
    </div>
</div>


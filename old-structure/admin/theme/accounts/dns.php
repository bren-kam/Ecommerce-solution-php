<?php
/**
 * @page Edit Account
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Get the website id if there is one
$website_id = ( isset( $_GET['wid'] ) ) ? $_GET['wid'] : false;

// Make sure they have permission to be here
if ( $user['role'] < 10 )
    url::redirect("/accounts/edit/?wid=$website_id");

$w = new Websites;
$v = new Validator;

library('r53');

$r53 = new Route53( config::key('aws_iam-access-key'), config::key('aws_iam-secret-key') );

// Get variables
$website = $w->get_website( $website_id );
$domain_name = url::domain( $website['domain'], false );
$full_domain_name = $domain_name . '.';
$zone_id = $w->get_setting( $website_id, 'r53-zone-id' );

$v->form_name = 'fEditDNS';

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-dns' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		if ( isset( $_POST['changes'] ) && is_array( $_POST['changes'] ) ) {
			$changes = array();
			$change_count = count( $_POST['changes']['name'] );
			
			for( $i = 0; $i < $change_count; $i++ ) {
				// Get the records
				$records = format::trim_deep( explode( "\n", $_POST['changes']['records'][$i] ) );
				
				switch( $_POST['changes']['action'][$i] ) {
					default:
						continue;
					break;
					
					case '1':
						$action = 'CREATE';
					break;
					
					case '0':
						$action = 'DELETE';
					break;
				}
				
				$changes[] = $r53->prepareChange( $action, $_POST['changes']['name'][$i], $_POST['changes']['type'][$i], $_POST['changes']['ttl'][$i], $records );
			}
			
			$response = $r53->changeResourceRecordSets( $zone_id, $changes );
		}
	}
}

if ( isset( $_GET['a'] ) )
switch ( $_GET['a'] ) {
    case 'create':
        if ( !empty( $zone_id ) )
            break;
        
        $zone = $r53->createHostedZone( $domain_name, md5(microtime()) );
	
        // Update the settings
        $w->update_settings( $website_id, array( 'r53-zone-id' => $zone['HostedZone']['Id'] ) );
        
        $zone_id = $zone['HostedZone']['Id'];
        
        $changes = array(
            $r53->prepareChange( 'CREATE', $full_domain_name, 'A', '14400', '199.79.48.138' )
            , $r53->prepareChange( 'CREATE', $full_domain_name, 'MX', '14400', '0 mail.' . $full_domain_name )
            , $r53->prepareChange( 'CREATE', 'mail.' . $full_domain_name, 'A', '14400', '199.79.48.137' )
            , $r53->prepareChange( 'CREATE', 'www.' . $full_domain_name, 'CNAME', '14400', $full_domain_name )
            , $r53->prepareChange( 'CREATE', 'ftp.' . $full_domain_name, 'A', '14400', '199.79.48.137' )
            , $r53->prepareChange( 'CREATE', 'cpanel.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
            , $r53->prepareChange( 'CREATE', 'whm.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
            , $r53->prepareChange( 'CREATE', 'webmail.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
            , $r53->prepareChange( 'CREATE', 'webdisk.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
        );
    
        $response = $r53->changeResourceRecordSets( $zone_id, $changes );
    break;
    
    case 'delete':
        if ( empty( $zone_id ) )
            break;
			
		if ( !$r53->deleteHostedZone( $zone_id ) ) {
			$error = $r53->getError();
			
			if ( 'NoSuchHostedZone' != $error['code'] ) {
				$errs = $error['error'];
				break;
			}
		}
	
        // Update the settings
        $w->update_settings( $website_id, array( 'r53-zone-id' => '' ) );

        $zone_id = '';
    break;
}

// DNS Sort
function dns_sort( $a, $b ) {
    if ( $a['Type'] == $b['Type'] )
        return 0;

	if ( 'SOA' == $b['Type'] && 'SOA' != $a['Type'] || 'NS' == $b['Type'] && 'NS' != $a['Type'] )
		return 1;

	if ( 'SOA' == $a['Type'] && 'SOA' != $b['Type'] || 'NS' == $a['Type'] && 'NS' != $b['Type'] )
		return -1;

    return strcmp( $a['Type'], $b['Type'] );
}

if ( !empty( $zone_id ) ) {
    $zone = $r53->getHostedZone( $zone_id );
    
    $records = $r53->listResourceRecordSets( $zone_id );
    
    usort( $records['ResourceRecordSets'], 'dns_sort' );
}

css( 'form', 'accounts/dns' );
javascript( 'validator', 'jquery', 'accounts/dns' );

$selected = 'accounts';
$title = _('Edit DNS Records') . ' | ' . TITLE;
get_header();
?>

<div id="content">
    <h1><?php echo _('DNS Zone File'), ': ', $website['title']; ?></h1>
	<br clear="all" /><br />
	<?php $sidebar_emails = true; get_sidebar( 'accounts/', 'dns' ); ?>
	<div id="subcontent">
		<?php 
		if ( isset( $response ) ) { 
			if ( $response ) {
			?>
				<p class="success"><?php echo _('Your DNS Zone file has been successfully updated!'); ?></p>
			<?php } else { ?>
				<p class="error"><?php echo _('There was an error while trying to update your DNS Zone file. Please try again.'); ?></p>
			<?php
			}
		}
        
		if ( $errs )
			echo "<p class='error'>$errs</p>";
		
        if ( !empty( $zone_id ) ) {
		?>
            <a href="<?php echo url::add_query_arg( 'a', 'delete' ); ?>" class="button float-right" title="<?php echo _('Delete Zone File'); ?>"><?php echo _('Delete Zone File'); ?></a>
            <br class="clr" /><br />
            <form name="fEditDNS" id="fEditDNS" action="" method="post">
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
                                                <a href="javascript:;" class="edit-record" title="<?php echo _('Edit Record'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit Record'); ?>" /></a>
                                                <a href="javascript:;" class="delete-record" title="<?php echo _('Delete Record'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete Record'); ?>" /></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr class="last"><td colspan="4"><a href="javascript:;" id="aAddRecord" title="<?php echo _('Add Record'); ?>"><?php echo _('Add Record'); ?></a></td></tr>
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
            <?php nonce::field('update-dns'); ?>
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
                        <a href="javascript:;" class="edit-record" title="<?php echo _('Edit Record'); ?>"><img src="/images/icons/edit.png" width="15" height="17" alt="<?php echo _('Edit Record'); ?>" /></a>
                        <a href="javascript:;" class="delete-record" title="<?php echo _('Delete Record'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Delete Record'); ?>" /></a>
                    </td>
                </tr>
            </table>
        <?php } else { ?>
            <p><a href="<?php echo url::add_query_arg('a', 'create'); ?>" class="button" title="<?php echo _('Create Zone File'); ?>"><?php echo _('Create Zone File'); ?></a></p>
            <br /><br />
            <br /><br />
            <br /><br />
        <?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>

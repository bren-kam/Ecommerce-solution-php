<?php
/**
 * @page Edit Account
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;
$v = new Validator;

library('r53');

$r53 = new Route53( config::key('aws_iam-access-key'), config::key('aws_iam-secret-key') );

// Get the website id if there is one
$website_id = ( isset( $_GET['wid'] ) ) ? $_GET['wid'] : false;

// Get variables
$website = $w->get_website( $website_id );
$domain_name = url::domain( $website['domain'], false );
$zone_id = $w->get_setting( $website_id, 'r53-zone-id' );

$v->form_name = 'fEditDNS';

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-dns' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		if ( isset( $_POST['changes'] ) && is_array( $_POST['changes'] ) ) {
			$changes = array();
			// Default TTL = 14400
			foreach ( $_POST['changes'] as $change ) {
				$changes[] = $r53->prepareChange( $change['action'], $change['name'], $change['type'], $change['ttl'], $change['records'] );
			}
			
			$response = $r53->changeResourceRecordSets( $zone_id, $changes );
		}
	}
}

// Get the zone
if ( is_null( $zone_id ) || empty( $zone_id ) ) {
	$zone = $r53->createHostedZone( $domain_name, md5(microtime()) );
	
	// Update the settings
	$w->update_settings( $website_id, array( 'r53-zone-id' => $zone['HostedZone']['Id'] ) );
} else {
	$zone = $r53->getHostedZone( $zone_id );
}

$records = $r53->listResourceRecordSets( $zone_id );

css( 'form', 'accounts/edit' );
javascript( 'validator', 'jquery', 'accounts/edit' );

$selected = 'accounts';
$title = _('Edit DNS Records') . ' | ' . TITLE;
get_header();
?>

<div id="content">
    <h1><?php echo _('DNS Zone File'), ': ', $website['title']; ?></h1>
	<br clear="all" /><br />
	<?php $sidebar_emails = true; get_sidebar( 'accounts/', 'dns' ); ?>
	<div id="subcontent">
		<form name="fEditDNS" action="" method="post">
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
								<th><?php echo _('Value'); ?></th>
								<th><?php echo _('TTL'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$record_count = count( $records['ResourceRecordSets'] );
							$i = 0;
							
							if ( is_array( $records['ResourceRecordSets'] ) )
							foreach ( $records['ResourceRecordSets'] as $r ) {
								$i++;
								?>
								<tr<?php if ( $i == $record_count ) echo ' class="last"'; ?>>
									<td><?php echo $r['Name']; ?></td>
									<td><?php echo $r['Type']; ?></td>
									<td><?php echo $r['TTL']; ?></td>
									<td><?php echo implode( "<br />\n", $r['ResourceRecords'] ); ?></td>
								</tr>
							<?php } ?>
							<tr><td colspan="2"><a href="javascript:;" id="aAddRecord" title="<?php echo _('Add Record'); ?>"><?php echo _('Add Record'); ?></a></td></tr>
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
		</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>

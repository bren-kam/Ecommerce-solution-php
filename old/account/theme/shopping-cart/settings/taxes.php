<?php
/**
 * @page Taxes
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$w = new Websites;

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'taxes' )  ) {
    $zip_codes = array();

    if ( isset( $_POST['zip_codes'] ) )
    foreach ( $_POST['zip_codes'] as $state => $taxes ) {
        $rows = explode( "\n", $taxes );
        foreach ( $rows as $r ) {
            list( $zip, $cost ) = explode( ' ', str_replace("\t", ' ', $r ) );
            $zip_codes[$state][$zip] = $cost;
        }
    }

	$success = $w->update_settings( array( 'taxes' => serialize( array( 'states' => $_POST['states'], 'zip_codes' => $zip_codes ) ) ) );
}

// Define variables
$taxes = $w->get_settings( 'taxes' );
$taxes = unserialize( html_entity_decode( $taxes['taxes'] ) );
$states = data::states( false );

javascript( 'shopping-cart/settings/taxes' );

$title = _('Tax Settings') . ' | ' . _('Shopping Cart') . ' | ' . TITLE;
$page = 'settings';
get_header();
?>

<div id="content">
	<h1><?php echo _('Taxes'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/', 'settings' ); ?>
	<div id="subcontent">
		<?php if ( $success ) echo '<p class="success">' . _('Tax settings successfully edited') . '</p>'; ?>
		<form name="fTaxes" id="fTaxes" action="/shopping-cart/settings/taxes/" method="post">
			<table id="tWebsiteTaxes" width="700">
				<tr>
					<th width="40%"><strong><?php echo _('State'); ?></strong></th>
					<th><strong><?php echo _('Cost'); ?></strong></th>
					<th width="40%"><strong><?php echo _('Actions'); ?></strong></th>
				</tr>
				<?php foreach ( $taxes['states'] as $abbr => $tax ) { ?>
				<tr id="trTax<?php echo $abbr; ?>">
					<td>
                        <a href="javascript:;" class="zip-codes" title="<?php echo _('Edit Tax Zip Codes'); ?>"><span><?php echo $states[$abbr]; ?></span></a>
                        <textarea name="zip_codes[<?php echo $abbr; ?>]" class="hidden" col="50" rows="3" tmpval="[Zip] [Cost]"><?php
                            if ( isset( $taxes['zip_codes'][$abbr] ) )
                            foreach ( $taxes['zip_codes'][$abbr] as $zip => $cost ) {
                                echo $zip, ' ', $cost, "\n";
                            }
                        ?></textarea>
                    </td>
					<td><input type="text" class="tb" name="states[<?php echo $abbr; ?>]" id="tState<?php echo $abbr; ?>" value="<?php echo $tax; ?>" maxlength="5" /></td>
					<td><a href="javascript:;" class="delete-state" id="aDeleteTax<?php echo $abbr; ?>" title="<?php echo _('Delete Tax'); ?>"><img width="15" height="17" alt="<?php echo _('Delete'); ?>" src="/images/icons/x.png"></a></td>
				</tr>
				<?php } ?>
				<tr id="trAddTax">
					<td>
						<select name="sState" id="sState">
							<option value="">-- <?php echo _('Select a State'); ?> --</option>
							<?php 
							foreach ( $states as $key => $state ) {
							?>
							<option class="<?php if ( array_key_exists( $key, $taxes['states'] ) ) echo ' hidden'; ?>" value="<?php echo $key; ?>"><?php echo $state; ?></option>
							<?php } ?>
						</select>
					</td>
					<td><input type="text" class="tb" name="tAmount" id="tAmount" maxlength="5" tmpval="<?php echo _('Amount'); ?>" /></td>
					<td><a href="javascript:;" class="button" id="aAddTax" title="<?php echo _('Add Tax'); ?>" error="<?php echo _('Please enter in a state and tax amount.'); ?>"><?php echo _('Add Tax'); ?></a></td>
				</tr>
			</table>
			<br />
			<input type="submit" class="button" value="<?php echo _('Save Changes'); ?>" />
			<?php nonce::field('taxes'); ?>
		</form>
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
    </div>
	<br /><br />
</div>

<?php get_footer(); ?>
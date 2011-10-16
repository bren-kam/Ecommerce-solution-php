<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$sc = new Shopping_Cart;
$v = new Validator;

$success = false;

$v->form_name = 'fAddEditShipping';
$v->add_validation( 'tName', 'req', _('The "Name" field is required' ) );
$v->add_validation( 'sMethod', 'req', _('Please select a shipping method' ) );
$v->add_validation( 'tAmount', 'req', _('Please enter a valid amount' ) );

$data = $_POST;
$success = false;
$zip_success = true;

if( !empty( $data ) ) {	
	$a = $v->Validate();
	if ( $a ) $errs[] = $a;
	
	if( empty( $errs ) )
	{
		$zips = $data['tZip'];
		
		if( $data['hID'] != '' ) {
			$success = $sc->update_shipping_method( $_POST['tName'], $_POST['sMethod'], $_POST['tAmount'], $_POST['hID'] );
			$success *= $sc->update_shipping_zip_codes( $_POST['hID'], $zips );
		} else {
			$insert_id = $sc->add_shipping_method( $_POST['tName'], $_POST['sMethod'], $_POST['tAmount'], $_POST['hID'] );
			$success = $sc->update_shipping_zip_codes( $insert_id, $zips );
		}
	} else {
		$zip_success = false;
	}
}

if( $success ) url::redirect( '/shopping-cart/shipping/' );

$id = ( isset( $_GET['wsmid'] ) ) ? $_GET['wsmid'] : false;

$methods = $sc->get_shipping_methods( $user['website']['website_id'] );

list( $name, $method, $amount ) = '';

foreach( $methods as $m ) {
	if( $m['website_shipping_method_id'] == $id ) {
		$name = $m['name'];
		$method = $m['method'];
		$amount = $m['amount'];
		break;
	}
}

$zips = ( $zip_success ) ? ( ( $id ) ? $sc->get_shipping_zip_codes( $id ) : false ) : $_POST['tZip'];
if ( $zip_success && ( $zips[0] == '' ) ) $zips = false;

$title = _('Shopping Cart - ' . ( ( $id ) ? 'Edit' : 'Add' ) . ' Shipping Method') . ' | ' . TITLE;
$page = 'shipping';
get_header();
javascript( '/shopping-cart/add-edit-shipping' );
?>

<div id="content">
	<h1><?php echo _( ( ( $id ) ? 'Edit' : 'Add' ) . ' Shipping Method'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
		<div id="dErrors">
        <?php 
			foreach( $errs as $err ) {
				echo "<p class='error'>" . $err . "</p>";
			}
		?>
		</div>
        <form id="fAddEditShipping" action="/shopping-cart/add-edit-shipping/" method="post">
            <table>
                <tr>
                    <td><label for="tName"><?php echo _('Name: '); ?></label></td>
                    <td><input name="tName" tmpval="Method Name..." class="tb" maxlength="5" type="text" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $name; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="sMethod"><?php echo _('Method: '); ?></label></td>
                    <td>
                        <select name="sMethod" class="tb" value="<?php echo ( !$success && isset( $_POST['sMethod'] ) ) ? $_POST['sMethod'] : $method; ?>">
                            <option value=""><?php echo _('--Select a Method--'); ?></option>
                            <option <?php if( $method == 'Flat Rate' ) echo 'selected="selected"'; ?>value="Flat Rate"><?php echo _('Flat Rate'); ?></option>
                            <option <?php if( $method == 'Percentage' ) echo 'selected="selected"'; ?>value="Percentage"><?php echo _('Percentage'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
					<td><label for="tAmount"/><?php echo _('Amount: '); ?></td>
					<td><input name="tAmount" tmpval="Enter Amount..." class="tb" type="text" value="<?php echo ( !$success && isset( $_POST['tAmount'] ) ) ? $_POST['tAmount'] : $amount; ?>" /></td>
				</tr>
                <tr>
                	<td>&nbsp;</td><td></td>
                </tr>
                <tr>
                	<td><label><?php echo _('Zip Codes:'); ?></label></td>
                    <td>
                    	<?php foreach( $zips as $zip ) { ?>
                        <span id="sZip<?php echo $zip; ?>">
                        	<input type="text" class="tb" maxlength="5" name="tZip[]" value="<?php echo $zip; ?>"/>&nbsp;
                            <a href="#" id="aDeleteZip<?php echo $zip; ?>" class="delete-zip">
                            	<img width="15" height="17" alt="Delete" src="/images/icons/x.png" />
							</a>
                            <br/>
						</span>
						<?php } ?>
                        <br id="brInsertZipAbove"/>
                        <input type="text" class="tb" maxlength="5" id="tAddNewZip" tmpval="<?php echo _('New Zip...'); ?>"/> <a href="#" class="button" id="aAddNewZip"><?php echo _('Add Zip'); ?></a>
                    </td>
                </tr>
			</table>
            <br/><br/>
			<input type="hidden" name="hID" value="<?php echo ( $_POST['hID'] ) ? $_POST['hID'] : $_GET['wsmid']; ?>" />
		<input type="submit" class="button" value="Save Shipping Method" />
		</form>	
		<?php echo $form_validation; ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
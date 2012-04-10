<?php
/**
 * @page Craigslist
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$sc = new Shopping_Cart;

$v = new Validator;
$uid = $_GET['uid'];

$v->form_name = 'fAddUser';
$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email') );
$v->add_validation( 'hAvailable', 'val=1', _('Please choose an available email address') );

$v->add_validation( 'pPassword|pVerifyPassword', 'match', _('The Password fields must match' ) );

$v->add_validation( 'tBillingZip', 'zip', _('The "Billing Information - Zip Code" field must contain a valid zip code' ) );
$v->add_validation( 'tShippingZip', 'zip', _('The "Shipping Information - Zip Code" field must contain a valid zip code' ) );

$data = $_POST;

$success = false;

if ( !empty( $data ) ) {
	
	$errs = $v->validate();
	
	if ( empty( $errs ) )
		$success = $sc->edit_user( $_POST['hUid'], $_POST['tEmail'], ( ( $_POST['pPassword'] == '' ) ? '' : $_POST['pPassword'] ), $_POST['tBillingFirstName'], $_POST['tBillingLastName'], $_POST['tBillingAddress'], $_POST['tBillingAddress2'], $_POST['tBillingCity'], $_POST['sBillingState'], $_POST['tBillingZip'], $_POST['tBillingPhone'], $_POST['tBillingAltPhone'], $_POST['tShippingFirstName'], $_POST['tShippingLastName'], $_POST['tShippingAddress'], $_POST['tShippingAddress2'], $_POST['tShippingCity'], $_POST['sShippingState'], $_POST['tShippingZip'], $_POST['sStatus'] );
}

$u = $sc->get_user( $uid, $user['website']['website_id'] );
if ( !$u ) url::redirect( '/shopping-cart/add-user/' );

css( 'shopping-cart/view' );
javascript( 'shopping-cart/view' );

$title = _('Shopping Cart - Edit User') . ' | ' . TITLE;
$page = 'users';
get_header();
?>

<div id="content">
	<h1><?php echo _('Edit User'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
		<div id="dErrors">
        <?php 
			if ( isset( $errs ) )
				echo "<p class='error'>$errs</p>";
			
			if ( $success ) 
				echo '<p class="success">User successfully edited!</p>';
		?>
		</div>
	    <form name="fEditUser" action="/shopping-cart/edit-user/?uid=<?php echo $uid; ?>" method="post">
        <input name="hUid" type="hidden" value="<?php echo $uid; ?>" />
        <table width="100%">
        	<tr>
            	<td><h2>Personal Information</h2></td>
                <td></td>
                <td><h2>Account Information</h2></td>
                <td></td>
         	</tr>
        	<tr>
            	<td><label for="tEmail">Email: <span class="red">*</span></label></td>
                <td><input name="tEmail" type="text" class="tb" value="<?php echo( isset( $u['email'] ) ) ? $u['email'] : ''; ?>"/></td>
                <td><label for="sStatus">Status:</label></td>
                <td>
                	<select name="sStatus">
                        <option value="1"<?php if ( $u['status'] == '1' ) echo ' selected="selected"'; ?>>Active</option>
                        <option value="0"<?php if ( $u['status'] == '0' || $u['status'] == '' ) echo ' selected="selected"'; ?>>Inactive</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="pPassword">Password: <span class="red">*</span></label></td>
                <td><input type="password" class="tb" name="pPassword" id="pPassword" maxlength="32" autocomplete="off" tabindex="2" /></td>
			</tr>
            <tr>
                <td><label for="pVerifyPassword">Verify Password: <span class="red">*</span></label></td>
                <td><input type="password" class="tb" name="pVerifyPassword" maxlength="32" tabindex="3" /></td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
                <th colspan="2"><h2>Billing Information</h2><br/></th>
                <th colspan="2"><h2>Shipping Information</h2></th>
            </tr>
            <tr>
                <td colspan="2" width="55%">&nbsp;</td>
                <td colspan="2"><input type="checkbox" id="cbSameShipping" tabindex="12" /> <label for="cbAddUserSameShipping"> Shipping is the same as Billing</label></td>
            </tr>
            <tr>
                <td width="20%"><label for="tBillingFirstName">First Name</label></td>
                <td width="35%"><input type="text" class="tb" name="tBillingFirstName" id="tBillingFirstName" maxlength="50" tabindex="5" value="<?php echo( isset( $u['billing_first_name'] ) ) ? $u['billing_first_name'] : ''; ?>" /></td>
                <td width="20%"><label for="tShippingFirstName">First Name</label></td>
                <td width="25%"><input type="text" class="tb" name="tShippingFirstName" id="tShippingFirstName" maxlength="50" tabindex="13" value="<?php echo( isset( $u['shipping_first_name'] ) ) ? $u['shipping_first_name'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingLastName">Last Name</label></td>
                <td><input type="text" class="tb" name="tBillingLastName" id="tBillingLastName" maxlength="50" tabindex="6" value="<?php echo( isset( $u['billing_last_name'] ) ) ? $u['billing_last_name'] : ''; ?>" /></td>
                <td><label for="tShippingLastName">Last Name</label></td>
                <td><input type="text" class="tb" name="tShippingLastName" id="tShippingLastName" maxlength="50" tabindex="14" value="<?php echo( isset( $u['shipping_last_name'] ) ) ? $u['shipping_last_name'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingAddress">Address 1</label></td>
                <td><input type="text" class="tb" name="tBillingAddress" id="tBillingAddress" maxlength="100" tabindex="7" value="<?php echo( isset( $u['billing_address1'] ) ) ? $u['billing_address1'] : ''; ?>" /></td>
                <td><label for="tShippingAddress">Address 1</label></td>
                <td><input type="text" class="tb" name="tShippingAddress" id="tShippingAddress" maxlength="100" tabindex="15" value="<?php echo( isset( $u['shipping_address1'] ) ) ? $u['shipping_address1'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingAddress2">Address 2</label></td>
                <td><input type="text" class="tb" name="tBillingAddress2" id="tBillingAddress2" maxlength="100" tabindex="8" value="<?php echo( isset( $u['billing_address2'] ) ) ? $u['billing_address2'] : ''; ?>" /></td>
                <td><label for="tShippingAddress2">Address 2</label></td>
                <td><input type="text" class="tb" name="tShippingAddress2" id="tShippingAddress2" maxlength="100" tabindex="16" value="<?php echo( isset( $u['shipping_address2'] ) ) ? $u['shipping_address2'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingCity">City</label></td>
                <td><input type="text" class="tb" name="tBillingCity" id="tBillingCity" maxlength="100" tabindex="9" value="<?php echo( isset( $u['billing_city'] ) ) ? $u['billing_city'] : ''; ?>" /></td>
                <td><label for="tShippingCity">City</label></td>
                <td><input type="text" class="tb" name="tShippingCity" id="tShippingCity" maxlength="100" tabindex="17" value="<?php echo( isset( $u['shipping_city'] ) ) ? $u['shipping_city'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td><label for="sBillingState">State</label></td>
                <td>
                    <select name="sBillingState" id="sBillingState" tabindex="10">
                        <option value="">-- Select State --</option>
                        <?php 
                        $billing_state = ( $u['billing_state'] ) ? $u['billing_state'] : '';
                        data::states( true, $billing_state ); 
                        ?>
                    </select>
                </td>
                <td><label for="sShippingState">State</label></td>
                <td>
                    <select name="sShippingState" id="sShippingState" tabindex="18" >
                        <option value="">-- Select State --</option>
                        <?php 
                        $shipping_state = ( isset( $u['shipping_state'] ) ) ? $u['shipping_state'] : '';
                        data::states( true, $shipping_state ); 
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="tBillingZip">Zip Code</label></td>
                <td><input type="text" class="tb" name="tBillingZip" id="tBillingZip" maxlength="10" tabindex="11" value="<?php echo( isset( $u['billing_zip'] ) ) ? $u['billing_zip'] : ''; ?>" /></td>
                <td><label for="tShippingZip">Zip Code</label></td>
                <td><input type="text" class="tb" name="tShippingZip" id="tShippingZip" maxlength="10" tabindex="19" value="<?php echo( isset( $u['shipping_zip'] ) ) ? $u['shipping_zip'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingPhone">Phone Number</label></td>
                <td><input type="text" class="tb" name="tBillingPhone" id="tBillingPhone" maxlength="10" tabindex="11" value="<?php echo( isset( $u['billing_phone'] ) ) ? $u['billing_phone'] : ''; ?>" /></td>
                <td><label for="tBillingAltPhone">Alt. Phone Number</label></td>
                <td><input type="text" class="tb" name="tBillingAltPhone" id="tBillingAltPhone" maxlength="10" tabindex="19" value="<?php echo( isset( $u['billing_alt_phone'] ) ) ? $u['billing_alt_phone'] : ''; ?>" /></td>
            </tr>
            <tr><td colspan="5">&nbsp;</td></tr>
            <tr><td colspan="5" style="text-align:center"><input type="submit" name="iSubmit" value="Edit User" class="button" tabindex="20" /></td></tr>
        </table>
        <input type="hidden" name="hAvailable" id="hAvailable" value="0" />
        <?php 
        nonce::field( 'check-email' );
        nonce::field( 'add-user' ); 
        ?>
	</form>
	<?php echo $form_validation; ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$sc = new Shopping_Cart;
$v = new Validator;

$success = false;

$v->form_name = 'fAddUser';
$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email') );
$v->add_validation( 'hAvailable', 'val=1', _('Please choose an available email address') );

$v->add_validation( 'pPassword', 'req', _('The "Password" field is required' ) );
$v->add_validation( 'pPassword', 'minlen=4', _('The "Password" field must be at least 4 characters long' ) );
$v->add_validation( 'pPassword|pVerifyPassword', 'match', _('The Password fields must match' ) );

$v->add_validation( 'tBillingZip', 'zip', _('The "Billing Information - Zip Code" field must contain a valid zip code' ) );
$v->add_validation( 'tShippingZip', 'zip', _('The "Shipping Information - Zip Code" field must contain a valid zip code' ) );

// Add validation
add_footer( $v->js_validation() );

$data = $_POST;
$success = false;

if ( !empty( $data ) ) {
	
	if ( !$sc->check_email( $user['website']['website_id'], $_POST['tEmail'] ) ) 
		$errs[] = _('Please choose an available email address');
	
	if ( $_POST['pPassword'] != $_POST['pVerifyPassword'] )
		$errs[] = _('Passwords must match.');
	
	$a = $v->Validate();
	if ( $a ) $errs[] = $a;
	
	if ( empty( $errs ) )
		$success = $sc->add_user( $user['website']['website_id'], $_POST['tEmail'], $_POST['pPassword'], $_POST['tBillingFirstName'], $_POST['tBillingLastName'], $_POST['tBillingAddress'], $_POST['tBillingAddress2'], $_POST['tBillingCity'], $_POST['sBillingState'], $_POST['tBillingZip'], $_POST['tBillingPhone'], $_POST['tBillingAltPhone'], $_POST['tShippingFirstName'], $_POST['tShippingLastName'], $_POST['tShippingAddress'], $_POST['tShippingAddress2'], $_POST['tShippingCity'], $_POST['sShippingState'], $_POST['tShippingZip'], $_POST['sStatus'] );	
}


css( 'shopping-cart/view' );
javascript( 'shopping-cart/view' );

$title = _('Shopping Cart - Add User') . ' | ' . TITLE;
$page = 'users';
get_header();
?>

<div id="content">
	<h1><?php echo _('Add User'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'shopping-cart/' ); ?>
	<div id="subcontent">
		<div id="dErrors">
        <?php 
			foreach ( $errs as $err ) {
				echo "<p class='error'>" . $err . "</p>";
			}
			if ( $success ) echo "<p class='success'>User successfully added!</p>";
		?>
		</div>
	    <form name="fAddUser" action="/shopping-cart/add-user/" method="post">
        <table width="100%">
        	<tr>
            	<td><h2>Personal Information</h2></td>
                <td></td>
                <td><h2>Account Information</h2></td>
                <td></td>
         	</tr>
        	<tr>
            	<td><label for="tEmail">Email: <span class="red">*</span></label></td>
                <td><input name="tEmail" class="tb" type="text" /></td>
                <td><label for="sStatus">Status:</label></td>
                <td>
                	<select name="sStatus">
                        <option value="1"<?php if ( !$success && isset( $_POST['sStatus'] ) && 1 == $_POST['sStatus'] || !isset( $_POST['sStatus'] ) ) echo ' selected="selected"'; ?>>Active</option>
                        <option value="0"<?php if ( !$success && isset( $_POST['sStatus'] ) && 0 == $_POST['sStatus'] ) echo ' selected="selected"'; ?>>Inactive</option>
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
                <td width="35%"><input class="tb" type="text" name="tBillingFirstName" id="tBillingFirstName" maxlength="50" tabindex="5" value="<?php if ( !$success && isset( $_POST['tBillingFirstName'] ) ) echo $_POST['tBillingFirstName']; ?>" /></td>
                <td width="20%"><label for="tShippingFirstName">First Name</label></td>
                <td width="25%"><input class="tb" type="text" name="tShippingFirstName" id="tShippingFirstName" maxlength="50" tabindex="13" value="<?php if ( !$success && isset( $_POST['tShippingFirstName'] ) ) echo $_POST['tShippingFirstName']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingLastName">Last Name</label></td>
                <td><input type="text" class="tb" name="tBillingLastName" id="tBillingLastName" maxlength="50" tabindex="6" value="<?php if ( !$success && isset( $_POST['tBillingLastName'] ) ) echo $_POST['tBillingLastName']; ?>" /></td>
                <td><label for="tShippingLastName">Last Name</label></td>
                <td><input type="text" class="tb" name="tShippingLastName" id="tShippingLastName" maxlength="50" tabindex="14" value="<?php if ( !$success && isset( $_POST['tShippingLastName'] ) ) echo $_POST['tShippingLastName']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingAddress">Address 1</label></td>
                <td><input type="text" class="tb" name="tBillingAddress" id="tBillingAddress" maxlength="100" tabindex="7" value="<?php if ( !$success && isset( $_POST['tBillingAddress'] ) ) echo $_POST['tBillingAddress']; ?>" /></td>
                <td><label for="tShippingAddress">Address 1</label></td>
                <td><input type="text" class="tb" name="tShippingAddress" id="tShippingAddress" maxlength="100" tabindex="15" value="<?php if ( !$success && isset( $_POST['tShippingAddress'] ) ) echo $_POST['tShippingAddress']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingAddress2">Address 2</label></td>
                <td><input type="text" class="tb" name="tBillingAddress2" id="tBillingAddress2" maxlength="100" tabindex="8" value="<?php if ( !$success && isset( $_POST['tBillingAddress2'] ) ) echo $_POST['tBillingAddress2']; ?>" /></td>
                <td><label for="tShippingAddress2">Address 2</label></td>
                <td><input type="text" class="tb" name="tShippingAddress2" id="tShippingAddress2" maxlength="100" tabindex="16" value="<?php if ( !$success && isset( $_POST['tShippingAddress2'] ) ) echo $_POST['tShippingAddress2']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingCity">City</label></td>
                <td><input type="text" class="tb" name="tBillingCity" id="tBillingCity" maxlength="100" tabindex="9" value="<?php if ( !$success && isset( $_POST['tBillingCity'] ) ) echo $_POST['tBillingCity']; ?>" /></td>
                <td><label for="tShippingCity">City</label></td>
                <td><input type="text" class="tb" name="tShippingCity" id="tShippingCity" maxlength="100" tabindex="17" value="<?php if ( !$success && isset( $_POST['tShippingCity'] ) ) echo $_POST['tShippingCity']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="sBillingState">State</label></td>
                <td>
                    <select name="sBillingState" id="sBillingState" tabindex="10">
                        <option value="">-- Select State --</option>
                        <?php 
                        $billing_state = ( !$success && isset( $_POST['sBillingState'] ) ) ? $_POST['sBillingState'] : '';
                        data::states( true, $billing_state ); 
                        ?>
                    </select>
                </td>
                <td><label for="sShippingState">State</label></td>
                <td>
                    <select name="sShippingState" id="sShippingState" tabindex="18">
                        <option value="">-- Select State --</option>
                        <?php 
                        $shipping_state = ( !$success && isset( $_POST['sShippingState'] ) ) ? $_POST['sShippingState'] : '';
                        data::states( true, $shipping_state ); 
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="tBillingZip">Zip Code</label></td>
                <td><input type="text" class="tb" name="tBillingZip" id="tBillingZip" maxlength="10" tabindex="11" value="<?php if ( !$success && isset( $_POST['tBillingZip'] ) ) echo $_POST['tBillingZip']; ?>" /></td>
                <td><label for="tShippingZip">Zip Code</label></td>
                <td><input type="text" class="tb" name="tShippingZip" id="tShippingZip" maxlength="10" tabindex="19" value="<?php if ( !$success && isset( $_POST['tShippingZip'] ) ) echo $_POST['tShippingZip']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="tBillingPhone">Phone Number</label></td>
                <td><input type="text" class="tb" name="tBillingPhone" id="tBillingPhone" maxlength="10" tabindex="11" value="<?php if ( !$success && isset( $_POST['tBillingPhone'] ) ) echo $_POST['tBillingZip']; ?>" /></td>
                <td><label for="tBillingAltPhone">Alt. Phone Number</label></td>
                <td><input type="text" class="tb" name="tBillingAltPhone" id="tBillingAltPhone" maxlength="10" tabindex="19" value="<?php if ( !$success && isset( $_POST['tBillingAltPhone'] ) ) echo $_POST['tShippingZip']; ?>" /></td>
            </tr>
            <tr><td colspan="5">&nbsp;</td></tr>
            <tr><td colspan="5" style="text-align:center"><input type="submit" name="iSubmit" value="Add User" class="button" tabindex="20" /></td></tr>
        </table>
        <input type="hidden" name="hAvailable" id="hAvailable" value="0" />
        <?php 
        nonce::field( 'check-email' );
        nonce::field( 'add-user' ); 
        ?>
	</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
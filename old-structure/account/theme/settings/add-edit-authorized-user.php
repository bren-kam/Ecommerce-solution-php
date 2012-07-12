<?php
/**
 * @page Add / Edit Authorized Users
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$au = new Authorized_Users;

$authorized_user_id = ( isset( $_GET['uid'] ) ) ? $_GET['uid'] : false;

if ( !$authorized_user_id ) {
    $v = new Validator();
    $v->form_name = 'fAddEditAuthorizedUser';
    $v->add_validation( 'tName', 'req', _('The "Name" field is required') );

    $v->add_validation( 'tEmail', 'req' , _('The "Email" field is required') );
    $v->add_validation( 'tEmail' , 'email' , _('The "Email" field must contain a valid email') );

    // Add validation
    add_footer( $v->js_validation() );
}

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-authorized-user' ) ) {
	$errs = ( $authorized_user_id ) ? '' : $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $authorized_user_id ) {
			// Update user
			$success = $au->update( $authorized_user_id, $_POST['cbPages'], $_POST['cbProducts'], $_POST['cbAnalytics'], $_POST['cbBlog'], $_POST['cbEmailMarketing'], $_POST['cbShoppingCart'] );
		} else {
			if ( $user['role'] >= 7 ) {
				$role = ( 'representative' == $_POST['sRole'] ) ? 6 : 1;
			} else {
				$role = 1;
			}
			
			// Add user
			$success = $au->create( $_POST['tName'], $_POST['tEmail'], $_POST['cbPages'], $_POST['cbProducts'], $_POST['cbAnalytics'], $_POST['cbBlog'], $_POST['cbEmailMarketing'], $_POST['cbShoppingCart'], $role );
		}
	}
	
	if ( !$errs && !$success ) 
		$errs = 'Could not create authorized user. This email may already exist for another user in the system.';
}

// Get the authorized user if necessary
if ( $authorized_user_id || $success ) {
    $authorized_user = ( $authorized_user_id ) ? $au->get( $authorized_user_id ) : $au->get( $success );
} else {
	$authorized_user = array(
		'contact_name' => ''
        , 'email' => ''
		, 'pages' => ''
		, 'products' => ''
		, 'analytics' => ''
		, 'blog' => ''
		, 'email_marketing' => ''
		, 'shopping_cart' => ''
	);
}

$sub_title = ( $authorized_user_id ) ? _('Edit Authorized User') : _('Add Authorized User');
$title =  "$sub_title | " . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'settings/', 'authorized_users' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $authorized_user_id ) ? _('Your authorized user has been updated successfully!') : _('Your authorized user has been added successfully!'); ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$authorized_user_id )
			$authorized_user_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditAuthorizedUser" action="/settings/add-edit-authorized-user/<?php if ( $authorized_user_id ) echo "?uid=$authorized_user_id"; ?>" method="post">
            <table>
                <tr>
                    <td><label for="tName"><?php echo _('Name'); ?>: <span class="red">*</span></label></td>
                    <td>
                        <?php if ( !$authorized_user_id ) { ?>
                            <input type="text" class="tb" name="tName" id="tName" value="<?php echo $authorized_user['contact_name']; ?>" />
                        <?php
                        } else {
                            echo $authorized_user['contact_name'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="tEmail"><?php echo _('Email'); ?>: <span class="red">*</span></label></td>
                    <td>
                        <?php if ( !$authorized_user_id ) { ?>
                            <input type="text" class="tb" name="tEmail" id="tEmail" value="<?php echo $authorized_user['email']; ?>" />
                        <?php
                        } else {
                            echo $authorized_user['email'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="top"><label><?php echo _('Section permissions:'); ?></label></td>
                    <td>
                        <input type="checkbox" class="cb" name="cbPages" id="cbPages" value="1"<?php if ( '1' == $authorized_user['pages'] ) echo ' checked="checked"'; ?> /> <label for="cbPages"><?php echo _('Pages'); ?></label><br />
                        <input type="checkbox" class="cb" name="cbProducts" id="cbProducts" value="1"<?php if ( '1' == $authorized_user['products'] ) echo ' checked="checked"'; ?> /> <label for="cbProducts"><?php echo _('Products'); ?></label><br />
                        <input type="checkbox" class="cb" name="cbAnalytics" id="cbAnalytics" value="1"<?php if ( '1' == $authorized_user['analytics'] ) echo ' checked="checked"'; ?> /> <label for="cbAnalytics"><?php echo _('Analytics'); ?></label><br />
                        <input type="checkbox" class="cb" name="cbBlog" id="cbBlog" value="1"<?php if ( '1' == $authorized_user['blog'] ) echo ' checked="checked"'; ?> /> <label for="cbBlog"><?php echo _('Blog'); ?></label><br />
                        <input type="checkbox" class="cb" name="cbEmailMarketing" id="cbEmailMarketing" value="1"<?php if ( '1' == $authorized_user['email_marketing'] ) echo ' checked="checked"'; ?> /> <label for="cbEmailMarketing"><?php echo _('Email Marketing'); ?></label><br />
                        <input type="checkbox" class="cb" name="cbShoppingCart" id="cbShoppingCart" value="1"<?php if ( '1' == $authorized_user['shopping_cart'] ) echo ' checked="checked"'; ?> /> <label for="cbShoppingCart"><?php echo _('Shopping Cart'); ?></label>
                    </td>
                </tr>
                <?php if ( $user['role'] >= 7 ) { ?>
                <tr>
                    <td><label for="sRole"><?php echo _('Role:'); ?></label></td>
                    <td>
                        <select name="sRole" id="sRole">
                             <option value="authorized-user"><?php echo _('Authorized User'); ?></option>
                             <option value="representative"><?php echo _('Representative'); ?></option>
                        </select>
                    </td>
                </tr>
	    		<?php } ?>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo ( $authorized_user_id ) ? _('Save') : _('Add'); ?>" /></td>
                </tr>
            </table>
			<?php nonce::field( 'add-edit-authorized-user' ); ?>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
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

$v = new Validator();
$v->form_name = 'fAddEditAuthorizedUser';
$v->add_validation( 'tEmail', 'req' , 'The email address is required' );
$v->add_validation( 'tEmail', 'val!=Enter email...' , 'The email address is required' );
$v->add_validation( 'tEmail' , 'email' , 'The email address must be a valid email' );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-authorized-user' ) ) {
	$errs = $v->validate();
	
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
			$success = $au->create( $_POST['tEmail'], $_POST['cbPages'], $_POST['cbProducts'], $_POST['cbAnalytics'], $_POST['cbBlog'], $_POST['cbEmailMarketing'], $_POST['cbShoppingCart'], $role );
		}
	}
	
	if ( !$errs && !$success ) 
		$errs = 'Could not create authorized user. This email may already exist for another user in the system.';
}

// Get the authorized user if necessary
if ( $authorized_user_id ) {
	$authorized_user = $au->get( $authorized_user_id );
} else {
	$authorized_user = array(
		'email' => ''
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
			<?php if ( !$authorized_user_id ) { ?>
                <p><input type="text" class="tb" name="tEmail" tmpval="<?php echo _('Enter email...'); ?>" value="<?php echo $authorized_user['email']; ?>" size="35" /></p>
    			<br />
            <?php } ?>
			<p><strong><?php echo _('Section permissions:'); ?></strong></p>
			<p>
				<input type="checkbox" class="cb" name="cbPages" id="cbPages" value="1"<?php if ( '1' == $authorized_user['pages'] ) echo ' checked="checked"'; ?> /> <label for="cbPages"><?php echo _('Pages'); ?></label><br />
				<input type="checkbox" class="cb" name="cbProducts" id="cbProducts" value="1"<?php if ( '1' == $authorized_user['products'] ) echo ' checked="checked"'; ?> /> <label for="cbProducts"><?php echo _('Products'); ?></label><br />
				<input type="checkbox" class="cb" name="cbAnalytics" id="cbAnalytics" value="1"<?php if ( '1' == $authorized_user['analytics'] ) echo ' checked="checked"'; ?> /> <label for="cbAnalytics"<?php echo _('>Analytics'); ?></label><br />
				<input type="checkbox" class="cb" name="cbBlog" id="cbBlog" value="1"<?php if ( '1' == $authorized_user['blog'] ) echo ' checked="checked"'; ?> /> <label for="cbBlog"><?php echo _('Blog'); ?></label><br />
				<input type="checkbox" class="cb" name="cbEmailMarketing" id="cbEmailMarketing" value="1"<?php if ( '1' == $authorized_user['email_marketing'] ) echo ' checked="checked"'; ?> /> <label for="cbEmailMarketing"><?php echo _('Email Marketing'); ?></label><br />
				<input type="checkbox" class="cb" name="cbShoppingCart" id="cbShoppingCart" value="1"<?php if ( '1' == $authorized_user['shopping_cart'] ) echo ' checked="checked"'; ?> /> <label for="cbShoppingCart"><?php echo _('Shopping Cart'); ?></label>
			</p>
			<?php nonce::field( 'add-edit-authorized-user' ); ?>
			<br />
			<?php if ( $user['role'] >= 7 ) { ?>
			<p><label for="sRole"><strong><?php echo _('Role:'); ?></strong></label></p>
			<p>
				<select name="sRole" id="sRole">
					 <option value="authorized-user"><?php echo _('Authorized User'); ?></option>
					 <option value="representative"><?php echo _('Representative'); ?></option>
				</select>
			</p>
			<br />
			<?php } ?>
			<input type="submit" class="button" value="<?php echo ( $authorized_user_id ) ? _('Update Authorized User') : _('Add Authorized User'); ?>" />
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
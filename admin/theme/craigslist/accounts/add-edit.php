<?php
/**
 * @page Craigslist Accounts Add/Edit
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate classes
$craigslist = new Craigslist;
$w = new Websites;
$v = new Validator();

$website_id = ( isset( $_GET['wid'] ) ) ? $_GET['wid'] : false;

// Add validation
$v = new Validator();
$v->form_name = 'fAddEditAccount';

if ( !$website_id )
    $v->add_validation( 'sWebsiteID', 'req', _('The "Account" field is required') );

$v->add_validation( 'sPlan', 'req', _('The "Plan" field is required') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-account' ) ) {
	// Server side validation
	$errs = $v->validate();

	if ( empty( $errs ) ) {
		if ( $website_id ) {
            // Update the plan
            $success = $w->update_settings( $website_id, array( 'craigslist-plan' => $_POST['sPlan'] ) );
        } else {
            // Create everything

            // Get the website
            $website = $w->get_website( $_POST['sWebsiteID'] );

            // Load the library
            library( 'craigslist-api' );

            // Create API object
            $craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );

            // Create the customer
            $customer_id = $craigslist_api->add_customer( $website['title'] );

            if ( $customer_id )
                $success = $w->update_settings( $website['website_id'], array( 'craigslist-customer-id' => $customer_id, 'craigslist-plan' => $_POST['sPlan'] ) );
        }
	}
}

// Get everything
if ( $website_id || $success ) {
	$account = ( !$website_id && $success ) ? $craigslist->get_account( $website['website_id'] ) : $craigslist->get_account( $website_id );
} else {
    // Need to get the accounts
    $accounts = $craigslist->get_unlinked_accounts();

    // We have no plan! Wherever will we go if we have no plan?!
	$plan = '';
}

javascript( 'validator' );

$selected = 'craigslist';
$sub_title = ( $website_id ) ? _('Edit Account') : _('Add Account');
$title = "$sub_title | " . _('Accounts') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'accounts' ); ?>
    
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $website_id ) ? _('Your account has been updated successfully!') : _('Your account has been added successfully!'); ?></p>
			<br />
            <p><?php echo _('Click here to'), ' <a href="/craigslist/accounts/" title="', _('Accounts'), '">', _('view your accounts'), '</a> or <a href="/craigslist/accounts/add-edit/" title="', _('Add Account'), '">', _('add another account'), '</a>.'; ?></p>
			<br />
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$website_id )
			$website_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p><br />";
        
        if ( !$website_id && !is_array( $accounts ) ) {
		?>
            <p><?php echo _('You have no accounts to add. Make sure the account you are trying to add has "Craigslist" checked off on the Accounts > Edit page.'); ?></p>
            <br /><br />
            <br /><br />
            <br /><br />
            <br /><br />
            <br /><br />
            <br /><br />
            <br /><br />
        <?php } else { ?>
		<form name="fAddEditAccount" id="fAddEditAccount" action="/craigslist/accounts/add-edit/<?php if ( $website_id ) echo "?wid=$website_id"; ?>" method="post">
		    <table cellpadding="0" cellspacing="0">
                <?php if ( !$website_id ) { ?>
                <tr>
                    <td><label for="sWebsiteID"><?php echo _('Account'); ?>:</label></td>
                    <td>
                        <select name="sWebsiteID" id="sWebsiteID">
                            <option value="">--<?php echo _('Select an Account'); ?>--</option>
                            <?php
                            $state = ( !$success && isset( $_POST['sWebsiteID'] ) ) ? $_POST['sWebsiteID'] : '';

                            if ( is_array( $accounts ) )
                            foreach ( $accounts as $a ) {
                            ?>
                                <option value="<?php echo $a['website_id']; ?>"><?php echo $a['title']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td><label><?php echo _('Account'); ?>:</label></td>
                    <td><?php echo $account['title']; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td><label for="sPlan"><?php echo _('Plan'); ?>:</label></td>
                    <td>
                        <select name="sPlan" id="sPlan">
                            <option value="">--<?php echo _('Select a Plan'); ?>--</option>
                            <?php
                            $plan = ( !$success && isset( $_POST['sPlan'] ) ) ? $_POST['sPlan'] : $account['plan'];

                            $plans = array(
                                '25 Ads/Day'
                                , '50 Ads/Day'
                                , '100 Ads/Day'
                            );

                            foreach ( $plans as $p ) {
                                $selected = ( $plan == $p ) ? ' selected="selected"' : '';
                            ?>
                                <option value="<?php echo $p; ?>"<?php echo $selected; ?>><?php echo $p; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo ( $website_id ) ? _('Update Account') : _('Add Account'); ?>" /></td>
                </tr>
		    </table>
            <?php nonce::field('add-edit-account'); ?>
		</form>
        <?php } ?>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>
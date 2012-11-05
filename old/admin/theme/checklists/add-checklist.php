<?php
/**
 * @page Checklists - Add
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate classes
$c = new Checklists();
$w = new Websites;
$v = new Validator();

// Add validation
$v = new Validator();
$v->form_name = 'fAddChecklist';

$v->add_validation( 'sWebsiteID', 'req', _('The "Website" field is required') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-checklist' ) ) {
	// Server side validation
	$errs = $v->validate();

	if ( empty( $errs ) )
        $success = $c->add_checklist( $_POST['sWebsiteID'] );
}

$accounts = $c->get_unchecklisted_accounts();

javascript( 'validator' );

$selected = 'checklists';
$sub_title = _('Add Checklist');
$title = "$sub_title | " . _('Checklists') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'checklists/', 'add-checklist' ); ?>
    
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your checklist has been added successfully!'); ?></p>
			<br />
            <p><?php echo _('Click here to'), ' <a href="/checklists/" title="', _('Checklists'), '">', _('view your checklists'), '</a> or <a href="/craigslist/accounts/add-edit/" title="', _('Add Account'), '">', _('add another account'), '</a>.'; ?></p>
			<br />
		</div>
		<?php 
		} else {
		
            if ( isset( $errs ) )
                echo "<p class='red'>$errs</p><br />";

            if ( !is_array( $accounts ) ) {
            ?>
                <p><?php echo _('You have no accounts to add. Make sure the account you are trying to add does not already have a checklist.'); ?></p>
                <br /><br />
                <br /><br />
                <br /><br />
                <br /><br />
                <br /><br />
                <br /><br />
                <br /><br />
            <?php } else { ?>
                <form name="fAddEditAccount" id="fAddEditAccount" action="/checklists/add-checklist/" method="post">
                    <table cellpadding="0" cellspacing="0">
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
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" class="button" value="<?php echo _('Add Checklist'); ?>" /></td>
                        </tr>
                    </table>
                    <?php nonce::field('add-checklist'); ?>
                </form>
            <?php
            }
        }
        ?>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>
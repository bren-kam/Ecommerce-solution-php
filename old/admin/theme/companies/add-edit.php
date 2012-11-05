<?php
/**
 * @page Companies - Add Edit
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

if ( 10 != $user['role'] )
    url::redirect('/');

$c = new Companies;
$v = new Validator;

// Get the Company ID
$company_id = ( isset( $_GET['cid'] ) ) ? $_GET['cid'] : false;

$v->form_name = 'fAddUser';
$v->add_validation( 'tName', 'req', _('The "Email" field is required') );
$v->add_validation( 'tDomain', 'URL', _('The "Domain" field must have an actual domain') );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-company' ) ) {
	$errs = $v->validate();

	if ( empty( $errs ) ) {
        if ( $company_id ) {
		    $success = $c->update( $company_id, $_POST['tName'], $_POST['tDomain'] );
        } else {
            $success = $c->create( $_POST['tName'], $_POST['tDomain'] );
        }
    }
}

// Get the email if necessary
if ( $company_id ) {
	$company = $c->get( $company_id );
} else {
	// Initialize variable
	$company = array(
		'name' => ''
		, 'domain' => ''
	);
}

javascript( 'validator' );

$sub_title = ( $company_id ) ? _('Edit Company') : _('Add Company');
$title = "$sub_title | " . _('Companies') . ' | ' . TITLE;
$page = 'accounts';
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'accounts/', 'companies' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $company_id ) ? _('Your company has been updated successfully!') : _('Your company has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/companies/" title="', _('Companies'), '">', _('view your companies'), '</a>.'; ?></p>
		</div>
		<?php
		}

		// Allow them to edit the entry they just created
		if ( $success && !$company_id )
			$company_id = $success;

		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
        <br /><br />
	    <form name="fAddEditCompanies" action="/companies/add-edit/<?php if ( $company_id ) echo "?cid=$company_id"; ?>" method="post">
			<?php nonce::field( 'add-edit-company' ); ?>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
					<td><input type="text" class="tb" name="tName" id="tName" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $company['name']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tDomain"><?php echo _('Domain'); ?>:</label></td>
					<td><input type="text" class="tb" name="tDomain" id="tDomain" maxlength="200" value="<?php echo ( !$success && isset( $_POST['tDomain'] ) ) ? $_POST['tDomain'] : $company['domain']; ?>" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo ( $company_id ) ? _('Update Company') : _('Add Company'); ?>" /></td>
				</tr>
			</table>
		</form>
        <br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
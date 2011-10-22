<?php
/**
 * @page Manage Website Emails
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// If their permissions are too low....
if ( $user['role'] < 9 )
	url::redirect( '/websites/' );

unset( $_SESSION['websites'] );

css( 'data-tables/TableTools', 'data-tables/ui', 'jquery.ui', 'websites/emails' );
javascript( 'jquery', 'jquery.ui', 'jquery.common', 'jquery.form', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard', 'data-tables/jquery.tableTools', 'websites/manage_emails' );

$selected = 'websites';
$title = _('Manage Website Emails') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Manage Website Emails'); ?></h1>
	<br clear="all" /><br />
	<?php $sidebar_emails = true; get_sidebar( 'websites/' ); ?>
    <div id="subcontent">
        <br />
    	<div>
			<a class="button" title="Create New" id="aCreateNew" href="#">Create New</a>
        </div>
        <br clear="left"/>
        <br/>
        <br/>
        <br/>
		<table cellpadding="0" cellspacing="0" width="100%" id="tListWebsites">
			<thead>
				<tr>
					<th width="35%"><?php echo _('Email Address'); ?></th>
					<th width="15%"><?php echo _('Usage / Quota' ); ?></th>
					<th width="50%"><?php echo _('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<div id="dChangeEmailAddress" class="hidden edit-email-window">
			<form name="fEditEmail" id="fEditEmail" action="" method="post">
				<input id="hEmailId" name="hEmailId" type="hidden" value="" />
				<?php nonce::field( 'edit-email', '_edit_email_nonce' ); ?>
				<table>
                    <tr><td><label for="iAddress">Email Address:</label></td><td><input class="email-info" name="iAddress" id="iAddress" type="text" value="" /><br/></td></tr>
                    <tr><td><label for="iPassword">Password:</label></td><td><input class="email-info" name="iPassword" id="iPassword" type="text" value="" /><br/></td><tr>
                    <tr><td><label for="iQuota">Quota:</label></td><td><input class="email-info" name="iQuota" id="iQuota" type="text" value="" /><br/></td></tr>
				</table>
                <br /><br />
                <div align="center"><input id="bSubmitEmailChanges" name="bSubmitEmailChanges" type="button" class="button" value="<?php echo _('Save Changes'); ?>" /></div>
            </form>
		</div>
		<div id="dCreateEmailAddress" class="hidden edit-email-window">
			<form name="fCreateEmail" id="fCreateEmail" action="" method="post">
				<?php nonce::field( 'create-email', '_create_email_nonce' ); ?>
				<table>
                    <tr><td><label for="iNewAddress">Email Address:</label></td><td><input class="email-info" name="iNewAddress" id="iNewAddress" type="text" value="" /><br/></td></tr>
                    <tr><td><br/></td><td></td></tr>
                    <tr><td><label for="iNewPassword1">Password:</label></td><td><input class="email-info" name="iNewPassword1" id="iNewPassword1" type="text" value="" /><br/></td><tr>
                    <tr><td><label for="iNewPassword2">Confirm Password:</label></td><td><input class="email-info" name="iNewPassword2" id="iNewPassword2" type="text" value="" /><br/></td><tr>
                    <tr><td><label for="iNewGenerate"></label></td><td><input class="email-info" name="bNewGeneratePW" id="bNewGeneratePW" type="button" value="Generate Password" /><br/></td><tr>
                    <tr><td><br/></td><td></td></tr>
                    <tr><td><label for="iNewQuota">Quota:</label></td><td><input class="email-info" name="iNewQuota" id="iNewQuota" type="text" value="" /><br/></td></tr>
				</table>
                <br /><br />
                <div align="center"><input id="bSubmitEmailChanges" name="bSubmitEmailChanges" type="button" class="button" value="<?php echo _('Save Changes'); ?>" /></div>
            </form>
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
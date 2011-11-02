<?php
/**
 * @page Edit Website
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;
$i = new Industries;
$v = new Validator;

$industries = $i->get_all();

$v->form_name = 'fEditWebsite';

$v->add_validation( 'tTitle', 'req', _('The "Website Title" field is required') );
$v->add_validation( 'tDomain', 'req', _('The "Website Domain" field is required') );
$v->add_validation( 'tTheme', 'req', _('The "Website Theme" field is required') );

$v->add_validation( 'tProducts', 'req', _('The "Products" field is required') );
$v->add_validation( 'tProducts', 'num', _('The "Products" field must contain a number') );

$v->add_validation( 'tType', 'req', _('The "Website Type" is required') );

$v->add_validation( 'tGAProfileID', 'num', _('The "Google Analytics Profile ID" field must contain a number') );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-website' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		// Start fields array
		$fields = array( 
			// Information
			'user_id' => $_POST['sUserID'],
			'os_user_id' => $_POST['sOSUserID'],
			'domain' => $_POST['tDomain'],
			'subdomain' => $_POST['tSubDomain'],
			'title' => stripslashes( $_POST['tTitle'] ),
			'theme' => $_POST['tTheme'],
			'phone' => $_POST['tPhone'],
			'products' => $_POST['tProducts'],
			'ga_profile_id' => $_POST['tGAProfileID'],
			'ga_tracking_key' => $_POST['tGATrackingKey'],
			// Features
			'pages' => $_POST['cbWebsite'], // Website/Page (same thing)
 			'product_catalog' => $_POST['cbProductCatalog'],
			'blog' => $_POST['cbBlog'],
			'email_marketing' => $_POST['cbEmailMarketing'],
			'shopping_cart' => $_POST['cbShoppingCart'],
			'seo' => $_POST['cbSEO'],
			'room_planner' => $_POST['cbRoomPlanner'],
			'craigslist' => $_POST['cbCraigslist'],
			'social_media' => $_POST['cbSocialMedia'],
			'domain_registration' => $_POST['cbDomainRegistration'],
			'additional_email_addresses' => $_POST['cbAdditionalEmail'],
			// Extras
			'mc_list_id' => $_POST['tMCListID'],
			'type' => $_POST['tType'],
			'live' => (isset( $_POST['cbLive'] ) ) ? 1 : 0
		);
		
		// Start DB safety preparation
		$fields_safety = 'iisssssiisiiiiiiiiiiissi';
		
		// FTP data
		if ( !empty( $_POST['tFTPHost'] ) ) {
			$fields['ftp_host'] = base64_encode( security::encrypt( $_POST['tFTPHost'], ENCRYPTION_KEY ) );
			$fields_safety .= 's';
		}
		
		if ( !empty( $_POST['tFTPUser'] ) ) {
			$fields['ftp_username'] = base64_encode( security::encrypt( $_POST['tFTPUser'], ENCRYPTION_KEY ) );
			$fields_safety .= 's';
		}
		
		if ( !empty( $_POST['tFTPPassword'] ) ) {
			$fields['ftp_password'] = base64_encode( security::encrypt( $_POST['tFTPPassword'], ENCRYPTION_KEY ) );
			$fields_safety .= 's';
		}
		
		// Industries
		$w->remove_industries( $_GET['wid'] );
		
		foreach ( $industries as $industry ) {
			if ( isset( $_POST['cbIndustry' . $industry['industry_id']] ) )
				$industry_ids[] = $industry['industry_id'];
		}
		
		$w->add_industries( $industry_ids, $_GET['wid'] );
			
		// Extras
		if ( !empty( $_POST['tWordPressUsername'] ) ) {
			$fields['wordpress_username'] = base64_encode( security::encrypt( $_POST['tWordPressUsername'], ENCRYPTION_KEY ) );
			$fields_safety .= 's';
		}

		if ( !empty( $_POST['tWordPressPassword'] ) ) {
			$fields['wordpress_password'] = base64_encode( security::encrypt( $_POST['tWordPressPassword'], ENCRYPTION_KEY ) );
			$fields_safety .= 's';
		}
		
		if ( isset( $_POST['cbCustomImageSize'] ) ) {
			$size = ( 500 > (int) $_POST['tCustomImageSize'] ) ? 500 : (int) $_POST['tCustomImageSize'];
			$w->update_settings( $_GET['wid'], array( 'custom-image-size' => $size ) );
		} else {
			$w->delete_settings( $_GET['wid'], array( 'custom-image-size' ) );
		}
		
		$success = $w->update( $_GET['wid'], $fields, $fields_safety );
		
		// Update Facebook settings
		$w->update_settings( $_GET['wid'], array( 
			//'facebook-username' => base64_encode( security::encrypt( $_POST['tFacebookUsername'], ENCRYPTION_KEY ) )
			//, 'facebook-password' => base64_encode( security::encrypt( $_POST['tFacebookPassword'], ENCRYPTION_KEY ) )
			'facebook-url' => $_POST['tFacebookURL']
			, 'limited-products' => ( isset( $_POST['cbLimitedProducts'] ) ) ? 1 : 0
            , 'advertising-url' => $_POST['tAdvertisingURL']
		) );
	}
}

$web = $w->get_website( $_GET['wid'] );
$ftp = $w->get_ftp_data( $_GET['wid'] );
$website_industries = $w->get_industries( $_GET['wid'] );
$users = $u->get_users();

$settings = $w->get_settings( $_GET['wid'], array( 'limited-products', 'custom-image-size', 'facebook-url', 'advertising-url' ) );
$web['custom_image_size'] = $settings['custom-image-size'];

// We must strip slashes, since $_POST automatically inserts them!
foreach ( $web as &$slot ){
	$slot = stripslashes( $slot );
}

css( 'form', 'websites/edit' );
javascript( 'validator', 'jquery', 'websites/edit' );

$selected = 'websites';
$title = _('Edit Website') . ' | ' . TITLE;
get_header();
?>

<div id="content">
    <h1><?php echo _('Edit Website'); ?></h1>
	<br clear="all" /><br />
	<?php $sidebar_emails = true; get_sidebar( 'websites/' ); ?>
	<div id="subcontent">
		<?php 
		if ( !isset( $success ) || !$success ) {
			$main_form_class = '';
			$success_class = ' class="hidden"';
			
			if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		} else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php
			if ( isset( $errs ) && !empty( $errs ) ) {
				$error_message = '';
				
				foreach ( $errs as $e ) {
					$error_message .= ( !empty( $error_message ) ) ? "<br />$e" : $e;
				}
				
				echo "<p class='red'>$error_message</p>";
			}
			?>
			
			<form action="/websites/edit/?wid=<?php echo $_GET['wid']; ?>" method="post" name="fEditWebsite">
			<?php 
			if ( '0' == $web['version'] )
				echo '<p>', _('Website has not been installed. Please verify domain and FTP data below and'), ' <a href="/websites/install/?wid=', $web['website_id'], '" title="', _('Install Website'), '">', _('click here to install the website'), '</a>.</p>';
			
			if ( isset( $_GET['i'] ) )
				echo ( '1' == $_GET['i'] ) ? '<p>' . _('The website was successfully installed!') . '</p>' : '<p>' . _('An error occurred while trying to install the website. Please check the error log for details.') . '</p>';
			?>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" class="block-labels">
						<h2><?php echo _('Information'); ?></h2>
						<p>
							<label for="tTitle"><?php echo _('Title'); ?>:</label>
							<input type="text" name="tTitle" id="tTitle" value="<?php echo $web['title']; ?>" class="tb" />
						</p>
						<p>
							<label for="tDomain"><?php echo _('Domain'); ?>:</label>
							<input type="text" name="tDomain" id="tDomain" value="<?php echo $web['domain']; ?>" class="tb" />
						</p>
						<p>
							<label for="tSubDomain"><?php echo _('Sub Domain'); ?>:</label>
							<input type="text" name="tSubDomain" id="tSubDomain" value="<?php echo $web['subdomain']; ?>" class="tb" />
						</p>
						<p>
						<p>
							<label for="tTheme"><?php echo _('Theme'); ?>:</label>
							<input type="text" name="tTheme" id="tTheme" value="<?php echo $web['theme']; ?>" class="tb" />
						</p>
						<p>
							<label for="tPhone"><?php echo _('Phone'); ?>:</label>
							<input type="text" name="tPhone" id="tPhone" value="<?php echo $web['phone']; ?>" class="tb" />
						</p>
						<p>
							<label for="tProducts"><?php echo _('Products'); ?>:</label>
							<input type="text" name="tProducts" id="tProducts" value="<?php echo $web['products']; ?>" class="tb" />
						</p>
						<p>
							<label for="sUserID"><?php echo _('Owner'); ?>:</label>
							<select name="sUserID" id="sUserID">
								<option value="">-- <?php echo _('Select a User'); ?> --</option>
								<?php 
								$user_email = '';
								foreach ( $users as $u ) { 
									// We don't want any empty users
									if ( '' == $u['contact_name'] )
										continue;
									
									$selected = ( $web['user_id'] == $u['user_id'] ) ? ' selected="selected"' : '';
									$email = ( $u['email'] ) ? 'email="' . $u['email'] . '"' : '';
									if ( $web['user_id'] == $u['user_id'] ) $user_email = $u['email'];
								?>
								<option value="<?php echo $u['user_id']; ?>"<?php echo $selected . $email; ?>><?php echo $u['contact_name']; ?></option>
								<?php } ?>
							</select>
						</p>

						<p>
                        	<label for="tUserEmail"><?php echo _('Email'); ?>:</label>
                            <input type="text" class="tb read-only" name="tUserEmail" id="tUserEmail" readonly="readonly" value="<?php echo $user_email; ?>" />
                        <p>
							<label for="sOSUserID"><?php echo _('Online Specialist'); ?>:</label>
							<select name="sOSUserID" id="sOSUserID" class="tb">
								<option value="">-- <?php echo _('Select a User'); ?> --</option>
								<?php 
								foreach ( $users as $u ) { 
									// We don't want any empty users
									if ( '' == $u['contact_name'] || $u['role'] < 7 )
										continue;
									
									$selected = ( $web['os_user_id'] == $u['user_id'] ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $u['user_id']; ?>"<?php echo $selected; ?>><?php echo $u['contact_name']; ?></option>
								<?php } ?>
							</select>
						</p>
					</td>
					<td valign="top">
						<h2><?php echo _('Features'); ?></h2>
						<p><input type="checkbox" name="cbWebsite" id="cbWebsite" value="1" class="cb"<?php if ( $web['pages'] ) echo ' checked="checked"'; ?> /> <label for="cbWebsite"><?php echo _('Website'); ?></label></p>
						<p><input type="checkbox" name="cbProductCatalog" id="cbProductCatalog" value="1" class="cb"<?php if ( $web['product_catalog'] ) echo ' checked="checked"'; ?> /> <label for="cbProductCatalog"><?php echo _('Product Catalog'); ?></label></p>
						<p><input type="checkbox" name="cbLimitedProducts" id="cbLimitedProducts" value="1" class="cb"<?php if ( '1' == $settings['limited-products'] ) echo ' checked="checked"'; ?> /> <label for="cbLimitedProducts"><?php echo _('Limited Products'); ?></label></p>
						<p><input type="checkbox" name="cbBlog" id="cbBlog" value="1" class="cb"<?php if ( $web['blog'] ) echo ' checked="checked"'; ?> /> <label for="cbBlog"><?php echo _('Blog'); ?></label></p>
						<p><input type="checkbox" name="cbEmailMarketing" id="cbEmailMarketing" value="1" class="cb"<?php if ( $web['email_marketing'] ) echo ' checked="checked"'; ?> /> <label for="cbEmailMarketing"><?php echo _('Email Marketing'); ?></label></p>
						<p><input type="checkbox" name="cbShoppingCart" id="cbShoppingCart" value="1" class="cb"<?php if ( $web['shopping_cart'] ) echo ' checked="checked"'; ?> /> <label for="cbShoppingCart"><?php echo _('Shopping Cart'); ?></label></p>
						<p><input type="checkbox" name="cbSEO" id="cbSEO" value="1" class="cb"<?php if ( $web['seo'] ) echo 'checked="checked"'; ?> /> <label for="cbSEO"><?php echo _('SEO'); ?></label></p>
						<p><input type="checkbox" name="cbRoomPlanner" id="cbRoomPlanner" value="1" class="cb"<?php if ( $web['room_planner'] ) echo ' checked="checked"'; ?> /> <label for="cbRoomPlanner"><?php echo _('Room Planner'); ?></label></p>
						<p><input type="checkbox" name="cbCraigslist" id="cbCraigslist" value="1" class="cb"<?php if ( $web['craigslist'] ) echo ' checked="checked"'; ?> /> <label for="cbCraigslist"><?php echo _('Craigslist'); ?></label></p>
						<p><input type="checkbox" name="cbSocialMedia" id="cbSocialMedia" value="1" class="cb"<?php if ( $web['social_media'] ) echo ' checked="checked"'; ?> /> <label for="cbSocialMedia"><?php echo _('Social Media'); ?></label></p>
						<p><input type="checkbox" name="cbDomainRegistration" id="cbDomainRegistration" value="1" class="cb"<?php if ( $web['domain_registration'] ) echo ' checked="checked"'; ?> /> <label for="cbDomainRegistration"><?php echo _('Domain Registration'); ?></label></p>
						<p><input type="checkbox" name="cbAdditionalEmail" id="cbAdditionalEmail" value="1" class="cb"<?php if ( $web['additional_email_addresses'] ) echo ' checked="checked"'; ?> /> <label for="cbAdditionalEmail"><?php echo _('Additional Email Addresses'); ?></label></p>
					</td>
					<td valign="top">
						<h2><?php echo _('FTP'); ?></h2>
						<div class="block-labels">
							<p>
								<label for="tFTPHost"><?php echo _('Host'); ?>:</label>
								<input type="text" name="tFTPHost" id="tFTPHost" value="<?php echo security::decrypt( base64_decode( $ftp['ftp_host'] ), ENCRYPTION_KEY ); ?>" class="tb" />
							</p>
							<p>
								<label for="tFTPUser"><?php echo _('User Name'); ?>:</label>
								<input type="text" name="tFTPUser" id="tFTPUser" value="<?php echo security::decrypt( base64_decode( $ftp['ftp_username'] ), ENCRYPTION_KEY ); ?>" class="tb" />
							</p>
							<p>
								<label for="tFTPPassword"><?php echo _('Password'); ?>:</label>
								<input type="password" name="tFTPPassword" id="tFTPPassword" value="<?php echo security::decrypt( base64_decode( $ftp['ftp_password'] ), ENCRYPTION_KEY ); ?>" class="tb" />
							</p>
						</div>
						<br />
						
						<h2><?php echo _('Industries'); ?></h2>
						<?php 
						foreach ( $industries as $i ) { 
							$checked = ( in_array( $i['industry_id'], $website_industries ) ) ? ' checked="checked"' : '';
						?>
						<p><input type="checkbox" name="cbIndustry<?php echo $i['industry_id']; ?>" id="cbIndustry<?php echo $i['industry_id']; ?>" value="" class="cb"<?php echo $checked; ?>/> <label for="cbIndustry<?php echo $i['industry_id']; ?>"><?php echo ucwords( $i['name'] ); ?></label></p>
						<?php } ?>
					</td>
					<td valign="top" class="block-labels">
						<h2><?php echo _('Extras'); ?></h2>
						<p>
							<label for="tType"><?php echo _('Type'); ?>:</label>
							<input type="text" name="tType" id="tType" value="<?php echo $web['type']; ?>" class="tb" />
						</p>
						<p>
							<?php echo _('Logo'); ?>:
							<strong><?php echo $web['logo']; ?></strong>
						</p>
						<p>
							<label for="tGAProfileID"><?php echo _('Google Analytics Profile ID'); ?>:</label>
							<input type="text" name="tGAProfileID" id="tGAProfileID" value="<?php echo $web['ga_profile_id']; ?>" class="tb" />
						</p>
						<p>
							<label for="tGATrackingKey"><?php echo _('Google Analytics Tracking Key'); ?>:</label>
							<input type="text" name="tGATrackingKey" id="tGATrackingKey" value="<?php echo $web['ga_tracking_key']; ?>" class="tb" />
						</p>
						<p>
                            <label for="tWordPressUsername"><?php echo _('WordPress Username'); ?>:</label>
							<input type="text" name="tWordPressUsername" id="tWordPressUsername" value="<?php if ( !empty( $web['wordpress_username'] ) ) echo security::decrypt( base64_decode( $web['wordpress_username'] ), ENCRYPTION_KEY ); ?>" class="tb" />
						</p>
						<p>
							<label for="tWordPressPassword"><?php echo _('WordPress Password'); ?>:</label>
							<input type="text" name="tWordPressPassword" id="tWordPressPassword" value="<?php if ( !empty( $web['wordpress_password'] ) ) echo security::decrypt( base64_decode( $web['wordpress_password'] ), ENCRYPTION_KEY ); ?>" class="tb" />
						</p>
						<p>
							<label for="tFacebookURL"><?php echo _('Facebook Page Insights URL'); ?>:</label>
							<input type="text" name="tFacebookURL" id="tFacebookURL" value="<?php if ( !is_array( $settings['facebook-url'] ) ) echo $settings['facebook-url']; ?>" class="tb" />
						</p>
						<p>
							<label for="tAdvertisingURL"><?php echo _('Advertising URL'); ?>:</label>
							<input type="text" name="tAdvertisingURL" id="tAdvertisingURL" value="<?php if ( !is_array( $settings['advertising-url'] ) ) echo $settings['advertising-url']; ?>" class="tb" />
						</p>
						<p>
							<label for="tMCListID"><?php echo _('MailChimp List ID'); ?>:</label>
							<input type="text" name="tMCListID" id="tMCListID" value="<?php echo $web['mc_list_id']; ?>" class="tb" />
						</p>
						<p>
                        	<input type="checkbox" name="cbCustomImageSize" id="cbCustomImageSize" value="" class="cb"<?php if ( isset( $web['custom_image_size'] ) && $web['custom_image_size'] != 0 ) echo ' checked="checked"'; ?>/> 
                            <label for="cbLive" class="inline"><?php echo _('Max image size for custom products:'); ?></label>&nbsp;
                            <input type="text" name="tCustomImageSize" id="tCustomImageSize" style="width:50px !important;" value="<?php echo ( isset( $web['custom_image_size'] ) ) ? $web['custom_image_size'] : ''; ?>" class="tb" />
						</p>
                        <p><input type="checkbox" name="cbLive" id="cbLive" value="" class="cb"<?php if ( $web['live'] ) echo ' checked="checked"'; ?>/> <label for="cbLive" class="inline"><?php echo _('Live'); ?></label></p>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<br /><br />
			<br />
			<input type="submit" id="bSubmit" name="bSubmit" value="<?php echo _('Update Website Information'); ?>" class="button" />
			<?php nonce::field( 'update-website' ); ?>
		</form>
		<?php add_footer( $v->js_validation() ); ?>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('Website has been successfully updated!'); ?></p>
			<p><?php echo _('Click here to <a href="/websites/" title="View Websites">view all websites</a> or <a href="#" id="aContinueEditingWebsite" title="Continue Editing Website">continue editing website</a>.'); ?></p>
			<br /><br />
			<br /><br />
			<br /><br />
			<br /><br />
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
